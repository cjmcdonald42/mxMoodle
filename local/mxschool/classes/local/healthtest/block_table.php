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
 * Block report table for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  healthtest
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2021 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 namespace local_mxschool\local\healthtest;

 defined('MOODLE_INTERNAL') || die();

 use local_mxschool\output\checkbox;

 class block_table extends \local_mxschool\table {

   /**
    * Creates a new table.
    *
    * @param stdClass $filter Any filtering for the table.
    * @param string download, indicates if the table is downloading
    */
   public function __construct($filter, $download) {
	  $this->is_downloading($download);
 	  // Define the names of the columns. Should match up with the $fields array.
       $columns = array('testing_cycle', 'start_time', 'end_time', 'date', 'num_testers', 'max_testers');
 	  // Get headers from language file
       $headers = $this->generate_headers($columns, 'healthtest:block_report');
 	  // Define sortable columns
       $sortable = array('testing_cycle', 'start_time', 'end_time', 'date', 'max_testers');
 	  // All columns are centered
       $centered = array('testing_cycle', 'start_time', 'end_time', 'date', 'num_testers', 'max_testers');
       parent::__construct('healthtest_block_table', $columns, $headers, $sortable, $centered, $filter, !$this->is_downloading());

 	  // The fields to query from the database
       $fields = array('tb.id AS tbid', 'tb.testing_cycle', 'tb.start_time', 'tb.end_time', 'tb.date', 'tb.max_testers');
 	  // The tables which to query
       $from = array('{local_mxschool_testing_block} tb');
 	  // Get everything unless there are filters
 	  $where = array('tb.max_testers > 0');

	  if($filter->testing_cycle) {
		  $where[] = "tb.testing_cycle = '{$filter->testing_cycle}'";
	  }

        $searchable = array('tb.date');
        $this->define_sql($fields, $from, $where, $searchable, $filter->search);
   }

	/* The following functions edit what is displayed in individual columns */

	protected function col_testing_cycle($values) {
		$testing_cycle_dates = get_testing_cycle_dates($values->testing_cycle);
		$cycle_start = date('n/d', strtotime($testing_cycle_dates['start']));
		$cycle_end = date('n/d', strtotime($testing_cycle_dates['end']));
		return "{$cycle_start} -- {$cycle_end}";
	}

	protected function col_start_time($values) {
		return date('g:i A', strtotime($values->start_time));
	}

	protected function col_end_time($values) {
		return date('g:i A', strtotime($values->end_time));
	}

	protected function col_date($values) {
		return date('M d', strtotime($values->date));
	}

	protected function col_num_testers($values) {
		return get_testing_block_num_testers($values->tbid);
	}
}
