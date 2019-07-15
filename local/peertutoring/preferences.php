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
 * Preferences page for Middlesex School's Peer Tutoring Subplugin.
 *
 * @package    local_peertutoring
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/../mxschool/locallib.php');
require_once(__DIR__.'/../mxschool/classes/output/renderable.php');
require_once(__DIR__.'/locallib.php');
require_once(__DIR__.'/preferences_form.php');
require_once(__DIR__.'/tutor_table.php');
require_once(__DIR__.'/department_table.php');
require_once(__DIR__.'/course_table.php');
require_once(__DIR__.'/type_table.php');
require_once(__DIR__.'/rating_table.php');

require_login();
require_capability('local/peertutoring:manage_preferences', context_system::instance());

$action = optional_param('action', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);
$table = optional_param('table', '', PARAM_RAW);

setup_mxschool_page('preferences', null, 'peertutoring');

if ($action === 'delete' && $id && $table) {
    switch ($table) {
        case 'tutor':
            $dbtable = 'local_peertutoring_tutor';
            break;
        case 'department':
            $dbtable = 'local_peertutoring_dept';
            break;
        case 'course':
            $dbtable = 'local_peertutoring_course';
            break;
        case 'type':
            $dbtable = 'local_peertutoring_type';
            break;
        case 'rating':
            $dbtable = 'local_peertutoring_rating';
            break;
        default:
            logged_redirect($PAGE->url, get_string('table_delete_failure', 'local_peertutoring'), 'delete', false);
    }
    $record = $DB->get_record($dbtable, array('id' => $id));
    if ($record) {
        $record->deleted = 1;
        $DB->update_record($dbtable, $record);
        logged_redirect($PAGE->url, get_string("{$table}_delete_success", 'local_peertutoring'), 'delete');
    } else {
        logged_redirect($PAGE->url, get_string("{$table}_delete_failure", 'local_peertutoring'), 'delete', false);
    }
}

$data = new stdClass();
$notification = $DB->get_record('local_mxschool_notification', array('class' => 'peer_tutor_summary'));
if ($notification) {
    $data->subject = $notification->subject;
    $data->body['text'] = $notification->body_html;
}

$form = new preferences_form();
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    update_notification('peer_tutor_summary', $data->subject, $data->body);
    logged_redirect(
        $form->get_redirect(), get_string('preferences_edit_success', 'local_peertutoring'), 'update'
    );
}

$tutortable = new tutor_table();
$departmenttable = new department_table();
$coursetable = new course_table();
$typetable = new type_table();
$ratingtable = new rating_table();

$tutoradd = new stdClass();
$tutoradd->text = get_string('tutor_report_add', 'local_peertutoring');
$tutoradd->url = new moodle_url('/local/peertutoring/tutor_edit.php');
$departmentadd = new stdClass();
$departmentadd->text = get_string('department_report_add', 'local_peertutoring');
$departmentadd->url = new moodle_url('/local/peertutoring/department_edit.php');
$courseadd = new stdClass();
$courseadd->text = get_string('course_report_add', 'local_peertutoring');
$courseadd->url = new moodle_url('/local/peertutoring/course_edit.php');
$typeadd = new stdClass();
$typeadd->text = get_string('type_report_add', 'local_peertutoring');
$typeadd->url = new moodle_url('/local/peertutoring/type_edit.php');
$ratingadd = new stdClass();
$ratingadd->text = get_string('rating_report_add', 'local_peertutoring');
$ratingadd->url = new moodle_url('/local/peertutoring/rating_edit.php');

$output = $PAGE->get_renderer('local_mxschool');
$formrenderable = new \local_mxschool\output\form($form);
$tutorrenderable = new \local_mxschool\output\report($tutortable, null, array(), false, $tutoradd);
$departmentrenderable = new \local_mxschool\output\report($departmenttable, null, array(), false, $departmentadd);
$courserenderable = new \local_mxschool\output\report($coursetable, null, array(), false, $courseadd);
$typerenderable = new \local_mxschool\output\report($typetable, null, array(), false, $typeadd);
$ratingrenderable = new \local_mxschool\output\report($ratingtable, null, array(), false, $ratingadd);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($formrenderable);
echo $output->heading(get_string('tutor_report', 'local_peertutoring'));
echo $output->render($tutorrenderable);
echo $output->heading(get_string('department_report', 'local_peertutoring'));
echo $output->render($departmentrenderable);
echo $output->heading(get_string('course_report', 'local_peertutoring'));
echo $output->render($courserenderable);
echo $output->heading(get_string('type_report', 'local_peertutoring'));
echo $output->render($typerenderable);
echo $output->heading(get_string('rating_report', 'local_peertutoring'));
echo $output->render($ratingrenderable);
echo $output->footer();
