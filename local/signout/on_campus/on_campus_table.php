<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * On-campus signout table for Middlesex School's eSignout Subplugin.
 *
 * @package    local_signout
 * @subpackage on_campus
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../mxschool/classes/mx_table.php');
require_once(__DIR__.'/../classes/output/renderable.php');

class on_campus_table extends local_mxschool_table {

    /** @var bool Whether the user is a student and only their records should be displayed. */
    private $isstudent;

    /**
     * Creates a new on_campus_table.
     *
     * @param stdClass $filter any filtering for the table - could include dorm, location, date, and search.
     * @param bool $isstudent Whether the user is a student and only their records should be displayed.
     */
    public function __construct($filter, $isstudent) {
        global $USER;
        $this->isstudent = $isstudent;
        $columns = array('student', 'grade', 'dorm', 'location', 'signoutdate', 'signouttime', 'confirmation', 'signin');
        if ($filter->dorm) {
            unset($columns[array_search('dorm', $columns)]);
        }
        if ($filter->location > 0) {
            unset($columns[array_search('location', $columns)]);
        }
        if ($filter->date) {
            unset($columns[array_search('signoutdate', $columns)]);
        }
        if ($isstudent) {
            unset($columns[array_search('confirmation', $columns)]);
        }
        $headers = array_map(function($column) {
            return get_string("on_campus_report_header_{$column}", 'local_signout');
        }, $columns);
        $columns[] = 'actions';
        $headers[] = get_string('report_header_actions', 'local_mxschool');
        $fields = array(
            'oc.id', 'oc.userid', "CONCAT(u.lastname, ', ', u.firstname) AS student", 's.grade', 'd.name AS dorm',
            'l.name AS location', 'oc.other', 'oc.time_created AS signoutdate', 'oc.time_created AS signouttime',
            'oc.confirmerid AS confirmer', 'oc.confirmation_time AS confirmationtime', 'oc.sign_in_time AS signin'
        );
        $from = array(
            '{local_signout_on_campus} oc', '{user} u ON oc.userid = u.id', '{local_mxschool_student} s ON s.userid = u.id',
            '{local_mxschool_dorm} d ON s.dormid = d.id', '{local_signout_location} l ON oc.locationid = l.id',
            '{user} c ON oc.confirmerid = c.id'
        );
        $starttime = generate_datetime('midnight')->getTimestamp();
        $where = array(
            'oc.deleted = 0', 'u.deleted = 0', $filter->dorm ? "s.dormid = {$filter->dorm}" : '',
            '(oc.locationid = -1 OR l.deleted = 0)', '(oc.confirmerid IS NULL OR c.deleted = 0)',
            $filter->location ? "oc.locationid = {$filter->location}" : '', $isstudent ? "oc.userid = {$USER->id}" : '',
            $isstudent ? "oc.time_created >= {$starttime}" : ''
        );
        if ($filter->date) {
            $starttime = generate_datetime($filter->date);
            $endtime = clone $starttime;
            $endtime->modify('+1 day');
            $where[] = "oc.time_created >= {$starttime->getTimestamp()}";
            $where[] = "oc.time_created < {$endtime->getTimestamp()}";
        }
        $sortable = array('student', 'grade', 'dorm', 'location', $filter->date ? 'signouttime' : 'signoutdate');
        $urlparams = array(
            'dorm' => $filter->dorm, 'location' => $filter->location, 'date' => $filter->date, 'search' => $filter->search
        );
        $centered = array('grade', 'signoutdate', 'signouttime', 'confirmation', 'signin');
        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename', 'l.name', 'oc.other', 'c.firstname', 'c.lastname');
        parent::__construct(
            'on_campus_table', $columns, $headers, $sortable, $filter->date ? 'signouttime' : 'signoutdate', $fields, $from, $where,
            $urlparams, $centered, $filter->search, $searchable, array(), false
        );
        $this->column_class('signin', "{$this->column_class['signin']} sign-in");
    }

    /**
     * Formats the student column to "last, first (preferred)" or "last, first".
     */
    protected function col_student($values) {
        return format_student_name($values->userid);
    }

    /**
     * Formats the location column.
     */
    protected function col_location($values) {
        return $values->other ?? $values->location;
    }

    /**
     * Formats the sign out date column to 'n/j/y'.
     */
    protected function col_signoutdate($values) {
        return format_date('n/j/y', $values->signoutdate);
    }

    /**
     * Formats the sign out time time column to 'g:i A'.
     */
    protected function col_signouttime($values) {
        return format_date('g:i A', $values->signouttime);
    }

    /**
     * Formats the confirmation column.
     */
    protected function col_confirmation($values) {
        if (!isset($values->confirmationtime)) {
            return '-';
        }
        return get_string('on_campus_report_column_confirmation_text', 'local_signout', array(
            'confirmer' => format_faculty_name($values->confirmer),
            'confirmationtime' => format_date('g:i A', $values->confirmationtime),
            'confirmationdate' => format_date('n/j/y', $values->confirmationtime)
        ));
    }

    /**
     * Formats the sign-in time column to 'g:i A'.
     */
    protected function col_signin($values) {
        if (!isset($values->signin)) {
            return '-';
        }
        return format_date('n/j/y', $values->signoutdate) === format_date('n/j/y', $values->signin)
            ? format_date('g:i A', $values->signin) : format_date('n/j/y g:i A', $values->signin);
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        global $PAGE;
        if (!$this->isstudent) {
            return $this->edit_icon('/local/signout/on_campus/on_campus_enter.php', $values->id)
                . $this->delete_icon($values->id);
        }
        if ($values->signin) {
            return '&#x2705;';
        }
        $output = $PAGE->get_renderer('local_signout');
        $renderable = new \local_signout\output\signin_button($values->id, 'local_signout_on_campus');
        if (
            !get_config('local_signout', 'on_campus_form_ipenabled')
            || $_SERVER['REMOTE_ADDR'] === get_config('local_signout', 'school_ip')
        ) {
            return $output->render($renderable);
        }
        return '-';
    }

}
