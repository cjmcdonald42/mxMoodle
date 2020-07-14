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
 * Rooming table for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  rooming
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\rooming;

defined('MOODLE_INTERNAL') || die();

class table extends \local_mxschool\table {

    /**
     * Creates a new rooming_table.
     *
     * @param stdClass $filter Any filtering for the table
     *                         - could include properties submitted, gender, roomtype, double, and search.
     * @param string $download Indicates whether the table is downloading.
     */
    public function __construct($filter, $download) {
        $this->is_downloading($download, 'Rooming Requests', 'Rooming Requests');
        $columns = array();
        $headers = $this->generate_headers($columns, 'rooming:report');
        $sortable = array();
        $centered = array();
        parent::__construct('rooming_table', $columns, $headers, $sortable, $centered, $filter, !$this->is_downloading());

        $fields = array(

        );
        $from = array(

        );
	   $where = array(

	   );
        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename', 'ru.firstname', 'ru.lastname', 'ru.alternatename');
        $this->define_sql($fields, $from, $where, $searchable, $filter->search);
    }

}
