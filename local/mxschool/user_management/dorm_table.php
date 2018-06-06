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
 * Dorm management table for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_table.php');

class dorm_table extends local_mxschool_table {

    /**
     * Creates a new dorm_table.
     *
     * @param string $uniqueid a unique identifier for the table.
     * @param string $search the search for the table.
     */
    public function __construct($uniqueid, $search) {
        $columns = array('name', 'abbreviation', 'hoh', 'type', 'gender', 'available');
        $headers = array();
        foreach ($columns as $column) {
            $headers[] = get_string("dorm_report_header_{$column}", 'local_mxschool');
        }
        $columns[] = 'actions';
        $headers[] = get_string('report_header_actions', 'local_mxschool');
        $fields = array(
            'd.id', 'd.name', 'd.abbreviation', "CONCAT(u.lastname, ', ', u.firstname) AS hoh", 'd.type', 'd.gender', 'd.available'
        );
        $from = array('{local_mxschool_dorm} d', '{user} u ON d.hohid = u.id');
        $where = array('d.deleted = 0', 'u.deleted = 0');
        $searchable = array('d.name', 'd.abbreviation', 'u.lastname', 'u.firstname');
        $sortable = array('name', 'type', 'gender');
        $urlparams = array('search' => $search);
        parent::__construct(
            $uniqueid, $columns, $headers, $sortable, 'name', $fields, $from, $where, $urlparams, $search, $searchable
        );
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return $this->edit_icon('/local/mxschool/user_management/dorm_edit.php', $values->id).$this->delete_icon($values->id);
    }

}
