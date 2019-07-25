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
 * Vacation travel report for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  vacation_travel
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:manage_vacation_travel', context_system::instance());

$filter = new stdClass();
$filter->dorm = get_param_faculty_dorm(false);
$filter->submitted = optional_param('submitted', '', PARAM_RAW);
$filter->search = optional_param('search', '', PARAM_RAW);

setup_mxschool_page('report', 'vacation_travel');

$submittedoptions = array(
    '1' => get_string('vacation_travel_report_select_submitted_true', 'local_mxschool'),
    '0' => get_string('vacation_travel_report_select_submitted_false', 'local_mxschool')
);

$table = new local_mxschool\local\vacation_travel\table($filter);
$dropdowns = array(
   local_mxschool\output\dropdown::dorm_dropdown($filter->dorm, false),
    new local_mxschool\output\dropdown(
        'submitted', $submittedoptions, $filter->submitted, get_string('report_select_default', 'local_mxschool')
    )
);
$buttons = array(new local_mxschool\output\redirect_button(
    get_string('vacation_travel_report_add', 'local_mxschool'),
    new moodle_url('/local/mxschool/vacation_travel/vacation_enter.php')
));
if (has_capability('local/mxschool:notify_vacation_travel', context_system::instance())) {
    $buttons[] = new local_mxschool\output\email_button(
        get_string('vacation_travel_report_remind', 'local_mxschool'), 'vacation_travel_notify_unsubmitted'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\report($table, $filter->search, $dropdowns, $buttons, true);

echo $output->header();
echo $output->heading(
    get_string('vacation_travel_report_title', 'local_mxschool', $filter->dorm ? format_dorm_name($filter->dorm) . ' ' : '')
);
echo $output->render($renderable);
echo $output->footer();
