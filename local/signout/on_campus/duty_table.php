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
 * On-campus duty table for Middlesex School's eSignout Subplugin.
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

class duty_table extends local_mxschool_table {

    /**
     * Creates a new duty_table.
     *
     * @param stdClass $filter any filtering for the table - could include pictures, location and search.
     */
    public function __construct($filter) {
        $columns = array('student', 'picture', 'grade', 'dorm', 'advisor', 'location', 'signouttime', 'confirmation');
        if (!$filter->pictures) {
            unset($columns[array_search('picture', $columns)]);
        }
        if ($filter->location > 0) {
            unset($columns[array_search('location', $columns)]);
        }
        $headers = array_map(function($column) {
            return get_string("duty_report_header_{$column}", 'local_signout');
        }, $columns);
        $columns[] = 'actions';
        $headers[] = get_string('report_header_actions', 'local_mxschool');
        $fields = array(
            'oc.id', "CONCAT(u.lastname, ', ', u.firstname) AS student", 'u.firstname', 'u.alternatename', 's.grade',
            'd.name AS dorm', "CONCAT(a.lastname, ', ', a.firstname) AS advisor", 'l.name AS location', 'oc.other',
            'oc.time_created AS signouttime', "CONCAT(c.lastname, ', ', c.firstname) AS confirmer",
            'oc.confirmation_time AS confirmationtime'
        );
        $from = array(
            '{local_signout_on_campus} oc', '{user} u ON oc.userid = u.id', '{local_mxschool_student} s ON s.userid = u.id',
            '{local_mxschool_dorm} d ON s.dormid = d.id', '{user} a ON s.advisorid = a.id',
            '{local_signout_location} l ON oc.locationid = l.id', '{user} c ON oc.confirmerid = c.id'
        );
        $starttime = generate_datetime('midnight')->getTimestamp();
        $where = array(
            'oc.deleted = 0', 'u.deleted = 0', '(oc.locationid = -1 OR l.deleted = 0)',
            '(oc.confirmerid IS NULL OR c.deleted = 0)', $filter->location ? "oc.locationid = {$filter->location}" : '',
            "oc.time_created >= {$starttime}"
        );
        $sortable = array('student', 'grade', 'dorm', 'location', 'signouttime');
        $urlparams = array('pictures' => $filter->pictures, 'location' => $filter->location, 'search' => $filter->search);
        $centered = array('picture', 'grade', 'signouttime', 'confirmation');
        $searchable = array(
            'u.firstname', 'u.lastname', 'u.alternatename', 'd.name', 'a.firstname', 'a.lastname', 'l.name', 'oc.other',
            'c.firstname', 'c.lastname'
        );
        parent::__construct(
            'on_campus_table', $columns, $headers, $sortable, 'signouttime', $fields, $from, $where, $urlparams, $centered,
            $filter->search, $searchable, array(), false
        );
        $this->column_class('confirmation', "{$this->column_class['confirmation']} confirmation");
    }

    /**
     * Formats the picture column.
     */
    protected function col_picture($values) {
        return get_string('duty_report_column_picture_notfound', 'local_signout');
    }

    /**
     * Formats the location column.
     */
    protected function col_location($values) {
        return $values->other ?? $values->location;
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
        return isset($values->confirmationtime) ? get_string('duty_report_column_confirmation_text', 'local_signout', array(
            'confirmer' => $values->confirmer, 'confirmationtime' => format_date('g:i A', $values->confirmationtime)
        )) : '-';
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        global $PAGE;
        if ($values->confirmationtime) {
            return '&#x2705;';
        }
        $output = $PAGE->get_renderer('local_signout');
        $renderable = new \local_signout\output\confirmation_button($values->id);
        return $output->render($renderable);
    }

}
