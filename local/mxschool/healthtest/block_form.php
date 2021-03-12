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
 * Page to create Testing Blocks for Middlesex's Dorm and Student Functions Plugin.
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

require_login();
$isstudent = user_is_student();

$id = optional_param('id', 0, PARAM_INT);

setup_mxschool_page('block_form', 'healthtest');

$queryfields = array(
    'local_mxschool_testing_block' => array(
        'abbreviation' => 'tb',
        'fields' => array(
            'id', 'testing_cycle', 'max_testers', 'start_time', 'end_time', 'date'
        )
    )
);

if ($id) { // Updating an existing record.
    if (!$DB->record_exists('local_mxschool_testing_block', array('id' => $id))) {
        redirect_to_fallback();
    }
    $data = get_record($queryfields, "tb.id = ?", array($id));
    if ($isstudent) { // Students cannot edit their forms.
        redirect($PAGE->url);
    }
    $data->isstudent = $isstudent ? '1' : '0';

    $time_data = array();

    $data->start_time_hour = date('h', strtotime($data->start_time));
    $data->start_time_minute = date('i', strtotime($data->start_time));
    $data->start_time_ampm = date('A', strtotime($data->start_time)) == 'AM' ? 0 : 1;

    $data->end_time_hour = date('h', strtotime($data->end_time));
    $data->end_time_minute = date('i', strtotime($data->end_time));
    $data->end_time_ampm = date('A', strtotime($data->end_time)) == 'AM' ? 0 : 1;

    $data->date = strtotime($data->date);
}
else {
	$data = new stdClass();
	$data->id = $id;
	$data->isstudent = $isstudent ? '1' : '0';
}
$testing_cycle_options = get_testing_cycle_list();

$form = new local_mxschool\local\healthtest\block_form(array('cycle_options' => $testing_cycle_options));
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    // format dates for DB
    $data->start_time_ampm = $data->start_time_ampm == 0 ? 'AM' : 'PM';
    $data->end_time_ampm = $data->end_time_ampm == 0 ? 'AM' : 'PM';

    $data->start_time_minute = $data->start_time_minute=='0' ? '00' : $data->start_time_minute;
    $data->end_time_minute = $data->end_time_minute=='0' ? '00' : $data->end_time_minute;

    $start_string = $data->start_time_hour.':'.$data->start_time_minute.' '.$data->start_time_ampm;
    $end_string = $data->end_time_hour.':'.$data->end_time_minute.' '.$data->end_time_ampm;

    $data->start_time = date('H:i', strtotime($start_string));
    $data->end_time = date('H:i', strtotime($end_string));
    $data->date = date('Y-m-d', $data->date);

    $id = update_record($queryfields, $data);
    logged_redirect(
        $form->get_redirect(), get_string('healthtest:block_form:success', 'local_mxschool'), $data->id ? 'update' : 'create'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
