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
 * Middlesex's Health Pass Form.
 *
 * @package     local_mxschool
 * @subpackage  healthpass
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 require(__DIR__.'/../../../config.php');
 require_once(__DIR__.'/../locallib.php');

 // All members of the community access this form.
 require_login();

 $user_healthform_info = get_todays_healthform_info($USER->id);

 // If Healthpass is disabled, redirect.
 if   (get_config('local_mxschool', 'healthpass_enabled')=='0')
 	  // NOTE: The line below only allows user to submit one healthpass a day.
       // or (!has_capability('local/mxschool:manage_healthpass', context_system::instance()) and $user_healthform_info->submitted_today))
    {
	 		redirect_to_fallback();
    }

 $id = optional_param('id', 0, PARAM_INT);
 setup_mxschool_page('form', 'healthpass');
 $isstudent = user_is_student();

 // The fields in the database to query, and the corresponding $data value.
 $queryfields = array(
     'local_mxschool_healthpass' => array(
         'abbreviation' => 'hif',
         'fields' => array(
             'id', 'userid' => 'name', 'status', 'body_temperature', 'health_info',
             'symptoms', 'override_status', 'comment' => 'health_info', 'form_submitted' => 'timecreated'
         )
     )
 );

 // Create a new record each time this form is submitted.
 $data = new stdClass();
 $data->id = $id;
 $data->timecreated = time();
 $data->isstudent = $isstudent ? '1' : '0';

 // Check if the user is a Healthpass Admin
 $isManager = has_capability('local/mxschool:manage_healthpass', context_system::instance());
 // If the user is an admin, will be able to select from a list of all users.
 if($isManager) {
	 $users = get_user_list();
 }
 // Else auto populates to user's name
 else {
	 $users = array('name' => $USER->firstname.' '.$USER->lastname);
 }

 // Create form and pass $users and $isManager
 $form = new local_mxschool\local\healthpass\form(array('users' => $users, 'isManager' => $isManager));
 $form->set_data($data);

 if($form->is_cancelled()) { // If the cancel button is pressed...
   redirect($form->get_redirect());
 }
 elseif($data = $form->get_data()) { // If the 'Save Changes' or 'I have no symptoms' button is pressed...
   // Name will not be set if the field was static, so sets the name here.
   if(!isset($data->name)) $data->name = $USER->id;
   // Concat the temperature and the decimal into one value for DB
   $data->body_temperature = $data->body_temperature_temp . $data->body_temperature_temp_decimal;

   // Add health_info questions to the health_info string
   $data->health_info = "";
   if($data->health_info0 == 'Yes') $data->health_info .= get_string("healthpass:health_info0", 'local_mxschool').", ";
   if($data->health_info1 == 'Yes') $data->health_info .= get_string("healthpass:health_info1", 'local_mxschool').", ";
   if($data->health_info2 == 'Yes') $data->health_info .= get_string("healthpass:health_info2", 'local_mxschool').", ";
   // add more health_info here
   if(strlen($data->health_info) != 0) $data->health_info = substr($data->health_info, 0, -2);
   else $data->health_info = '';

   // If no symptoms button pressed, set symptoms to NONE
   if($data->no_symptoms) {
	   $data->symptoms = 'None';
   }
   // If save changes button pressed, add symptoms to list if yes
   else {
	   $data->symptoms = "";
	   if($data->symptom0 == 'Yes') $data->symptoms .= get_string("healthpass:symptom0", 'local_mxschool').", ";
	   if($data->symptom1 == 'Yes') $data->symptoms .= get_string("healthpass:symptom1", 'local_mxschool').", ";
	   if($data->symptom2 == 'Yes') $data->symptoms .= get_string("healthpass:symptom2", 'local_mxschool').", ";
	   if($data->symptom3 == 'Yes') $data->symptoms .= get_string("healthpass:symptom3", 'local_mxschool').", ";
	   if($data->symptom4 == 'Yes') $data->symptoms .= get_string("healthpass:symptom4", 'local_mxschool').", ";
	   if($data->symptom5 == 'Yes') $data->symptoms .= get_string("healthpass:symptom5", 'local_mxschool').", ";
	   if($data->symptom6 == 'Yes') $data->symptoms .= get_string("healthpass:symptom6", 'local_mxschool').", ";
	   if($data->symptom7 == 'Yes') $data->symptoms .= get_string("healthpass:symptom7", 'local_mxschool').", ";
	   // add more symptoms here
	   if(strlen($data->symptoms) != 0) $data->symptoms = substr($data->symptoms, 0, -2);
	   else $data_symptoms = 'None';
   }

   // Logic for approve/deny healthpass
   if($data->symptoms=='None' and $data->body_temperature <= get_config('local_mxschool', 'healthpass_max_body_temp')
   	 and $data->health_info =='') {
		 $data->status = 'Approved';
	 }
   else $data->status = 'Denied';

   // Override status always should start with not_overridden
   $data->override_status = 'Not Overridden';

   // Add the user's form data to the database
   global $DB;
   if(!$DB->record_exists("local_mxschool_healthpass", array("userid" => "{$data->name}"))) {
	   $id = update_record($queryfields, $data);
   }
   // if a healthpass record already exists for the user, update it.
   else {
	   $sql = "
	   		UPDATE {local_mxschool_healthpass} hp
			SET hp.status = '{$data->status}', hp.body_temperature = '{$data->body_temperature}',
			    hp.symptoms = '{$data->symptoms}', hp.override_status = '{$data->override_status}',
			    hp.comment = '{$data->health_info}', hp.form_submitted = {$data->timecreated}
			WHERE hp.userid = {$data->name}
	   		";
	   $id = $DB->execute($sql);
   }
   // Sends email to the user depending on status.
   if($data->status=='Approved') {
	   $response_message = get_string('healthpass:form:success:approved', 'local_mxschool');
	   (new local_mxschool\local\healthpass\healthpass_approved($data->name))->send();
   }
   else if($data->status=='Denied') {
	   $response_message = get_string('healthpass:form:success:denied', 'local_mxschool');
	   if(get_config('local_mxschool', 'healthcenter_notification_enabled') == '1') {
		   (new local_mxschool\local\healthpass\healthcenter_notification($data->name))->send();
	   }
	   (new local_mxschool\local\healthpass\healthpass_denied($data->name))->send();
   }
   else throw new \coding_exception("ERROR: Unrecognized health status: {$data->status}");
   // Redirect user
   logged_redirect(
       $form->get_redirect(), $response_message, $data->id ? 'update' : 'create'
   );
 }

// Output form to page
$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\form($form);

echo $output->header();
echo $output->heading(
  get_string('healthpass:form', 'local_mxschool')
);
echo $output->render($renderable);
echo $output->footer();
