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

 if(get_config('local_mxschool', 'healthpass_enabled')=='No') {
	 redirect_to_fallback();
 }

 $id = optional_param('id', 0, PARAM_INT);
 setup_mxschool_page('form', 'healthpass');
 $PAGE->requires->js_call_amd('local_mxschool/healthpass_form', 'setup');
 $isstudent = user_is_student();

 $queryfields = array(
     'local_mxschool_healthpass' => array(
         'abbreviation' => 'hif',
         'fields' => array(
             'id', 'userid', 'status', 'body_temperature', 'anyone_sick_at_home', 'traveled_internationally',
             'has_fever', 'has_sore_throat', 'has_cough', 'has_runny_nose',
             'has_muscle_aches', 'has_loss_of_sense', 'has_short_breath', 'form_submitted' => 'timecreated'
         )
     )
 );


 // Create a new record each time this form is submitted.
 $data = new stdClass();
 $data->id = $id;
 $data->userid = $USER->id;
 $data->timecreated = time();
 $data->isstudent = $isstudent ? '1' : '0';

 $isManager = has_capability('local/mxschool:manage_healthpass', context_system::instance());
 $students = $isManager ? get_user_list() : array($USER->id => $USER->firstname.' '.$USER->lastname);

 $form = new local_mxschool\local\healthpass\form(array('students' => $students));
 $form->set_data($data);

 if($form->is_cancelled()){
   redirect($form->get_redirect());
 }
 elseif($data = $form->get_data()) {
	 // logic for approve/deny
   if ($data->body_temperature != 98 or $data->anyone_sick_at_home
      or $data->traveled_internationally or $data->has_fever or $data->has_sore_throat or $data->has_cough
      or $data->has_runny_nose or $data->has_muscle_aches or $data->has_loss_of_sense
      or $data->has_short_breath) {
        $data->status = "Denied";
      }
   else {
     $data->status = "Approved";
   }

   // Switch from 'yes' and 'no' to 1 and 0 for db
   $data->anyone_sick_at_home = $data->anyone_sick_at_home['anyone_sick_at_home']=='Yes' ? 1 : 0;
   $data->traveled_internationally = $data->traveled_internationally['traveled_internationally']=='Yes' ? 1 : 0;

   if($data->none_above['none_above']=='Yes') { // if none_above was selected, set all data to no
	   $data->has_fever = 0;
	   $data->has_sore_throat = 0;
	   $data->has_cough = 0;
	   $data->has_runny_nose = 0;
	   $data->has_muscle_aches = 0;
	   $data->has_loss_of_sense = 0;
	   $data->has_short_breath = 0;
   }
   else { // None above was not selected so must switch each field from 'yes' and 'no' to 1 and 0 for db
	   $data->has_fever = $data->has_fever['has_fever']=='Yes' ? 1 : 0;
	   $data->has_sore_throat = $data->has_sore_throat['has_sore_throat']=='Yes' ? 1 : 0;
	   $data->has_cough = $data->has_cough['has_cough']=='Yes' ? 1 : 0;
	   $data->has_runny_nose = $data->has_runny_nose['has_runny_nose']=='Yes' ? 1 : 0;
	   $data->has_muscle_aches = $data->has_muscle_aches['has_muscle_aches']=='Yes' ? 1 : 0;
	   $data->has_loss_of_sense = $data->has_loss_of_sense['has_loss_of_sense']=='Yes' ? 1 : 0;
	   $data->has_short_breath = $data->has_short_breath['has_short_breath']=='Yes' ? 1 : 0;
   }
   $id = update_record($queryfields, $data); // put data in db
   podio_submit($data); // submit data to podio
   logged_redirect(
       $form->get_redirect(), get_string('healthpass:form:success', 'local_mxschool'), $data->id ? 'update' : 'create'
   );
 }

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\form($form);

echo $output->header();
echo $output->heading(
  get_string('healthpass:form', 'local_mxschool')
);
echo $output->render($renderable);
echo $output->footer();
