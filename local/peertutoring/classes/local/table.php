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
 * Tutoring Table for Middlesex's Peer Tutoring Subplugin.
 *
 * @package     local_peertutoring
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_peertutoring\local;

defined('MOODLE_INTERNAL') || die();

class table extends \local_mxschool\table {

    /**
     * Creates a new tutoring_table.
     *
     * @param stdClass $filter Any filtering for the table - could include properties tutor, department, type, date, and search.
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
        if ($filter->type > 0) {
            unset($columns[array_search('type', $columns)]);
        }
        $headers = $this->generate_headers($columns, 'report', 'local_peertutoring');
        $sortable = array('tutoringdate', 'tutor', 'student', 'department', 'course', 'type', 'rating');
        $centered = array('tutoringdate', 'department', 'course');
        parent::__construct(
            'tutoring_table', $columns, $headers, $sortable, $centered, $filter, !$email && !$this->is_downloading(), false
        );
        if ($email) {
            $this->sortable(false);
            $this->column_style_all('vertical-align', 'middle');
            $this->column_style_all('padding', '0.25rem');
            $this->column_style_all('border', '1px solid #999');
        }

        $fields = array(
            's.id', 's.tutorid', "CONCAT(tu.lastname, ', ', tu.firstname) AS tutor", 's.studentid',
            "CONCAT(su.lastname, ', ', su.firstname) AS student", "CONCAT(ua.lastname, ', ', ua.firstname) AS advisor", 's.tutoring_date AS tutoringdate', 'd.name AS department',
            'c.name AS course', 's.topic', 'ty.displaytext AS type', 's.other', 'r.displaytext AS rating', 's.notes'
        );
        $from = array(
            '{local_peertutoring_session} s', '{user} tu ON s.tutorid = tu.id', '{user} su ON s.studentid = su.id',
            '{local_mxschool_student} sa ON s.studentid = sa.userid',
            '{user} ua ON sa.advisorid = ua.id',
            '{local_peertutoring_tutor} t ON s.tutorid = t.userid', '{local_peertutoring_course} c ON s.courseid = c.id',
            '{local_peertutoring_dept} d ON c.departmentid = d.id', '{local_peertutoring_type} ty ON s.typeid = ty.id',
            '{local_peertutoring_rating} r ON s.ratingid = r.id'
        );
        $where = array(
            's.deleted = 0', 'tu.deleted = 0', 'su.deleted = 0', 't.deleted = 0', 'c.deleted = 0', 'd.deleted = 0',
            'ty.deleted = 0', 'r.deleted = 0'
        );
        if ($filter->tutor) {
            $where[] = "tu.id = {$filter->tutor}";
        }
        if ($filter->advisor) {
            $where[] = "ua.id = {$filter->advisor}";
        }
        if ($filter->department) {
            $where[] = "c.departmentid = {$filter->department}";
        }
        if ($filter->type) {
            $where[] = "s.typeid = {$filter->type}";
        }
        if ($filter->date) {
            $starttime = generate_datetime($filter->date);
            $endtime = clone $starttime;
            $endtime->modify('+1 day');
            array_push($where, "s.tutoring_date >= {$starttime->getTimestamp()}", "s.tutoring_date < {$endtime->getTimestamp()}");
        }
        $searchable = array(
            'tu.lastname', 'tu.firstname', 'tu.alternatename', 'su.lastname', 'su.firstname', 'su.alternatename', 'd.name',
            'c.name', 's.topic', 'ty.displaytext', 's.other', 'r.displaytext', 's.notes'
        );
        $this->define_sql($fields, $from, $where, $searchable, $filter->search);
    }

    /**
     * Formats the tutor column to "last, first (preferred)" or "last, first".
     */
    protected function col_tutor($values) {
        return format_student_name($values->tutorid);
    }

    /**
     * Formats the tutoring date column to 'n/j/y'.
     */
    protected function col_tutoringdate($values) {
        return format_date('n/j/y', $values->tutoringdate);
    }

    /**
     * Formats the student column to "last, first (preferred)" or "last, first".
     */
    protected function col_student($values) {
        return format_student_name($values->studentid);
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
        return $this->edit_icon('/local/peertutoring/form.php', $values->id) . $this->delete_icon($values->id);
    }

}
