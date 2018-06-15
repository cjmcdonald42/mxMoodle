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
 * Weekend calculator report for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once('weekend_calculator_table.php');
require_once(__DIR__.'/../classes/mx_dropdown.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/events/page_visited.php');
require_once(__DIR__.'/../locallib.php');

require_login();
$isstudent = user_is_student();
if (!$isstudent) {
    require_capability('local/mxschool:manage_weekend', context_system::instance());
}

$filter = new stdClass();
$filter->dorm = get_param_faculty_dorm();
$filter->semester = get_param_current_semester();

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('checkin', 'local_mxschool') => '/local/mxschool/checkin/index.php'
);
$url = '/local/mxschool/checkin/weekend_calculator.php';
$title = get_string('weekend_calculator', 'local_mxschool');

$dorms = get_dorms_list();
$semesters = array('1' => get_string('first_semester', 'local_mxschool'), '2' => get_string('second_semester', 'local_mxschool'));
$startdate = $filter->semester == 1 ? get_config('local_mxschool', 'dorms_open_date')
                                    : get_config('local_mxschool', 'second_semester_start_date');
$enddate = $filter->semester == 1 ? get_config('local_mxschool', 'second_semester_start_date')
                                  : get_config('local_mxschool', 'dorms_close_date');
$weekends = $DB->get_records_sql(
    "SELECT id, sunday_time FROM {local_mxschool_weekend} WHERE sunday_time >= ? AND sunday_time < ? AND type <> 'Vacation'
     ORDER BY sunday_time", array($startdate, $enddate)
);

$event = \local_mxschool\event\page_visited::create(array('other' => array('page' => $title)));
$event->trigger();

$PAGE->set_url(new moodle_url($url));
$PAGE->set_context(context_system::instance());
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('incourse');
foreach ($parents as $display => $parenturl) {
    $PAGE->navbar->add($display, new moodle_url($parenturl));
}
$PAGE->navbar->add($title);

$table = new weekend_calculator_table('weekend_calculator_table', $filter, $weekends, $isstudent);

$dropdowns = $isstudent ? array() : array(
    new local_mxschool_dropdown('dorm', $dorms, $filter->dorm, get_string('report_select_dorm', 'local_mxschool'))
);
$dropdowns[] = new local_mxschool_dropdown('semester', $semesters, $filter->semester);
$highlight = new stdClass();
$highlight->formatcolumn = 2 + count($weekends);
$highlight->referencecolumn = 3 + count($weekends);
$rows = array(
    array(
        'lefttext' => get_string('weekend_report_abbreviation_offcampus', 'local_mxschool'),
        'righttext' => get_string('weekend_report_legend_offcampus', 'local_mxschool')
    ), array('righttext' => get_string('weekend_report_legend_3_left', 'local_mxschool')),
    array('leftclass' => 'green', 'righttext' => get_string('weekend_report_legend_2_left', 'local_mxschool')),
    array('leftclass' => 'yellow', 'righttext' => get_string('weekend_report_legend_1_left', 'local_mxschool')),
    array('leftclass' => 'red', 'righttext' => get_string('weekend_report_legend_0_left', 'local_mxschool')),
    array(
        'lefttext' => get_string('weekend_report_abbreviation_free', 'local_mxschool'),
        'righttext' => get_string('weekend_report_legend_free', 'local_mxschool')
    ), array(
        'lefttext' => get_string('weekend_report_abbreviation_closed', 'local_mxschool'),
        'righttext' => get_string('weekend_report_legend_closed', 'local_mxschool')
    )
);

$output = $PAGE->get_renderer('local_mxschool');
$reportrenderable = new \local_mxschool\output\report_page(
    'weekend-calculator', $table, 50, null, $dropdowns, true, false, false, $highlight
);
$legendrenderable = new \local_mxschool\output\legend_table($rows);

echo $output->header();
echo $output->heading(
    get_string('weekend_calculator_report_title', 'local_mxschool', $filter->dorm ? " for {$dorms[$filter->dorm]}" : '')
);
echo $output->render($reportrenderable);
echo $output->render($legendrenderable);
echo $output->footer();
