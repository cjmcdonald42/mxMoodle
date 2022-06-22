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
 * Student management, permissions, and parents report for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  user_management
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

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
    'students' => get_string('user_management:student_report:type:students', 'local_mxschool'),
    'permissions' => get_string('user_management:student_report:type:permissions', 'local_mxschool'),
    'parents' => get_string('user_management:student_report:type:parents', 'local_mxschool')
);

if (!isset($types[$filter->type])) {
    unset($filter->type);
    redirect(new moodle_url($PAGE->url, (array) $filter));
}
if ($filter->type === 'parents' && $action === 'delete' && $id) {
    $result = $DB->record_exists('local_mxschool_parent', array('id' => $id)) ? 'success' : 'failure';
    $DB->set_field('local_mxschool_parent', 'deleted', 1, array('id' => $id));
    logged_redirect(
        new moodle_url($PAGE->url, (array) $filter), get_string("user_management:parent:delete:{$result}", 'local_mxschool'),
        'delete', $result === 'success'
    );
}
$table = new local_mxschool\local\user_management\student_table($filter);
$dropdowns = array(
    new local_mxschool\output\dropdown('type', $types, $filter->type), local_mxschool\output\dropdown::dorm_dropdown($filter->dorm)
);
$buttons = $filter->type === 'parents' ? $buttons = array(new local_mxschool\output\redirect_button(
    get_string('user_management:student_report:add_parent', 'local_mxschool'),
    new moodle_url('/local/mxschool/user_management/parent_edit.php')
)) : array();

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\report($table, $filter->search, $dropdowns, $buttons, $filter->type !== 'parents');

echo $output->header();
echo $output->heading(($filter->dorm > 0 ? format_dorm_name($filter->dorm) . ' ' : '') . $types[$filter->type]);
echo $output->render($renderable);
echo $output->footer();
