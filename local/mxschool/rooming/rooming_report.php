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
 * Rooming report for Middlesex School's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage rooming
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/mx_dropdown.php');
require_once('rooming_table.php');

require_login();
require_capability('local/mxschool:manage_rooming', context_system::instance());

$filter = new stdClass();
$filter->submitted = optional_param('submitted', '', PARAM_RAW);
$filter->gender = optional_param('gender', '', PARAM_RAW);
$filter->roomtype = optional_param('roomtype', '', PARAM_RAW);
$filter->double = optional_param('double', '', PARAM_RAW);
$filter->search = optional_param('search', '', PARAM_RAW);
$download = optional_param('download', '', PARAM_ALPHA);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('rooming', 'local_mxschool') => '/local/mxschool/rooming/index.php'
);
$url = '/local/mxschool/rooming/rooming_report.php';
$title = get_string('rooming_report', 'local_mxschool');

setup_mxschool_page($url, $title, $parents);

$submittedoptions = array(
    '1' => get_string('rooming_report_select_submitted_true', 'local_mxschool'),
    '0' => get_string('rooming_report_select_submitted_false', 'local_mxschool')
);
$genderoptions = array(
    'M' => get_string('rooming_report_select_gender_M', 'local_mxschool'),
    'F' => get_string('rooming_report_select_gender_F', 'local_mxschool')
);
$roomtypeoptions = get_roomtype_list();
$doubleoptions = array(
    '1' => get_string('rooming_report_select_double_true', 'local_mxschool'),
    '0' => get_string('rooming_report_select_double_false', 'local_mxschool')
);

$table = new rooming_table($filter, $download);

$dropdowns = array(
    new local_mxschool_dropdown(
        'submitted', $submittedoptions, $filter->submitted, get_string('report_select_default', 'local_mxschool')
    ), new local_mxschool_dropdown(
        'gender', $genderoptions, $filter->gender, get_string('rooming_report_select_gender_all', 'local_mxschool')
    ), new local_mxschool_dropdown(
        'roomtype', $roomtypeoptions, $filter->roomtype, get_string('rooming_report_select_roomtype_all', 'local_mxschool')
    ), new local_mxschool_dropdown('double', $doubleoptions, $filter->double, get_string('report_select_default', 'local_mxschool'))
);
$addbutton = new stdClass();
$addbutton->text = get_string('rooming_report_add', 'local_mxschool');
$addbutton->url = new moodle_url('/local/mxschool/rooming/rooming_enter.php');
$emailbutton = new stdClass();
$emailbutton->text = get_string('rooming_report_remind', 'local_mxschool');
$emailbutton->emailclass = 'rooming_notify_unsubmitted';
$emailbuttons = array($emailbutton);

$output = $PAGE->get_renderer('local_mxschool');
if ($table->is_downloading()) {
    $renderable = new \local_mxschool\output\report_table($table);
    echo $output->render($renderable);
    die();
}
$renderable = new \local_mxschool\output\report($table, $filter->search, $dropdowns, false, $addbutton, $emailbuttons);

echo $output->header();
echo $output->heading($title);
echo $output->render($renderable);
echo $output->footer();
