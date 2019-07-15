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
 * Advisor selection table for Middlesex School's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage advisor_selection
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_table.php');

class advisor_table extends local_mxschool_table {

    /**
     * Creates a new advisor_table.
     *
     * @param stdClass $filter Any filtering for the table - could include propeties submitted, keepcurrent, and search.
     * @param string $download Indicates whether the table is downloading.
     */
    public function __construct($filter, $download) {
        $this->is_downloading($download, 'Advisor Selection Records', 'Advisor Selection Records');
        $columns = array(
            'student', 'current', 'keepcurrent', 'option1', 'option2', 'option3', 'option4', 'option5', 'selected'
        );
        if ($filter->keepcurrent !== '') {
            unset($columns[array_search('keepcurrent', $columns)]);
        }
        $headers = array_map(function($column) {
            return get_string("advisor_report_header_{$column}", 'local_mxschool');
        }, $columns);
        if (!$this->is_downloading()) {
            $columns[] = 'actions';
            $headers[] = get_string('report_header_actions', 'local_mxschool');
        }
        $fields = array(
            's.id', 's.userid', 'asf.id AS asfid', "CONCAT(u.lastname, ', ', u.firstname) AS student",
            'asf.keep_current AS keepcurrent', "CONCAT(ca.lastname, ', ', ca.firstname) AS current", 'ca.id AS cid',
            'o1a.id AS o1id', 'o2a.id AS o2id', 'o3a.id AS o3id', 'o4a.id AS o4id', 'o5a.id AS o5id', 'sa.id AS sid',
        );
        $from = array(
            '{local_mxschool_student} s', '{user} u ON s.userid = u.id', '{user} ca ON s.advisorid = ca.id',
            '{local_mxschool_adv_selection} asf ON s.userid = asf.userid', '{user} o1a ON asf.option1id = o1a.id',
            '{user} o2a ON asf.option2id = o2a.id', '{user} o3a ON asf.option3id = o3a.id', '{user} o4a ON asf.option4id = o4a.id',
            '{user} o5a ON asf.option5id = o5a.id', '{user} sa ON asf.selectedid = sa.id'
        );
        $year = (int)format_date('Y') - 1;
        $where = array(
            'u.deleted = 0', $filter->keepcurrent !== '' ? "asf.keep_current = {$filter->keepcurrent}" : '',
            get_config('local_mxschool', 'advisor_form_enabled_who') === 'new' ? "s.admission_year = {$year}" : 's.grade <> 12'
        );
        switch ($filter->submitted) {
            case '1':
                $where[] = "EXISTS (SELECT userid FROM {local_mxschool_adv_selection} WHERE userid = u.id)";
                break;
            case '0':
                $where[] = "NOT EXISTS (SELECT userid FROM {local_mxschool_adv_selection} WHERE userid = u.id)";
                break;
        }
        $sortable = array('student', 'current');
        $urlparams = array('submitted' => $filter->submitted, 'keepcurrent' => $filter->keepcurrent, 'search' => $filter->search);
        $centered = array('current', 'keepcurrent', 'option1', 'option2', 'option3', 'option4', 'option5');
        $searchable = array(
            'u.firstname', 'u.lastname', 'u.alternatename', 'ca.firstname', 'ca.lastname', 'o1a.firstname', 'o1a.lastname',
            'o2a.firstname', 'o2a.lastname', 'o3a.firstname', 'o3a.lastname', 'o4a.firstname', 'o4a.lastname', 'o5a.firstname',
            'o5a.lastname', 'sa.firstname', 'sa.lastname'
        );
        parent::__construct(
            'advisor_table', $columns, $headers, $sortable, 'student', $fields, $from, $where, $urlparams, $centered,
            $filter->search, $searchable
        );

        $this->column_class('selected', "{$this->column_class['selected']} selection-selected");
    }

    /**
     * Formats the student column to "last, first (preferred)" or "last, first".
     */
    protected function col_student($values) {
        return format_student_name($values->userid);
    }

    /**
     * Formats the keep current column.
     */
    protected function col_keepcurrent($values) {
        return isset($values->keepcurrent) ? format_boolean($values->keepcurrent) : '';
    }

    /**
     * Formats the current column to a selection button.
     */
    protected function col_current($values) {
        global $PAGE;
        if (!isset($values->cid)) {
            return '';
        }
        if ($this->is_downloading()) {
            return $values->current;
        }
        $output = $PAGE->get_renderer('local_mxschool');
        $renderable = new \local_mxschool\output\selection_button($values->userid, $values->cid, $values->current);
        return $output->render($renderable);
    }

    /**
     * Formats the option 1 column to a selection button.
     */
    protected function col_option1($values) {
        global $PAGE;
        if (!isset($values->o1id)) {
            return '';
        }
        $text = format_faculty_name($values->o1id);
        if ($this->is_downloading()) {
            return $text;
        }
        $output = $PAGE->get_renderer('local_mxschool');
        $renderable = new \local_mxschool\output\selection_button($values->userid, $values->o1id, $text);
        return $output->render($renderable);
    }

    /**
     * Formats the option 2 column to a selection button.
     */
    protected function col_option2($values) {
        global $PAGE;
        if (!isset($values->o2id)) {
            return '';
        }
        $text = format_faculty_name($values->o2id);
        if ($this->is_downloading()) {
            return $text;
        }
        $output = $PAGE->get_renderer('local_mxschool');
        $renderable = new \local_mxschool\output\selection_button($values->userid, $values->o2id, $text);
        return $output->render($renderable);
    }

    /**
     * Formats the option 3 column to a selection button.
     */
    protected function col_option3($values) {
        global $PAGE;
        if (!isset($values->o3id)) {
            return '';
        }
        $text = format_faculty_name($values->o3id);
        if ($this->is_downloading()) {
            return $text;
        }
        $output = $PAGE->get_renderer('local_mxschool');
        $renderable = new \local_mxschool\output\selection_button($values->userid, $values->o3id, $text);
        return $output->render($renderable);
    }

    /**
     * Formats the option 4 column to a selection button.
     */
    protected function col_option4($values) {
        global $PAGE;
        if (!isset($values->o4id)) {
            return '';
        }
        $text = format_faculty_name($values->o4id);
        if ($this->is_downloading()) {
            return $text;
        }
        $output = $PAGE->get_renderer('local_mxschool');
        $renderable = new \local_mxschool\output\selection_button($values->userid, $values->o4id, $text);
        return $output->render($renderable);
    }

    /**
     * Formats the option 5 column to a selection button.
     */
    protected function col_option5($values) {
        global $PAGE;
        if (!isset($values->o5id)) {
            return '';
        }
        $text = format_faculty_name($values->o5id);
        if ($this->is_downloading()) {
            return $text;
        }
        $output = $PAGE->get_renderer('local_mxschool');
        $renderable = new \local_mxschool\output\selection_button($values->userid, $values->o5id, $text);
        return $output->render($renderable);
    }

    /**
     * Formats the selected column to "Last, First".
     */
    protected function col_selected($values) {
        return isset($values->sid) ? format_faculty_name($values->sid) : '';
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return isset($values->asfid) ? $this->edit_icon('/local/mxschool/advisor_selection/advisor_enter.php', $values->asfid) : '';
    }

}
