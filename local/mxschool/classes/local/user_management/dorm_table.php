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
 * Dorm management table for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  user_management
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\user_management;

defined('MOODLE_INTERNAL') || die();

class dorm_table extends \local_mxschool\table {

    /**
     * Creates a new dorm_table.
     *
     * @param stdClass $filter Any filtering for the table - could include property search.
     */
    public function __construct($filter) {
        $columns = array('name', 'hoh', 'permissionsline', 'type', 'gender', 'available');
        $headers = $this->generate_headers($columns, 'user_management_dorm_report');
        $sortable = array('name', 'type', 'gender', 'available');
        $centered = array('abbreviation', 'type', 'gender', 'available');
        parent::__construct('dorm_table', $columns, $headers, $sortable, $centered, $filter);

        $fields = array(
            'd.id', 'd.name', "d.hohid AS hoh", 'd.permissions_line AS permissionsline', 'd.type', 'd.gender', 'd.available'
        );
        $from = array('{local_mxschool_dorm} d', '{user} u ON d.hohid = u.id');
        $where = array('d.deleted = 0', 'u.deleted = 0');
        $searchable = array('d.name', 'd.abbreviation', 'u.lastname', 'u.firstname');
        $this->set_sql($fields, $from, $where, $searchable, $filter->search);
    }

    /**
     * Formats the hoh column to "Last, First".
     */
    protected function col_hoh($values) {
        return format_faculty_name($values->hoh);
    }

    /**
     * Formats the available column.
     */
    protected function col_available($values) {
        return format_boolean($values->available);
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return $this->edit_icon('/local/mxschool/user_management/dorm_edit.php', $values->id) . $this->delete_icon($values->id);
    }

}
