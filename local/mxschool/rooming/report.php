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
 * Rooming report for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  rooming
 * @author      mxMoodle Development Team
 * @copyright   2022 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:manage_rooming', context_system::instance());

$filter = new stdClass();
$filter->submitted = optional_param('submitted', '', PARAM_RAW);
$filter->gender = optional_param('gender', '', PARAM_RAW);
$filter->roomtype = optional_param('roomtype', '', PARAM_RAW);
$filter->double = optional_param('double', '', PARAM_RAW);
$filter->search = optional_param('search', '', PARAM_RAW);
$download = optional_param('download', '', PARAM_ALPHA);

setup_mxschool_page('report', 'rooming');

$submittedoptions = array(
    '1' => get_string('rooming:report:select_submitted:true', 'local_mxschool'),
    '0' => get_string('rooming:report:select_submitted:false', 'local_mxschool')
);
$genderoptions = array(
    'M' => get_string('rooming:report:select_gender:M', 'local_mxschool'),
    'F' => get_string('rooming:report:select_gender:F', 'local_mxschool'),
    'N' => get_string('rooming:report:select_gender:N', 'local_mxschool')
);
$roomtypeoptions = get_room_type_list();
$doubleoptions = array(
    '1' => get_string('rooming:report:select_double:true', 'local_mxschool'),
    '0' => get_string('rooming:report:select_double:false', 'local_mxschool')
);

$table = new local_mxschool\local\rooming\table($filter, $download);
$dropdowns = array(
    new local_mxschool\output\dropdown(
        'submitted', $submittedoptions, $filter->submitted, get_string('rooming:report:select_submitted:all', 'local_mxschool')
    ),
    new local_mxschool\output\dropdown(
        'gender', $genderoptions, $filter->gender, get_string('rooming:report:select_gender:all', 'local_mxschool')
    ),
    new local_mxschool\output\dropdown(
        'roomtype', $roomtypeoptions, $filter->roomtype, get_string('rooming:report:select_roomtype:all', 'local_mxschool')
    ),
    new local_mxschool\output\dropdown(
        'double', $doubleoptions, $filter->double, get_string('dropdown:default', 'local_mxschool')
    )
);
$buttons = array(
    new local_mxschool\output\redirect_button(
        get_string('rooming:report:add', 'local_mxschool'), new moodle_url('/local/mxschool/rooming/form.php')
    ),
    new local_mxschool\output\email_button(get_string('rooming:report:remind', 'local_mxschool'), 'rooming_notify_unsubmitted')
);

$output = $PAGE->get_renderer('local_mxschool');
if ($table->is_downloading()) {
    $renderable = new local_mxschool\output\report_table($table);
    echo $output->render($renderable);
    die();
}
$renderable = new local_mxschool\output\report($table, $filter->search, $dropdowns, $buttons);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
