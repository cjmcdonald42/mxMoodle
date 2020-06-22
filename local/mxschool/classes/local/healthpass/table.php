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
 * Health report table for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  healthpass
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 namespace local_mxschool\local\healthpass;

 defined('MOODLE_INTERNAL') || die();

 class table extends \local_mxschool\table {

   /**
    * Creates a new table.
    *
    * @param stdClass $filter Any filtering for the table.
    */
   public function __construct($filter) {
	  global $DB;
 	  // Define the names of the columns. Should match up with the $fields array.
       $columns = array('userid', 'status', 'body_temperature', 'symptoms', 'time_submitted');
 	  // Get headers from language file
       $headers = $this->generate_headers($columns, 'healthpass:report');
 	  // Define name, status, body_temp, and time_submitted as sortable
       $sortable = array('userid', 'status', 'time_submitted');
 	  // All columns are centered
       $centered = array('userid', 'status', 'body_temperature', 'symptoms', 'time_submitted');
       parent::__construct('health_table', $columns, $headers, $sortable, $centered, $filter, false);

 	  // The fields to query from the database
       $fields = array('u.id', "CONCAT(u.lastname, ', ', u.firstname) AS userid", 'hp.status',
                         'hp.body_temperature', 'hp.symptoms', 'MAX(hp.form_submitted) AS time_submitted');
 	  // The tables which to query
       $from = array('{user} u', '{local_mxschool_healthpass} hp ON u.id = hp.userid');
 	  // Get everything unless there are filters
 	  $where = array('u.deleted = 0 GROUP BY u.id');

 	  // If filtering by status, append to where[] accordingly
        if ($filter->status) {
 		  if($filter->status == 'Unsubmitted') {
			  $today = generate_datetime(time())->modify('midnight');
			  array_unshift(
				$where, "u.id NOT IN
			  	(SELECT userid FROM {local_mxschool_healthpass} WHERE form_submitted >=
			  	{$today->getTimestamp()})"
			  );
 		  }
             else array_unshift($where, "hp.status = '{$filter->status}'");
        }

        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename');
        $this->define_sql($fields, $from, $where, $searchable, $filter->search);
   }


   protected function col_status($values) {
	  $today = generate_datetime(time())->modify('midnight');
	  if($values->time_submitted < $today->getTimestamp() or !isset($values->time_submitted)) {
		 return "<p style='color:goldenrod;'>Unsubmitted</p>";
	  }
       else if($values->status == 'Approved') {
         return "<p style='color:green;'>".$values->status."</p>";
       }
       else if($values->status == 'Denied') {
         return "<p style='color:red;'>".$values->status."</p>";
       }
       else return '';
   }

   protected function col_time_submitted($values) {
	   return $values->time_submitted ? format_date('n/j/y g:i A', $values->time_submitted) : 'Never';
   }

   protected function col_body_temperature($values) {
	   $today = generate_datetime(time())->modify('midnight')->getTimestamp();
	   if(!isset($values->time_submitted) or $values->time_submitted < $today) return '';
	   else return $values->body_temperature;
   }

   protected function col_symptoms($values) {
		$today = generate_datetime(time())->modify('midnight')->getTimestamp();
		if(!isset($values->symptoms) or $values->time_submitted < $today) return '';
		else return $values->symptoms;
   }
}
