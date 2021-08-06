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
 use local_mxschool\output\changeable_text;
 use local_mxschool\output\override_button;
 use local_mxschool\output\email_button;

 class table extends \local_mxschool\table {

   /**
    * Creates a new table.
    *
    * @param stdClass $filter Any filtering for the table.
    * @param string download, indicates if the table is downloading
    */
   public function __construct($filter, $download) {
	  $today = generate_datetime(time())->modify('midnight');
	  $this->is_downloading($download, "COVIDpass-{$today->format('ymd')}", "COVIDpass-{$today->format('ymd')}");
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
                         'hp.body_temperature', 'hp.symptoms', 'hp.override_status', 'hp.comment', 'hp.form_submitted AS time_submitted', 'u.alternatename');
 	  // The tables which to query
       $from = array('{user} u', '{local_mxschool_healthpass} hp ON u.id = hp.userid');
 	  // Get everything unless there are filters
 	  $where = array('u.deleted = 0');

 	  // If filtering by status, append to where[] accordingly
        if ($filter->status) {
		  $today = generate_datetime(time())->modify('4 AM');
 		  if($filter->status == 'Unsubmitted') {
			   $where[] = "(hp.form_submitted < {$today->getTimestamp()} OR hp.userid IS NULL)";
 		  }
		  else if($filter->status == 'Submitted') {
			   $where[] = "hp.form_submitted >= {$today->getTimestamp()}";
		  }
		  else{
	             $where[] = "hp.status = '{$filter->status}'";
			   $where[] = "hp.form_submitted >= {$today->getTimestamp()}";
		   }
        }
	   switch($filter->user_type) {
		   case 'Students':
		   		$from[] = "{local_mxschool_student} stu ON u.id = stu.userid";
				$from[] = "{local_mxschool_dorm} d ON stu.dormid = d.id";
				$where[] = "stu.userid IS NOT NULL";
				break;
		   case 'Faculty':
			     $from[] = "{local_mxschool_faculty} fac ON u.id = fac.userid";
			     $where[] = "fac.userid IS NOT NULL";
				break;
		   case 'Staff':
		   		$from[] = "{local_mxschool_student} stu ON u.id = stu.userid";
			     $from[] = "{local_mxschool_faculty} fac ON u.id = fac.userid";
				$where[] = "stu.userid IS NULL";
				$where[] = "fac.userid IS NULL";
				break;
		   default:
		   		break;
	   }
	   if ($filter->dorm) {
		  $where[] = $this->get_dorm_where($filter->dorm);
	   }
        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename');
        $this->define_sql($fields, $from, $where, $searchable, $filter->search);
   }

   protected function col_userid($values) {
	   $today = generate_datetime(time())->modify('4 AM');
	   $name = $values->alternatename ? "{$values->userid} ({$values->alternatename})" : $values->userid;
	   if($values->time_submitted < $today->getTimestamp() or !isset($values->time_submitted) or $this->is_downloading()) {
		  return $name;
	   }
	   global $PAGE;
	   $output = $PAGE->get_renderer('local_mxschool');
	   $student_info = get_student_contact_info($values->id);
	   $contact_info = $student_info ? "\n{$student_info->dorm_name} ({$student_info->boarding_status})\n{$student_info->phone_number}" : '';
	   $renderable = new changeable_text($values->id, 'contact_info', $contact_info);
	   return "{$name}{$output->render($renderable)}";
   }

   protected function col_status($values) {
	  $today = generate_datetime(time())->modify('4 AM');
	  if($values->time_submitted < $today->getTimestamp() or !isset($values->time_submitted)) {
		 return $this->is_downloading() ? 'Unsubmitted' : "<span style='color:goldenrod;'>Unsubmitted</span>";
	  }
	  if($this->is_downloading()) return $values->status;
	  global $PAGE;
	  $output = $PAGE->get_renderer('local_mxschool');
	  $renderable = new changeable_text($values->id, 'status', $values->status);
	  return $output->render($renderable);
   }

   protected function col_time_submitted($values) {
	   return $values->time_submitted==='' ? 'Never' :
		   !empty($values->time_submitted) ? format_date('n/j/y g:i A', $values->time_submitted)
		   : 'Never';
   }

   protected function col_body_temperature($values) {
	   $today = generate_datetime(time())->modify('4 AM')->getTimestamp();
	   if(!isset($values->time_submitted) or $values->time_submitted < $today) return '';
	   else return $values->body_temperature;
   }

   protected function col_symptoms($values) {
		$today = generate_datetime(time())->modify('4 AM')->getTimestamp();
		if(!isset($values->symptoms) or $values->time_submitted < $today) return '';
		else return $values->symptoms;
   }

   protected function col_override_status($values) {
	   $today = generate_datetime(time())->modify('4 AM');
	   if($values->time_submitted < $today->getTimestamp()) return '';
	   if($this->is_downloading()) return $values->override_status;
	   global $PAGE;
	   $output = $PAGE->get_renderer('local_mxschool');
	   $renderable_override = new override_button($values->id, $values->override_status);
	   $renderable_email = new email_button(
		   get_string('healthpass:report:override_email_button', 'local_mxschool'),
		   'healthpass_overridden', $values->id, false, true
	   );
	   return "{$output->render($renderable_override)}{$output->render($renderable_email)}";
    }

    protected function col_comment($values) {
	    if($this->is_downloading()) return $values->comment;
    	    $today = generate_datetime(time())->modify('4 AM')->getTimestamp();
	    if($values->time_submitted < $today or !isset($values->time_submitted)) return '';
	    global $PAGE;
	    $output = $PAGE->get_renderer('local_mxschool');
	    $today = generate_datetime(time())->modify('4 AM')->getTimestamp();
	    $renderable = new comment($values->id, 'hp', $values->comment, 'Edit', 'Save', 'local_mxschool_healthpass');
	    return $output->render($renderable);
    }
}
