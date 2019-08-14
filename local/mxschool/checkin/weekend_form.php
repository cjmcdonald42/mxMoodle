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
 * Page for students to submit weekend travel plans for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  checkin
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
$isstudent = user_is_student();
if (!$isstudent) {
    require_capability('local/mxschool:manage_weekend', context_system::instance());
}

$id = optional_param('id', 0, PARAM_INT);

setup_mxschool_page('weekend_form', 'checkin');
$PAGE->requires->js_call_amd('local_mxschool/weekend_form', 'setup');

$queryfields = array(
    'local_mxschool_weekend_form' => array(
        'abbreviation' => 'wf',
        'fields' => array(
            'id', 'userid' => 'student', 'weekendid' => 'weekend', 'departure_date_time' => 'departure_date',
            'return_date_time' => 'return_date', 'destination', 'transportation', 'phone_number' => 'phone',
            'time_created' => 'timecreated', 'time_modified' => 'timemodified'
        )
    )
);

if ($isstudent && !student_may_access_weekend($USER->id)) {
    redirect_to_fallback();
}
if ($id) { // Updating an existing record.
    if (!$DB->record_exists('local_mxschool_weekend_form', array('id' => $id))) {
        redirect_to_fallback();
    }
    if ($isstudent) { // Students cannot edit existing weekend forms.
        redirect($PAGE->url);
    }
    $data = get_record($queryfields, "wf.id = ?", array($id));
    $data->dorm = $DB->get_field('local_mxschool_student', 'dormid', array('userid' => $data->student));
} else { // Creating a new record.
    $data = new stdClass();
    $data->id = $id;
    $data->timecreated = $data->departure_date = $data->return_date = time();
    if ($isstudent) {
        $data->student = $USER->id;
        $data->dorm = $DB->get_field('local_mxschool_student', 'dormid', array('userid' => $USER->id));
    } else {
        $dorm = $DB->get_field('local_mxschool_faculty', 'dormid', array('userid' => $USER->id));
        if ($dorm) {
            $data->dorm = $dorm;
        }
    }
    if (isset($data->dorm)) {
        $record = $DB->get_record_select(
            'local_mxschool_dorm', 'id = ? AND deleted = 0', array($data->dorm), 'hohid AS hoh, permissions_line AS permissionsline'
        );
    }
}
$data->isstudent = $isstudent ? '1' : '0';
$data->warning = get_config('local_mxschool', 'weekend_form_warning_closed');
generate_time_selector_fields($data, 'departure', 15);
generate_time_selector_fields($data, 'return', 15);
$dorms = array('0' => get_string('checkin:weekend_form:dorm:default', 'local_mxschool')) + get_dorm_list(false);
$students = get_boarding_student_list();

$form = new local_mxschool\local\checkin\weekend_form(array('dorms' => $dorms, 'students' => $students));
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    $data->timemodified = time();
    $data->departure_date = generate_timestamp($data, 'departure');
    $data->return_date = generate_timestamp($data, 'return');
    $departurestartbound = generate_datetime($data->departure_date);
    $departureendbound = clone $departurestartbound;
    $departurestartbound->modify('+4 days'); // Map 0:00:00 Wednesday to 0:00:00 Sunday.
    $departureendbound->modify('-3 days'); // Map 0:00:00 Tuesday to 0:00:00 Sunday.
    $data->weekend = $DB->get_field_select(
        'local_mxschool_weekend', 'id', '? >= sunday_time AND ? < sunday_time',
        array($departurestartbound->getTimestamp(), $departureendbound->getTimestamp())
    );
    $id = update_record($queryfields, $data);
    $DB->set_field_select(
        'local_mxschool_weekend_form', 'active', 0, 'userid = ? AND weekendid = ? AND id <> ? AND active = 1',
        array($data->student, $data->weekend, $id)
    );
    $result = (new local_mxschool\local\checkin\weekend_form_submitted($id))->send();
    logged_redirect(
        $form->get_redirect(), get_string('checkin:weekend_form:success', 'local_mxschool'), $data->id ? 'update' : 'create'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$bottominstructions = get_config('local_mxschool', 'weekend_form_instructions_bottom');
if (isset($record)) {
    $bottominstructions = str_replace('{hoh}', format_faculty_name($record->hoh, false), $bottominstructions);
    $bottominstructions = str_replace('{permissionsline}', $record->permissionsline, $bottominstructions);
}
$renderable = new local_mxschool\output\form(
    $form, get_config('local_mxschool', 'weekend_form_instructions_top'), $bottominstructions
);

echo $output->header();
echo $output->heading(
    $isstudent ? get_string('checkin:weekend_form:title', 'local_mxschool', format_student_name($USER->id)) : $PAGE->title
);
echo $output->render($renderable);
echo $output->footer();
