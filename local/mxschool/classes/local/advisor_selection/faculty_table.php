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
 * Faculty preferences table for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  advisor_selection
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\advisor_selection;

defined('MOODLE_INTERNAL') || die();

use local_mxschool\output\checkbox;

class faculty_table extends \local_mxschool\table {

    /**
     * Creates a new faculty_table.
     */
    public function __construct() {
        $columns = array('name', 'advisoryavailable', 'advisoryclosing');
        $headers = $this->generate_headers($columns, 'user_management_faculty_report');
        $sortable = array('name');
        $centered = array('advisoryavailable', 'advisoryclosing');
        parent::__construct('faculty_table', $columns, $headers, $sortable, $centered, array(), false);

        $fields = array(
            'f.id', "CONCAT(u.lastname, ', ', u.firstname) AS name", 'f.advisory_available AS advisoryavailable',
            'f.advisory_closing AS advisoryclosing'
        );
        $from = array('{local_mxschool_faculty} f', '{user} u ON f.userid = u.id');
        $where = array('u.deleted = 0');
        $this->set_sql($fields, $from, $where);
    }

    /**
     * Formats the advisory available column to a checkbox.
     */
    protected function col_advisoryavailable($values) {
        global $PAGE;
        $output = $PAGE->get_renderer('local_mxschool');
        $renderable = new checkbox($values->id, 'local_mxschool_faculty', 'advisory_available', $values->advisoryavailable);
        return $output->render($renderable);
    }

    /**
     * Formats the advisory closing column to a checkbox.
     */
    protected function col_advisoryclosing($values) {
        global $PAGE;
        $output = $PAGE->get_renderer('local_mxschool');
        $renderable = new checkbox($values->id, 'local_mxschool_faculty', 'advisory_closing', $values->advisoryclosing);
        return $output->render($renderable);
    }

}
