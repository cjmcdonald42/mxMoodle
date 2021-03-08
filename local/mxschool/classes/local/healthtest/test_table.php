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
 * Test report table for Middlesex's Dorm and Student Functions Plugin.
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

 class test_table extends \local_mxschool\table {

   /**
    * Creates a new table.
    *
    * @param stdClass $filter Any filtering for the table.
    * @param string download, indicates if the table is downloading
    */
   public function __construct($filter, $download) {
	  $this->is_downloading($download);
 	  // Define the names of the columns. Should match up with the $fields array.
       $columns = array('lastname', 'firstname', 'grade', 'boarding_status', 'dormname', 'attended');
 	  // Get headers from language file
       $headers = $this->generate_headers($columns, 'healthtest:test_report');
 	  // Define sortable columns
       $sortable = array('lastname', 'firstname', 'grade', 'dormname', 'attended');
 	  // All columns are centered
       $centered = array('lastname', 'firstname', 'grade', 'boarding_status', 'dormname', 'attended');
       parent::__construct('healthtest_test_table', $columns, $headers, $sortable, $centered, $filter, false);

 	  // The fields to query from the database
       $fields = array('ht.id', 'u.lastname', 'u.firstname', 'u.alternatename', 'stu.grade', 'stu.boarding_status',
  					'dorm.name AS dormname', 'ht.attended', 'ht.testing_block_id', 'tb.start_time', 'tb.end_time', 'tb.day_of_week', 'tb.date');
 	  // The tables which to query
       $from = array('{local_mxschool_healthtest} ht', '{local_mxschool_testing_block} tb ON tb.id = ht.testing_block_id',
  					'{user} u ON u.id = ht.userid', '{local_mxschool_student} stu ON stu.userid = u.id',
					'{local_mxschool_dorm} dorm ON dorm.id = stu.dormid');
 	  // Get everything unless there are filters
 	  $where = array('u.deleted = 0');

        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename', 'stu.grade', 'dorm.name');
        $this->define_sql($fields, $from, $where, $searchable, $filter->search);
   }

	protected function col_firstname($values) {
		if($values->alternatename) return "{$values->firstname} ({$values->alternatename})";
		return $values->firstname;
	}

	protected function col_grade($values) {
		if(!$values->grade) return "Faculty/Staff";
		return $values->grade;
	}

	protected function col_boarding_status($values) {
		if(!$values->boarding_status) return "NA";
		return $values->boarding_status;
	}

	protected function col_dormname($values) {
		if(!$values->dormname) return "NA";
		return $values->dormname;
	}

}
