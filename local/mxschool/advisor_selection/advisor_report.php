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
 * Advisor selection report for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage advisor_selection
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/mx_dropdown.php');
require_once('advisor_table.php');

require_login();
require_capability('local/mxschool:manage_advisor_selection', context_system::instance());

$filter = new stdClass();
$filter->submitted = optional_param('submitted', '', PARAM_RAW);
$filter->keepcurrent = optional_param('keepcurrent', '', PARAM_RAW);
$filter->search = optional_param('search', '', PARAM_RAW);
$download = optional_param('download', '', PARAM_ALPHA);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('advisor_selection', 'local_mxschool') => '/local/mxschool/advisor_selection/index.php'
);
$url = '/local/mxschool/advisor_selection/advisor_report.php';
$title = get_string('advisor_selection_report', 'local_mxschool');

setup_mxschool_page($url, $title, $parents);

$submittedoptions = array(
    '1' => get_string('advisor_selection_report_select_submitted_true', 'local_mxschool'),
    '0' => get_string('advisor_selection_report_select_submitted_false', 'local_mxschool')
);
$keepcurrentoptions = array(
    '1' => get_string('advisor_selection_report_select_keepcurrent_true', 'local_mxschool'),
    '0' => get_string('advisor_selection_report_select_keepcurrent_false', 'local_mxschool')
);

$table = new advisor_table($filter, $download);

$dropdowns = array(
    new local_mxschool_dropdown(
        'submitted', $submittedoptions, $filter->submitted,
        get_string('report_select_default', 'local_mxschool')
    ), new local_mxschool_dropdown(
        'keepcurrent', $keepcurrentoptions, $filter->keepcurrent,
        get_string('report_select_default', 'local_mxschool')
    )
);
$addbutton = new stdClass();
$addbutton->text = get_string('advisor_selection_report_add', 'local_mxschool');
$addbutton->url = new moodle_url('/local/mxschool/advisor_selection/advisor_enter.php');
$emailreminder = new stdClass();
$emailreminder->text = get_string('advisor_selection_report_remind', 'local_mxschool');
$emailreminder->emailclass = 'advisor_selection_notify_unsubmitted';
$emailresults = new stdClass();
$emailresults->text = get_string('advisor_selection_report_results', 'local_mxschool');
$emailresults->emailclass = 'advisor_selection_notify_results';
$emailbuttons = array($emailreminder, $emailresults);

$output = $PAGE->get_renderer('local_mxschool');
if ($table->is_downloading()) {
    $renderable = new \local_mxschool\output\report_table($table, $DB->count_records('local_mxschool_adv_selection'));
    echo $output->render($renderable);
    die();
}
$renderable = new \local_mxschool\output\report($table, 50, $filter->search, $dropdowns, false, $addbutton, $emailbuttons);

echo $output->header();
echo $output->heading($title);
echo $output->render($renderable);
echo $output->footer();
