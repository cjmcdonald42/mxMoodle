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
require_once(__DIR__.'/../classes/mx_dropdown.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/events/page_visited.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:manage_students', context_system::instance());

$type = optional_param('type', 'students', PARAM_RAW);
$filter = new stdClass();
$filter->dorm = optional_param('dorm', '', PARAM_RAW);
$filter->search = optional_param('search', '', PARAM_RAW);
$action = optional_param('action', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('user_management', 'local_mxschool') => '/local/mxschool/user_management/index.php'
);
$url = '/local/mxschool/user_management/student_report.php';
$title = get_string('student_report', 'local_mxschool');
$types = array(
    'students' => get_string('student_report_type_students', 'local_mxschool'),
    'permissions' => get_string('student_report_type_permissions', 'local_mxschool'),
    'parents' => get_string('student_report_type_parents', 'local_mxschool')
);
$dorms = get_dorms_list();

if (!isset($types[$type])) {
    redirect(new moodle_url($url, array('type' => 'students', 'dorm' => $filter->dorm, 'search' => $filter->search)));
}
if ($type === 'parents' && $action === 'delete' && $id) {
    $record = $DB->get_record('local_mxschool_parent', array('id' => $id));
    if ($record) {
        $record->deleted = 1;
        if ($record->is_primary_parent === 'Yes') { // Each student must have a primary parent.
            $record->is_primary_parent = 'No';
            $newprimary = $DB->get_record_sql(
                "SELECT id, is_primary_parent FROM {local_mxschool_parent} WHERE userid = ? AND id != ? AND deleted = 0",
                array($record->userid, $record->id), IGNORE_MULTIPLE
            );
            if ($newprimary) {
                $newprimary->is_primary_parent = 'Yes';
                $DB->update_record('local_mxschool_parent', $newprimary);
            }
        }
        $DB->update_record('local_mxschool_parent', $record);
        redirect(
            new moodle_url($url, array('type' => $type, 'dorm' => $filter->dorm, 'search' => $filter->search)),
            get_string('parent_delete_success', 'local_mxschool'), null, \core\output\notification::NOTIFY_SUCCESS
        );
    } else {
        redirect(
            new moodle_url($url, array('type' => $type, 'dorm' => $filter->dorm, 'search' => $filter->search)),
            get_string('parent_delete_failure', 'local_mxschool'), null, \core\output\notification::NOTIFY_WARNING
        );
    }
}

$event = \local_mxschool\event\page_visited::create(array('other' => array('page' => $title)));
$event->trigger();

$PAGE->set_url(new moodle_url($url));
$PAGE->set_context(context_system::instance());
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('incourse');
foreach ($parents as $display => $url) {
    $PAGE->navbar->add($display, new moodle_url($url));
}
$PAGE->navbar->add($title);

$table = new student_table('student_table', $type, $filter);

$typeselect = new local_mxschool_dropdown('type', $types, $type);
$dormselect = new local_mxschool_dropdown('dorm', $dorms, $filter->dorm, get_string('report_select_dorm', 'local_mxschool'));
$addbutton = array(
    'text' => get_string('parent_report_add', 'local_mxschool'),
    'url' => new moodle_url('/local/mxschool/user_management/parent_edit.php')
);

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\report_page(
    'student-report', $table, 50, $filter->search, array($typeselect, $dormselect), $type !== 'parents',
    $type === 'parents' ? $addbutton : false
);

echo $output->header();
echo $output->heading($types[$type].($filter->dorm ? " - {$dorms[$filter->dorm]}" : ''));
echo $output->render($renderable);
echo $output->footer();
