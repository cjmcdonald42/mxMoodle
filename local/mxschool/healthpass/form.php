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
             'has_fever', 'has_sore_throat', 'has_cough', 'has_runny_nose',
             'has_muscle_aches', 'has_loss_of_sense', 'has_short_breath', 'form_submitted' => 'timecreated'
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
   // Switch from 'yes' and 'no' to 1 and 0 for the database
   $data->anyone_sick_at_home = $data->anyone_sick_at_home['anyone_sick_at_home']=='Yes' ? 1 : 0;
   $data->traveled_internationally = $data->traveled_internationally['traveled_internationally']=='Yes' ? 1 : 0;
   // If no symptoms button pressed, set all symptoms to false
   if($data->no_symptoms) {
	   $data->has_fever = 0;
	   $data->has_sore_throat = 0;
	   $data->has_cough = 0;
	   $data->has_runny_nose = 0;
	   $data->has_muscle_aches = 0;
	   $data->has_loss_of_sense = 0;
	   $data->has_short_breath = 0;
   }
   // If save changes button pressed, switch from 'yes' and 'no' to 1 and 0 for db
   else {
	   $data->has_fever = $data->has_fever['has_fever']=='Yes' ? 1 : 0;
	   $data->has_sore_throat = $data->has_sore_throat['has_sore_throat']=='Yes' ? 1 : 0;
	   $data->has_cough = $data->has_cough['has_cough']=='Yes' ? 1 : 0;
	   $data->has_runny_nose = $data->has_runny_nose['has_runny_nose']=='Yes' ? 1 : 0;
	   $data->has_muscle_aches = $data->has_muscle_aches['has_muscle_aches']=='Yes' ? 1 : 0;
	   $data->has_loss_of_sense = $data->has_loss_of_sense['has_loss_of_sense']=='Yes' ? 1 : 0;
	   $data->has_short_breath = $data->has_short_breath['has_short_breath']=='Yes' ? 1 : 0;
   }
   // Submit data to podio and get response
   $status = podio_submit($data);
   // The status to be added to the database depends on Podio's response
   $data->status = $status=='Green' ? 'Approved' : 'Denied';
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
