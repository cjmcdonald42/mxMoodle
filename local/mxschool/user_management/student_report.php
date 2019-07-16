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
 * Student management report for Middlesex School's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage user_management
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/mx_dropdown.php');
require_once(__DIR__.'/student_table.php');

require_login();
require_capability('local/mxschool:manage_students', context_system::instance());

$filter = new stdClass();
$filter->type = optional_param('type', 'students', PARAM_RAW);
$filter->dorm = get_param_faculty_dorm();
$filter->search = optional_param('search', '', PARAM_RAW);
$action = optional_param('action', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);

setup_mxschool_page('student_report', 'user_management');

$types = array(
    'students' => get_string('user_management_student_report_type_students', 'local_mxschool'),
    'permissions' => get_string('user_management_student_report_type_permissions', 'local_mxschool'),
    'parents' => get_string('user_management_student_report_type_parents', 'local_mxschool')
);

if (!isset($types[$filter->type])) {
    unset($filter->type);
    redirect(new moodle_url($PAGE->url, (array) $filter));
}
if ($filter->type === 'parents' && $action === 'delete' && $id) {
    $record = $DB->get_record('local_mxschool_parent', array('id' => $id));
    $redirect = new moodle_url($PAGE->url, (array) $filter);
    if ($record) {
        $record->deleted = 1;
        if ($record->is_primary_parent) { // Each student must have a primary parent.
            $record->is_primary_parent = false;
            $newprimary = $DB->get_record_sql(
                "SELECT id, is_primary_parent
                 FROM {local_mxschool_parent}
                 WHERE userid = ? AND id <> ? AND deleted = 0", array($record->userid, $record->id), IGNORE_MULTIPLE
            );
            if ($newprimary) {
                $newprimary->is_primary_parent = true;
                $DB->update_record('local_mxschool_parent', $newprimary);
            }
        }
        $DB->update_record('local_mxschool_parent', $record);
        logged_redirect($redirect, get_string('user_management_parent_delete_success', 'local_mxschool'), 'delete');
    } else {
        logged_redirect($redirect, get_string('user_management_parent_delete_failure', 'local_mxschool'), 'delete', false);
    }
}

$dorms = get_dorm_list();

$table = new student_table($filter);

$dropdowns = array(
    new local_mxschool_dropdown('type', $types, $filter->type), local_mxschool_dropdown::dorm_dropdown($filter->dorm)
);
if ($filter->type === 'parents') {
    $addbutton = new stdClass();
    $addbutton->text = get_string('user_management_parent_report_add', 'local_mxschool');
    $addbutton->url = new moodle_url('/local/mxschool/user_management/parent_edit.php');
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\report(
    $table, $filter->search, $dropdowns, $filter->type !== 'parents', $addbutton ?? false
);

echo $output->header();
echo $output->heading(($filter->dorm ? "{$dorms[$filter->dorm]} " : '') . $types[$filter->type]);
echo $output->render($renderable);
echo $output->footer();
