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
 * Tutoring Table for Middlesex School's Peer Tutoring Subplugin.
 *
 * @package    local_peertutoring
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../mxschool/classes/mx_table.php');

class tutoring_table extends local_mxschool_table {

    /**
     * Creates a new tutoring_table.
     *
     * @param stdClass $filter Any filtering for the table - may include tutor, department, type, date, search.
     * @param string $download Indicates whether the table is downloading.
     * @param bool $email Indicates whether the table should be generated in an email-compatible form.
     */
    public function __construct($filter, $download, $email = false) {
        if (!$email) {
            $this->is_downloading($download, 'Peer Tutoring Records', 'Peer Tutoring Record');
        }
        $columns = array('tutor', 'tutoringdate', 'student', 'department', 'course', 'topic', 'type', 'rating', 'notes');
        if ($filter->tutor) {
            unset($columns[array_search('tutor', $columns)]);
        }
        if ($filter->department) {
            unset($columns[array_search('department', $columns)]);
        }
        if ($filter->type) {
            unset($columns[array_search('type', $columns)]);
        }
        $headers = array_map(function($column) {
            return get_string("tutoring_report_header_{$column}", 'local_peertutoring');
        }, $columns);
        if (!$email && !$this->is_downloading()) {
            $columns[] = 'actions';
            $headers[] = get_string('report_header_actions', 'local_mxschool');
        }
        $fields = array(
            's.id', "CONCAT(tu.lastname, ', ', tu.firstname) AS tutor", 'tu.firstname AS tfirstname',
            'tu.alternatename AS talternatename', "CONCAT(su.lastname, ', ', su.firstname) AS student", 'su.firstname',
            'su.alternatename', 's.tutoring_date AS tutoringdate', 'd.name AS department', 'c.name AS course', 's.topic',
            'ty.displaytext AS type', 's.other', 'r.displaytext AS rating', 's.notes'
        );
        $from = array(
            '{local_peertutoring_session} s', '{user} tu ON s.tutorid = tu.id', '{user} su ON s.studentid = su.id',
            '{local_peertutoring_tutor} t ON s.tutorid = t.userid', '{local_peertutoring_course} c ON s.courseid = c.id',
            '{local_peertutoring_dept} d ON c.departmentid = d.id', '{local_peertutoring_type} ty ON s.typeid = ty.id',
            '{local_peertutoring_rating} r ON s.ratingid = r.id'
        );
        if ($filter->date) {
            $starttime = generate_datetime($filter->date);
            $endtime = clone $starttime;
            $endtime->modify('+1 day');
        }
        $where = array(
            's.deleted = 0', 'tu.deleted = 0', 'su.deleted = 0', 't.deleted = 0', $filter->tutor ? "tu.id = {$filter->tutor}" : '',
            $filter->department ? "d.id = {$filter->department}" : '', $filter->type ? "ty.id = {$filter->type}" : '',
            $filter->date ? "s.tutoring_date >= {$starttime->getTimestamp()}" : '',
            $filter->date ? "s.tutoring_date < {$endtime->getTimestamp()}" : ''
        );
        $sortable = $email ? array() : array('tutor', 'tutoringdate', 'student', 'department', 'course', 'type', 'rating');
        $urlparams = array(
            'tutor' => $filter->tutor, 'department' => $filter->department, 'type' => $filter->type, 'date' => $filter->date,
            'search' => $filter->search
        );
        $centered = array('tutoringdate', 'department', 'course');
        $searchable = array(
            'tu.lastname', 'tu.firstname', 'tu.alternatename', 'su.lastname', 'su.firstname', 'su.alternatename', 'd.name',
            'c.name', 's.topic', 'ty.displaytext', 's.other', 'r.displaytext'
        );
        parent::__construct(
            'tutoring_table', $columns, $headers, $sortable, 'tutoringdate', $fields, $from, $where, $urlparams, $centered,
            $filter->search, $searchable, array(), false
        );
    }

    /**
     * Formats the tutor column to "last, first (alternate)" or "last, first".
     */
    protected function col_tutor($values) {
        return $values->tutor . (
            $values->talternatename && $values->talternatename !== $values->tfirstname ? " ($values->talternatename)" : ''
        );
    }

    /**
     * Formats the tutoring date column to 'n/j/y'.
     */
    protected function col_tutoringdate($values) {
        return format_date('n/j/y', $values->tutoringdate);
    }

    /**
     * Formats the type column.
     */
    protected function col_type($values) {
        return $values->other ?? $values->type;
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return $this->edit_icon('/local/peertutoring/tutoring_enter.php', $values->id) . $this->delete_icon($values->id);
    }

}
