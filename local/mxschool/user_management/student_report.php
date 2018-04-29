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
 * Student management report for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once('student_table.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:view_users', context_system::instance());

$filter = new stdClass();
$type = optional_param('type', 'students', PARAM_RAW);
$filter->dorm = optional_param('dorm', '', PARAM_RAW);
$filter->search = optional_param('search', '', PARAM_RAW);
$action = optional_param('action', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);

$url = '/local/mxschool/user_management/student_report.php';
$title = get_string('student_report', 'local_mxschool');

if ($action == 'delete' and $id) {
    $userid = $DB->get_field('local_mxschool_student', 'userid', array('id' => $id));
    $user = $DB->get_record('user', array('id' => $userid));
    if ($user) {
        delete_user($user);
    }
    redirect(new moodle_url($url, array('type' => $type, 'dorm' => $filter->dorm, 'search' => $filter->search)));
}

$PAGE->set_url(new moodle_url($url));
$PAGE->set_context(context_system::instance());
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('incourse');
$PAGE->navbar->add(get_string('pluginname', 'local_mxschool'), new moodle_url('/local/mxschool/index.php'));
$PAGE->navbar->add(get_string('user_management', 'local_mxschool'), new moodle_url('/local/mxschool/user_management/index.php'));
$PAGE->navbar->add($title);

$table = new student_table('student_table', $type, $filter);

$typeselect = new stdClass();
$typeselect->name = 'type';
$typeselect->options = array(
    'students' => get_string('student_report_type_students', 'local_mxschool'),
    'permissions' => get_string('student_report_type_permissions', 'local_mxschool'),
    'parents' => get_string('student_report_type_parents', 'local_mxschool')
);
$typeselect->selected = $type;
$typeselect->default = false;

$dormselect = new stdClass();
$dormselect->name = 'dorm';
$dormselect->options = get_dorms_list();
$dormselect->selected = $filter->dorm;
$dormselect->default = array('' => get_string('report_select_dorm', 'local_mxschool'));

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\report_page($table, 50, array($typeselect, $dormselect), $filter->search);

echo $output->header();
echo $output->heading($title);
echo $output->render($renderable);
echo $output->footer();
