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
 * Advisor selection table for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_table.php');

class advisor_table extends local_mxschool_table {

    /**
     * Creates a new advisor_table.
     *
     * @param stdClass $filter Any filtering for the table - could include submitted, keepcurrent, and search.
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
        $headers = array();
        foreach ($columns as $column) {
            $headers[] = get_string("advisor_report_header_{$column}", 'local_mxschool');
        }
        if (!$this->is_downloading()) {
            $columns[] = 'actions';
            $headers[] = get_string('report_header_actions', 'local_mxschool');
        }
        $fields = array(
            's.id', 'u.id AS userid', 'asf.id AS asfid', "CONCAT(u.lastname, ', ', u.firstname) AS student", 'u.firstname',
            'u.alternatename', "CONCAT(ca.lastname, ', ', ca.firstname) AS current", 'asf.keep_current AS keepcurrent',
            "CONCAT(o1a.lastname, ', ', o1a.firstname) AS option1", "CONCAT(o2a.lastname, ', ', o2a.firstname) AS option2",
            "CONCAT(o3a.lastname, ', ', o3a.firstname) AS option3", "CONCAT(o4a.lastname, ', ', o4a.firstname) AS option4",
            "CONCAT(o5a.lastname, ', ', o5a.firstname) AS option5", "CONCAT(sa.lastname, ', ', sa.firstname) AS selected"
        );
        $from = array(
            '{local_mxschool_student} s', '{user} u ON s.userid = u.id', '{user} ca ON s.advisorid = ca.id',
            '{local_mxschool_adv_selection} asf ON s.userid = asf.userid', '{user} o1a ON asf.option1id = o1a.id',
            '{user} o2a ON asf.option2id = o2a.id', '{user} o3a ON asf.option3id = o3a.id', '{user} o4a ON asf.option4id = o4a.id',
            '{user} o5a ON asf.option5id = o5a.id', '{user} sa ON asf.selectedid = sa.id'
        );
        $where = array(
            'u.deleted = 0', $filter->submitted === '1' ? "EXISTS (
                SELECT userid FROM {local_mxschool_adv_selection} WHERE userid = u.id
            )" : '', $filter->submitted === '0' ? "NOT EXISTS (
                SELECT userid FROM {local_mxschool_adv_selection} WHERE userid = u.id
            )" : '', $filter->keepcurrent !== '' ? "asf.keep_current = {$filter->keepcurrent}" : ''
        );
        $sortable = array('student', 'current', 'keepcurrent');
        $urlparams = array('submitted' => $filter->submitted, 'keepcurrent' => $filter->keepcurrent, 'search' => $filter->search);
        $centered = array('keepcurrent');
        $searchable = array(
            'u.firstname', 'u.lastname', 'u.alternatename', 'ca.firstname', 'ca.lastname', 'o1a.firstname', 'o1a.lastname',
            'o2a.firstname', 'o2a.lastname', 'o3a.firstname', 'o3a.lastname', 'o4a.firstname', 'o4a.lastname', 'o5a.firstname',
            'o5a.lastname', 'sa.firstname', 'sa.lastname'
        );
        parent::__construct(
            'advisor_table', $columns, $headers, $sortable, 'student', $fields, $from, $where, $urlparams, $centered,
            $filter->search, $searchable
        );
    }

    /**
     * Formats the keep current column.
     */
    protected function col_keepcurrent($values) {
        return isset($values->keepcurrent) ? ($values->keepcurrent ? get_string('yes') : get_string('no')) : '';
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return isset($values->asfid)
            ? $this->edit_icon('/local/mxschool/advisor_selection/advisor_enter.php', $values->asfid) : '';
    }

}
