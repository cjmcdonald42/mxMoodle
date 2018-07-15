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
 * Faculty preferences table for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_table.php');

class faculty_table extends local_mxschool_table {

    /**
     * Creates a new faculty_table.
     */
    public function __construct() {
        $columns = array('name', 'advisoryavailable', 'advisoryclosing');
        $headers = array();
        foreach ($columns as $column) {
            $headers[] = get_string("faculty_report_header_{$column}", 'local_mxschool');
        }
        $fields = array(
            'f.id', "CONCAT(u.lastname, ', ', u.firstname) AS name", 'f.advisory_available AS advisoryavailable',
            'f.advisory_closing AS advisoryclosing'
        );
        $from = array('{local_mxschool_faculty} f', '{user} u ON f.userid = u.id');
        $where = array('u.deleted = 0');
        $sortable = array('name');
        $urlparams = array();
        $centered = array('advisoryavailable', 'advisoryclosing');
        parent::__construct('faculty_table', $columns, $headers, $sortable, 'name', $fields, $from, $where, $urlparams, $centered);
    }

    /**
     * Formats the advisory available column to a checkbox.
     */
    protected function col_advisoryavailable($values) {
        global $PAGE;
        $output = $PAGE->get_renderer('local_mxschool');
        $renderable = new \local_mxschool\output\checkbox(
            $values->id, 'local_mxschool_faculty', 'advisory_available', $values->advisoryavailable
        );
        return $output->render($renderable);
    }

    /**
     * Formats the advisory closing column to a checkbox.
     */
    protected function col_advisoryclosing($values) {
        global $PAGE;
        $output = $PAGE->get_renderer('local_mxschool');
        $renderable = new \local_mxschool\output\checkbox(
            $values->id, 'local_mxschool_faculty', 'advisory_closing', $values->advisoryclosing
        );
        return $output->render($renderable);
    }

}
