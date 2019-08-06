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
 * On-campus duty table for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @subpackage  on_campus
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_signout\local\on_campus;

defined('MOODLE_INTERNAL') || die();

use local_mxschool\output\student_picture;
use local_signout\output\confirmation_button;

class duty_table extends \local_mxschool\table {

    /**
     * Creates a new duty_table.
     *
     * @param stdClass $filter Any filtering for the table - could include properties active, pictures, location, and search.
     */
    public function __construct($filter) {
        $columns = array('student', 'picture', 'grade', 'dorm', 'advisor', 'location', 'signouttime', 'confirmation');
        if (!$filter->pictures) {
            unset($columns[array_search('picture', $columns)]);
        }
        if ($filter->location > 0) {
            unset($columns[array_search('location', $columns)]);
        }
        $headers = $this->generate_headers($columns, 'duty_report', 'local_signout');
        $sortable = array('signouttime', 'student', 'grade', 'dorm', 'location');
        $centered = array('picture', 'grade', 'signouttime', 'confirmation');
        parent::__construct('on_campus_table', $columns, $headers, $sortable, $centered, $filter, true, false);
        $this->add_column_class('confirmation', 'confirmation');

        $fields = array(
            'oc.id', 'oc.userid', "CONCAT(u.lastname, ', ', u.firstname) AS student", 's.grade', 's.dormid', 'd.name AS dorm',
            "CONCAT(a.lastname, ', ', a.firstname) AS advisor", 'l.name AS location', 'oc.other',
            'oc.time_created AS signouttime', 'oc.confirmerid AS confirmer', 'oc.confirmation_time AS confirmationtime'
        );
        $from = array(
            '{local_signout_on_campus} oc', '{user} u ON oc.userid = u.id', '{local_mxschool_student} s ON s.userid = u.id',
            '{local_mxschool_dorm} d ON s.dormid = d.id', '{user} a ON s.advisorid = a.id',
            '{local_signout_location} l ON oc.locationid = l.id', '{user} c ON oc.confirmerid = c.id'
        );
        $starttime = generate_datetime('midnight')->getTimestamp();
        $where = array(
            'oc.deleted = 0', 'u.deleted = 0', '(oc.locationid = -1 OR l.deleted = 0)',
             "oc.time_created >= {$starttime}"
        );
        if ($filter->active) {
            $where[] = 'oc.sign_in_time IS NULL';
        }
        if ($filter->location) {
            $where[] = "oc.locationid = {$filter->location}";
        }
        $searchable = array(
            'u.firstname', 'u.lastname', 'u.alternatename', 'd.name', 'a.firstname', 'a.lastname', 'l.name', 'oc.other',
            'c.firstname', 'c.lastname'
        );
        $this->define_sql($fields, $from, $where, $searchable, $filter->search);
    }

    /**
     * Formats the picture column.
     */
    protected function col_picture($values) {
        global $DB, $PAGE;
        $filename = $DB->get_field('local_mxschool_student', 'picture_filename', array('userid' => $values->userid));
        if (!$filename) {
            return get_string('duty_report_column_picture_notfound', 'local_signout');
        }
        $url = \moodle_url::make_pluginfile_url(1, 'local_mxschool', 'student_pictures', 0, '/', $filename, false);
        $output = $PAGE->get_renderer('local_mxschool');
        $renderable = new student_picture($url, format_student_name($values->userid));
        return $output->render($renderable);
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
        if (!isset($values->confirmationtime)) {
            return '-';
        }
        return get_string('duty_report_column_confirmation_text', 'local_signout', array(
            'confirmer' => format_faculty_name($values->confirmer),
            'confirmationtime' => format_date('g:i A', $values->confirmationtime)
        ));
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
        $renderable = new confirmation_button($values->id);
        return $output->render($renderable);
    }

}
