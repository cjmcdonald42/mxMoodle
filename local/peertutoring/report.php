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
 * Tutoring Report for Middlesex's Peer Tutoring Subplugin.
 *
 * @package     local_peertutoring
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/locallib.php');

require_login();
require_capability('local/peertutoring:manage_tutoring', context_system::instance());

$filter = new stdClass();
$filter->tutor = optional_param('tutor', 0, PARAM_INT);
$filter->advisor = get_param_faculty_advisor();
$filter->department = optional_param('department', 0, PARAM_INT);
$filter->type = optional_param('type', 0, PARAM_INT);
$filter->date = optional_param('date', 0, PARAM_INT);
$filter->search = optional_param('search', '', PARAM_RAW);
$action = optional_param('action', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);

setup_mxschool_page('report', null, 'peertutoring');

$types = get_type_list() + array(-1 => get_string('report:select_type:other', 'local_peertutoring'));
if ($filter->type && !isset($types[$filter->type])) {
    unset($filter->type);
    redirect(new moodle_url($PAGE->url, (array) $filter));
}
if ($action === 'delete' && $id) {
    $result = $DB->record_exists('local_peertutoring_session', array('id' => $id)) ? 'success' : 'failure';
    $DB->set_field('local_peertutoring_session', 'deleted', 1, array('id' => $id));
    logged_redirect(
        new moodle_url($PAGE->url, (array) $filter), get_string("session:delete:{$result}", 'local_peertutoring'), 'delete',
        $result === 'success'
    );
}

$advisors = get_advisor_list();
$tutors = get_tutor_list();
$departments = get_department_list();
$dates = get_tutoring_date_list();

$table = new local_peertutoring\local\table($filter, $download);
$dropdowns = array(
    new local_mxschool\output\dropdown(
        'date', $dates, $filter->date, get_string('report:select_date:all', 'local_peertutoring')
    ),
    new local_mxschool\output\dropdown(
        'advisor', $advisors, $filter->advisor, get_string('report:select_advisor:all', 'local_peertutoring')
    ),
    new local_mxschool\output\dropdown(
        'tutor', $tutors, $filter->tutor, get_string('report:select_tutor:all', 'local_peertutoring')
    ),
    new local_mxschool\output\dropdown(
        'department', $departments, $filter->department, get_string('report:select_department:all', 'local_peertutoring')
    ),
    new local_mxschool\output\dropdown(
        'type', $types, $filter->type, get_string('report:select_type:all', 'local_peertutoring')
    )
);
$buttons = array(new local_mxschool\output\redirect_button(
    get_string('report:add', 'local_peertutoring'), new moodle_url('/local/peertutoring/form.php')
));

$output = $PAGE->get_renderer('local_peertutoring');
if ($table->is_downloading()) {
    $renderable = new local_mxschool\output\report_table($table);
    echo $output->render($renderable);
    die();
}
$renderable = new local_mxschool\output\report($table, $filter->search, $dropdowns, $buttons, true);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
