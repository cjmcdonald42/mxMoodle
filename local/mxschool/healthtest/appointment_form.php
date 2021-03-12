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
 * Middlesex's Health Test Appointment Form.
 *
 * @package     local_mxschool
 * @subpackage  healthtest
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2021 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 require(__DIR__.'/../../../config.php');
 require_once(__DIR__.'/../locallib.php');

 // All members of the community access this form.
 require_login();

 $id = optional_param('id', 0, PARAM_INT);
 setup_mxschool_page('test_form', 'healthtest');
 $isstudent = user_is_student();

 // redirect of healthtest is disabled
 if (get_config('local_mxschool', 'healthtest_enabled')=='0') {
	redirect(new moodle_url('/my'));
 }

 // The fields in the database to query, and the corresponding $data value.
 $queryfields = array(
     'local_mxschool_healthtest' => array(
         'abbreviation' => 'ht',
         'fields' => array(
             'id', 'userid' => 'name', 'testing_block_id' => 'block', 'attended', 'time_created' => 'timecreated'
         )
     )
 );

 // Create a new record each time this form is submitted.
 $data = new stdClass();
 $data->id = $id;
 $data->timecreated = time();
 $data->isstudent = $isstudent ? '1' : '0';

 // Check if the user is a health Admin
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
 $form = new local_mxschool\local\healthtest\appointment_form(array('users' => $users, 'isManager' => $isManager, 'userid' => $USER->id));
 $form->set_data($data);

 if($form->is_cancelled()) { // If the cancel button is pressed...
	 redirect($form->get_redirect());
 }
 elseif($data = $form->get_data()) { // If the 'Save Changes' button is pressed...
	// redirect if no testing block is selected
	if(!$data->block) redirect($form->get_redirect());

	$data->attended = 0;
	if(!isset($data->name)) $data->name = $USER->id;

	// Add the user's form data to the database
	global $DB;
	if(!$DB->record_exists("local_mxschool_healthtest", array("testing_block_id" => "{$data->block}", 'userid' => "{$data->name}"))) {
		$id = update_record($queryfields, $data);
	}
	// send email if confirmation emails are enabled
	if(get_config('local_mxschool', 'healthtest_confirm_enabled')=='1') {
		(new local_mxschool\local\healthtest\healthtest_confirm($id))->send();
	}
	// Redirect user
	logged_redirect(
	  $form->get_redirect(), get_string('healthtest:form:success', 'local_mxschool'), $data->id ? 'update' : 'create'
	);
 }

// Output form to page
$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\form($form);

echo $output->header();
echo $output->heading(
  get_string('healthtest:form', 'local_mxschool')
);
echo $output->render($renderable);
echo $output->footer();
