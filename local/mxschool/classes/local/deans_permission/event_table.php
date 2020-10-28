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
 * Deans permission table for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  deans_permission
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\deans_permission;

defined('MOODLE_INTERNAL') || die();

class event_table extends \local_mxschool\table {

    /**
     * Creates a new deans_permission_table.
     *
     * @param stdClass $filter Any filtering for the table
     *                         - could include properties submitted, gender, roomtype, double, and search.
     * @param string $download Indicates whether the table is downloading.
     */
    public function __construct() {
        $columns = array('event_name');
        $headers = $this->generate_headers($columns, 'deans_permission:event_report');
        $centered = array('event_name');
	   $sortable = array('event-name');
        parent::__construct('deans_permission_event_table', $columns, $headers, $sortable, $centered);

        $fields = array(
		   'dpe.id', 'dpe.name AS event_name'
        );
        $from = array(
		   '{local_mxschool_dp_event} dpe'
        );
	   $where = array('dpe.id > 0');
        $this->define_sql($fields, $from, $where);
    }

    protected function col_actions($values) {
	    return isset($values->id) ? $this->edit_icon('/local/mxschool/deans_permission/event_edit.php', $values->id).
							   $this->delete_icon($values->id): '';
    }

}
