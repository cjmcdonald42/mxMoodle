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
       $columns = array('userid', 'status', 'body_temperature', 'has_fever',
                        'has_sore_throat', 'has_cough', 'has_runny_nose',
                        'has_muscle_aches', 'has_loss_of_sense', 'has_short_breath', 'time_submitted');
       if ($filter->status) {
            unset($columns[array_search('status', $columns)]);
       }
	  if ($filter->date) {
		  unset($columns[array_search('time_submitted', $columns)]);
	  }
	  if($filter->submitted == '0') {
		  $columns = array('userid', 'time_submitted');
	  }
       $headers = $this->generate_headers($columns, 'healthpass:report');
       $sortable = array('userid', 'status', 'body_temperature', 'time_submitted');
       $centered = array('userid', 'status', 'body_temperature', 'has_fever',
                        'has_sore_throat', 'has_cough', 'has_runny_nose',
                        'has_muscle_aches', 'has_loss_of_sense', 'has_short_breath', 'time_submitted');
       parent::__construct('health_table', $columns, $headers, $sortable, $centered, $filter, false);

       $fields = array('hp.id', "CONCAT(u.lastname, ', ', u.firstname) AS userid", 'hp.status',
                        'hp.body_temperature', 'hp.has_fever', 'hp.has_sore_throat',
                        'hp.has_cough', 'hp.has_runny_nose', 'hp.has_muscle_aches',
                         'hp.has_loss_of_sense', 'hp.has_short_breath', 'hp.form_submitted AS time_submitted');
       $from = array('{local_mxschool_healthpass} hp',
                     '{user} u ON hp.userid = u.id', '{local_mxschool_student} stu ON hp.userid = stu.userid',
			 	  '{local_mxschool_faculty} fac ON hp.userid = fac.userid');
       $where = array('u.deleted = 0');
       if ($filter->status) {
           $where[] = "hp.status = '{$filter->status}'";
       }
	  if ($filter->user_type) {
		  if($filter->user_type == 'student') {
			  $where[] = "stu.userid IS NOT NULL";
		  }
		  else if($filter->user_type == 'facultystaff') {
			  $where[] = "fac.userid IS NOT NULL";
		  }
	  }
	  if ($filter->date) {
		 $starttime = generate_datetime($filter->date);
		 $endtime = clone $starttime;
		 $endtime->modify('+1 day');
		 array_push($where, "hp.form_submitted >= {$starttime->getTimestamp()}", "hp.form_submitted < {$endtime->getTimestamp()}");
	  }
	  $starttime = generate_datetime(time())->modify('midnight');
	  if($filter->submitted == '1') {
		  $endtime = clone $starttime;
		  $endtime->modify('+1 day');
		  array_push($where, "hp.form_submitted >= {$starttime->getTimestamp()}", "hp.form_submitted < {$endtime->getTimestamp()}");
	  }
	  else if($filter->submitted == '0') {
		  $fields = array('hp.id', "CONCAT(u.lastname, ', ', u.firstname) AS userid", "MAX(hp.form_submitted) AS time_submitted");
	       $where = array("u.deleted = 0 GROUP BY hp.userid");
	  }
	  ///"form_submitted < {$starttime->getTimestamp()}"
       $searchable = array('u.firstname', 'u.lastname', 'u.alternatename');
       $this->define_sql($fields, $from, $where, $searchable, $filter->search);
   }

   /**
    * The following methods reformat boolean values to "Yes" / "No". Any yes's will turn red
    */

   protected function col_has_fever($values) {
     return isset($values->has_fever) ? $values->has_fever ?
            "<p style='color:red;'>".get_string('yes')."</p>" : get_string('no') : '';
   }

   protected function col_has_sore_throat($values) {
     return isset($values->has_sore_throat) ? $values->has_sore_throat ?
            "<p style='color:red;'>".get_string('yes')."</p>" : get_string('no') : '';
   }

   protected function col_has_cough($values) {
     return isset($values->has_cough) ? $values->has_cough ?
            "<p style='color:red;'>".get_string('yes')."</p>" : get_string('no') : '';
   }

   protected function col_has_runny_nose($values) {
     return isset($values->has_runny_nose) ? $values->has_runny_nose ?
            "<p style='color:red;'>".get_string('yes')."</p>" : get_string('no') : '';
   }

   protected function col_has_muscle_aches($values) {
       return isset($values->has_muscle_aches) ? $values->has_muscle_aches ?
              "<p style='color:red;'>".get_string('yes')."</p>" : get_string('no') : '';
   }

   protected function col_has_loss_of_sense($values) {
     return isset($values->has_loss_of_sense) ? $values->has_loss_of_sense ?
            "<p style='color:red;'>".get_string('yes')."</p>" : get_string('no') : '';
   }

   protected function col_has_short_breath($values) {
     return isset($values->has_short_breath) ? $values->has_short_breath ?
            "<p style='color:red;'>".get_string('yes')."</p>" : get_string('no') : '';
   }

   protected function col_body_temperature($values) {
     return isset($values->body_temperature) ? $values->body_temperature != 98 ?
            "<p style='color:red;'>".$values->body_temperature."</p>" : $values->body_temperature
            : '';
   }

   protected function col_status($values) {
      if(isset($values->status)) {
            if($values->status == 'Approved') {
              return "<p style='color:green;'>".$values->status."</p>";
            }
            else if($values->status == 'Denied') {
              return "<p style='color:red;'>".$values->status."</p>";
            }
            else return $values->status;
      }
      else return '';
   }

   protected function col_time_submitted($values) {
	   return $values->time_submitted ? format_date('n/j/y g:i A', $values->time_submitted) : '';
   }

}
