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

 setup_mxschool_page('form', 'healthpass');

 $id = optional_param('id', 0, PARAM_INT);
 $isstudent = user_is_student();

 $queryfields = array(
     'local_mxschool_healthpass' => array(
         'abbreviation' => 'hif',
         'fields' => array(
             'id', 'userid', 'body_temperature', 'anyone_sick_at_home',
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

 $temps = array('<95', '96', '97', '98', '99', '100', '101', '102', '103', '104', '104+');

 $form = new local_mxschool\local\healthpass\form(array('temps' => $temps));
 $form->set_data($data);

 if($form->is_cancelled()){
   redirect($form->get_redirect());
 }
 elseif($data = $form->get_data()) {
   $id = update_record($queryfields, $data);
   logged_redirect(
       $form->get_redirect(), get_string('advisor_selection:form:success', 'local_mxschool'), $data->id ? 'update' : 'create'
   );
 }

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\form($form);

 VAR_DUMP();
 echo "TESTTESTTEST";
echo $output->header();
echo $output->heading(
    $isstudent ? get_string('healthpass:health_form:title', 'local_mxschool', format_student_name($USER->id)) : $PAGE->title
);
echo $output->render($renderable);
echo $output->footer();
