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
 * Advisor selection report for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  advisor_selection
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:manage_advisor_selection', context_system::instance());

$filter = new stdClass();
$filter->submitted = optional_param('submitted', '', PARAM_RAW);
$filter->keepcurrent = optional_param('keepcurrent', '', PARAM_RAW);
$filter->search = optional_param('search', '', PARAM_RAW);
$download = optional_param('download', '', PARAM_ALPHA);

setup_mxschool_page('report', 'advisor_selection');

$submittedoptions = array(
    '1' => get_string('advisor_selection_report_select_submitted_true', 'local_mxschool'),
    '0' => get_string('advisor_selection_report_select_submitted_false', 'local_mxschool')
);
$keepcurrentoptions = array(
    '1' => get_string('advisor_selection_report_select_keepcurrent_true', 'local_mxschool'),
    '0' => get_string('advisor_selection_report_select_keepcurrent_false', 'local_mxschool')
);

$table = new local_mxschool\local\advisor_selection\table($filter, $download);
$dropdowns = array(
    new local_mxschool\output\dropdown(
        'submitted', $submittedoptions, $filter->submitted,
        get_string('dropdown:default', 'local_mxschool')
    ),
    new local_mxschool\output\dropdown(
        'keepcurrent', $keepcurrentoptions, $filter->keepcurrent,
        get_string('dropdown:default', 'local_mxschool')
    )
);
$buttons = array(
    new local_mxschool\output\redirect_button(
        get_string('advisor_selection_report_add', 'local_mxschool'), new moodle_url('/local/mxschool/advisor_selection/form.php')
    ),
    new local_mxschool\output\email_button(
        get_string('advisor_selection_report_remind', 'local_mxschool'), 'advisor_selection_notify_unsubmitted'
    ),
    new local_mxschool\output\email_button(
        get_string('advisor_selection_report_results', 'local_mxschool'), 'advisor_selection_notify_results'
    )
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
