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
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 require(__DIR__.'/../../../config.php');
 require_once(__DIR__.'/../locallib.php');

 // All members of the community access this form.
 require_login();

 // If Healthpass is disabled, redirect.
 if(get_config('local_mxschool', 'healthpass_enabled')=='0') {
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
             'id', 'userid' => 'name', 'status', 'body_temperature', 'anyone_sick_at_home', 'traveled_internationally',
             'symptoms', 'form_submitted' => 'timecreated'
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
 if($isManager){
	 $users = get_user_list();
 }
 // Else auto populates to user's name
 else {
	 $users = array('name' => $USER->firstname.' '.$USER->lastname);
 }

 // Create form and pass $users and $isManager
 $form = new local_mxschool\local\healthpass\form(array('users' => $users, 'isManager' => $isManager));
 $form->set_data($data);

 if($form->is_cancelled()){ // If the cancel button is pressed...
   redirect($form->get_redirect());
 }
 elseif($data = $form->get_data()) { // If the 'Save Changes' or 'I have no symptoms' button is pressed...
   // Name will not be set if the field was static, so sets the name here.
   if(!isset($data->name)) $data->name = $USER->id;
   // Concat the temperature and the decimal into one value for DB
   $data->body_temperature = $data->body_temperature_temp . $data->body_temperature_temp_decimal;
   // Switch from 'yes' and 'no' to 1 and 0 for the database
   $data->anyone_sick_at_home = $data->anyone_sick_at_home['anyone_sick_at_home']=='Yes' ? 1 : 0;
   $data->traveled_internationally = $data->traveled_internationally['traveled_internationally']=='Yes' ? 1 : 0;
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
	   // add more symptoms here
	   if(strlen($data->symptoms) != 0) $data->symptoms = substr($data->symptoms, 0, -2);
	   else $data_symptoms = 'None';
   }
   // Logic for approve/deny healthpass TODO: Update with max_temp config
   $data->status = $data->symptoms == 'None' ? 'Approved' : 'Denied';
   // Put the form data in the database
   $id = update_record($queryfields, $data);
   // Successfully submitted message depends on healthpass status
   $response_string = $data->status=='Approved' ?
   				  get_string('healthpass:form:success:approved', 'local_mxschool')
				  : get_string('healthpass:form:success:denied', 'local_mxschool');
   // Redirect user
   logged_redirect(
       $form->get_redirect(), $response_string, $data->id ? 'update' : 'create'
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
