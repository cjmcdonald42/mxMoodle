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
 * Generic checkin sheet table for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_table.php');

class generic_table extends local_mxschool_table {

    /**
     * Creates a new generic_table.
     *
     * @param string $uniqueid a unique identifier for the table.
     * @param string $dorm the id of the currently selected dorm or '' for all dorms.
     */
    public function __construct($uniqueid, $dorm) {
        $columns = array('student', 'room', 'grade', 'checkin');
        $headers = array();
        foreach ($columns as $column) {
            $headers[] = get_string("generic_report_header_{$column}", 'local_mxschool');
        }
        $fields = array(
            's.id', "CONCAT(u.lastname, ', ', u.firstname) AS student", 'u.firstname', 'u.alternatename', 's.room', 's.grade',
            "'' AS checkin"
        );
        $from = array('{local_mxschool_student} s', '{user} u ON s.userid = u.id', '{local_mxschool_dorm} d ON s.dormid = d.id');
        $where = array('u.deleted = 0', $dorm ? "d.id = $dorm" : '');
        $sortable = array('student', 'room', 'grade');
        $urlparams = array('dorm' => $dorm);
        $centered = array('room', 'grade');
        parent::__construct($uniqueid, $columns, $headers, $sortable, 'student', $fields, $from, $where, $urlparams, $centered);
    }

}
