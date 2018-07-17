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
 * Weekend checkin sheet for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/mx_dropdown.php');
require_once('weekend_table.php');
require_once('weekend_comment_form.php');

require_login();
require_capability('local/mxschool:manage_weekend', context_system::instance());

$filter = new stdClass();
$filter->dorm = get_param_faculty_dorm();
$filter->weekend = get_param_current_weekend();
$filter->submitted = optional_param('submitted', '', PARAM_RAW);
$filter->search = optional_param('search', '', PARAM_RAW);
$action = optional_param('action', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('checkin', 'local_mxschool') => '/local/mxschool/checkin/index.php'
);
$url = '/local/mxschool/checkin/weekend_report.php';
$title = get_string('weekend_report', 'local_mxschool');

setup_mxschool_page($url, $title, $parents);

$queryfields = array('local_mxschool_comment' => array('abbreviation' => 'c', 'fields' => array(
    'id', 'weekendid' => 'weekend', 'dormid' => 'dorm', 'comment'
)));

if ($action === 'delete' && $id) {
    $record = $DB->get_record('local_mxschool_weekend_form', array('id' => $id));
    $urlparams = array(
        'dorm' => $filter->dorm, 'weekend' => $filter->weekend, 'submitted' => $filter->submitted, 'search' => $filter->search
    );
    if ($record) {
        $record->active = 0;
        $DB->update_record('local_mxschool_weekend_form', $record);
        redirect(
            new moodle_url($url, $urlparams), get_string('weekend_form_delete_success', 'local_mxschool'), null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } else {
        redirect(
            new moodle_url($url, $urlparams), get_string('weekend_form_delete_failure', 'local_mxschool'), null,
            \core\output\notification::NOTIFY_WARNING
        );
    }
}
$data = get_record($queryfields, "c.weekendid = ? AND c.dormid = ?", array($filter->weekend, $filter->dorm));
if (!$data) {
    $data = new stdClass();
    $data->weekend = $filter->weekend;
    $data->dorm = $filter->dorm;
}

$dorms = get_boarding_dorm_list();
$weekends = get_weekend_list();
$submittedoptions = array(
    '1' => get_string('weekend_report_select_submitted_true', 'local_mxschool'),
    '0' => get_string('weekend_report_select_submitted_false', 'local_mxschool')
);
$weekendrecord = $DB->get_record('local_mxschool_weekend', array('id' => $filter->weekend));
$startday = date('w', $weekendrecord->start_time) - 7;
$endday = date('w', $weekendrecord->end_time);

$table = new weekend_table($filter);

$form = new weekend_comment_form(array('id' => $id));
$form->set_redirect(new moodle_url($url, array(
    'dorm' => $filter->dorm, 'weekend' => $filter->weekend, 'submitted' => $filter->submitted, 'search' => $filter->search
)), true);
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    update_record($queryfields, $data);
    redirect(
        $form->get_redirect(), get_string('weekend_comment_form_success', 'local_mxschool'), null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

$dropdowns = array(
    new local_mxschool_dropdown('dorm', $dorms, $filter->dorm, get_string('report_select_boarding_dorm', 'local_mxschool')),
    new local_mxschool_dropdown('weekend', $weekends, $filter->weekend),
    new local_mxschool_dropdown(
        'submitted', $submittedoptions, $filter->submitted, get_string('report_select_default', 'local_mxschool')
    )
);
$addbutton = new stdClass();
$addbutton->text = get_string('weekend_report_add', 'local_mxschool');
$addbutton->url = new moodle_url('/local/mxschool/checkin/weekend_enter.php');
$headers = array(array(
    'text' => '', 'length' => $filter->dorm ? (
        $DB->get_field('local_mxschool_dorm', 'type', array('id' => $filter->dorm)
    ) === 'Day' ? 2 : 3) : 4
));
for ($i = $startday; $i <= $endday; $i++) {
    $day = ($i + 7) % 7;
    $headers[] = array('text' => get_string("day_$day", 'local_mxschool'), 'length' => 2);
}
$headers[] = array('text' => '', 'length' => 9);

$output = $PAGE->get_renderer('local_mxschool');
$reportrenderable = new \local_mxschool\output\report($table, 50, $filter->search, $dropdowns, true, $addbutton, false, $headers);
$formrenderable = new \local_mxschool\output\form($form);

echo $output->header();
echo $output->heading(get_string('weekend_report_title', 'local_mxschool', array(
    'dorm' => $filter->dorm ? "{$dorms[$filter->dorm]} " : '', 'weekend' => $weekends[$filter->weekend],
    'type' => $weekendrecord->type
)));
echo $output->render($reportrenderable);
echo $output->render($formrenderable);
echo $output->footer();
