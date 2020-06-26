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

 use local_mxschool\output\comment;

 class table extends \local_mxschool\table {

   /**
    * Creates a new table.
    *
    * @param stdClass $filter Any filtering for the table.
    * @param string download, indicates if the table is downloading
    */
   public function __construct($filter, $download) {
	  $this->is_downloading($download, 'Healthpass', 'Healthpass');
 	  // Define the names of the columns. Should match up with the $fields array.
       $columns = array('userid', 'status', 'body_temperature', 'symptoms', 'override_status', 'comment', 'time_submitted');
 	  // Get headers from language file
       $headers = $this->generate_headers($columns, 'healthpass:report');
 	  // Define name, status, body_temp, and time_submitted as sortable
       $sortable = array('userid', 'status', 'time_submitted');
 	  // All columns are centered
       $centered = array('userid', 'status', 'body_temperature', 'symptoms', 'override_status', 'comment', 'time_submitted');
       parent::__construct('health_table', $columns, $headers, $sortable, $centered, $filter, false);

 	  // The fields to query from the database
       $fields = array('u.id', "CONCAT(u.lastname, ', ', u.firstname) AS userid", 'hp.status',
                         'hp.body_temperature', 'hp.symptoms', 'hp.override_status', 'hp.comment', 'hp.form_submitted AS time_submitted');
 	  // The tables which to query
       $from = array('{user} u', '{local_mxschool_healthpass} hp ON u.id = hp.userid');
 	  // Get everything unless there are filters
 	  $where = array('u.deleted = 0');

 	  // If filtering by status, append to where[] accordingly
        if ($filter->status) {
		  $today = generate_datetime(time())->modify('midnight');
 		  if($filter->status == 'Unsubmitted') {
			   $where[] = "hp.form_submitted < {$today->getTimestamp()} OR hp.userid IS NULL";
 		  }
		  else if($filter->status == 'Submitted') {
			   $where[] = "hp.form_submitted >= {$today->getTimestamp()}";
		  }
		  else{
	             $where[] = "hp.status = '{$filter->status}'";
			   $where[] = "hp.form_submitted >= {$today->getTimestamp()}";
		   }
        }

        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename');
        $this->define_sql($fields, $from, $where, $searchable, $filter->search);
   }


   protected function col_status($values) {
	  $today = generate_datetime(time())->modify('midnight');
	  if($values->time_submitted < $today->getTimestamp() or !isset($values->time_submitted)) {
		 return $this->is_downloading() ? 'Unsubmitted' : "<p style='color:goldenrod;'>Unsubmitted</p>";
	  }
	  if($this->is_downloading()) return $values->status;
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

   protected function col_override_status($values) {
	   $today = generate_datetime(time())->modify('midnight');
	   if($values->time_submitted < $today->getTimestamp()) return '';
	   if($this->is_downloading()) return $values->override_status;
	   if(isset($_POST["update_override{$values->id}"])) {
		   update_healthform_override_status($values->id, $values->status, $values->override_status);
		   echo "<script>window.location.reload();</script>";
	   }
	   switch ($values->override_status) {
		   case 'Not Overridden':
					return "<form method='POST'><button style='
								background-color:dodgerblue;
						          color:white;
						          text-align:center;
						          cursor:pointer;'
						   id='update_override{$values->id}' name='update_override{$values->id}' type='submit'
						   target='_blank'>".get_string('healthpass:report:review_button', 'local_mxschool')."</button>";
					break;
			case 'Under Review':
					return "<p>".get_string('healthpass:report:under_review', 'local_mxschool').
						  "</p><form method='POST'><button style='
								background-color:peru;
								color:white;
								text-align:center;
								cursor:pointer;'
						   id='update_override{$values->id}' name='update_override{$values->id}' type='submit'
						   target='_blank'>".get_string('healthpass:report:override_button', 'local_mxschool')."</button>";
  					break;
			case 'Overridden':
					return "<p>".get_string('healthpass:report:overridden', 'local_mxschool').
						  "</p><form method='POST'><button style='
								background-color:lightcoral;
								color:black;
								text-align:center;
								cursor:pointer;'
						   id='update_override{$values->id}' name='update_override{$values->id}' type='submit'
						   target='_blank'>".get_string('healthpass:report:undo_override_button', 'local_mxschool')."</button>";
  					break;
			default:
					return "ERROR";
					break;
		}
    }

    protected function col_comment($values) {
	    if($this->is_downloading()) return $values->comment;
	    // if(isset($_POST["comment_submit{$values->id}"])) {
		//     update_healthform_comment($values->id, $_POST["comment{$values->id}"]);
		//     echo "<script>window.location.reload();</script>";
	    // }
	    // if(isset($_POST["comment_edit{$values->id}"])) {
		//     return "<form method='POST'>
     	//     		   <textarea name='comment{$values->id}'>{$values->comment}</textarea>
     	// 		   <button type='submit' name='comment_submit{$values->id}'>Save</button>
     	// 		  </form>";
	    // }
	    // return "<p>{$values->comment}</p>
	    // 	       <form method='POST'>
		// 	   <button type='submit' name='comment_edit{$values->id}'>Edit</button>
		// 	  </form>";
		global $PAGE;
		$output = $PAGE->get_renderer('local_mxschool');
		$renderable = new comment($values->id, $values->comment, 'Edit', 'Save');
		return $output->render($renderable);
    }
}
