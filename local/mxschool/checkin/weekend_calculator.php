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
 * Weekend calculator report for Middlesex School's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage checkin
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/mx_dropdown.php');
require_once('weekend_calculator_table.php');
require_login();
$isstudent = user_is_student();
if (!$isstudent) {
    require_capability('local/mxschool:manage_weekend', context_system::instance());
}

$filter = new stdClass();
$filter->dorm = get_param_faculty_dorm();
$filter->semester = get_param_current_semester();

setup_mxschool_page('weekend_calculator', 'checkin');

$dorms = get_boarding_dorm_list();
$semesters = array('1' => get_string('first_semester', 'local_mxschool'), '2' => get_string('second_semester', 'local_mxschool'));
$startdate = get_config('local_mxschool', $filter->semester == 1 ? 'dorms_open_date' : 'second_semester_start_date');
$enddate = get_config('local_mxschool', $filter->semester == 1 ? 'second_semester_start_date' : 'dorms_close_date');
$weekends = $DB->get_records_sql(
    "SELECT id, sunday_time FROM {local_mxschool_weekend} WHERE sunday_time >= ? AND sunday_time < ? AND type <> 'Vacation'
     ORDER BY sunday_time", array($startdate, $enddate)
);

$table = new weekend_calculator_table($filter, $weekends, $isstudent);

$dropdowns = $isstudent ? array() : array(
    new local_mxschool_dropdown('dorm', $dorms, $filter->dorm, get_string('report_select_boarding_dorm', 'local_mxschool'))
);
$dropdowns[] = new local_mxschool_dropdown('semester', $semesters, $filter->semester);
$rows = array(
    array(
        'lefttext' => get_string('checkin_weekend_calculator_abbreviation_offcampus', 'local_mxschool'),
        'righttext' => get_string('checkin_weekend_calculator_legend_offcampus', 'local_mxschool')
    ),
    array('righttext' => get_string('checkin_weekend_calculator_legend_3_left', 'local_mxschool')),
    array('leftclass' => 'mx-green', 'righttext' => get_string('checkin_weekend_calculator_legend_2_left', 'local_mxschool')),
    array('leftclass' => 'mx-yellow', 'righttext' => get_string('checkin_weekend_calculator_legend_1_left', 'local_mxschool')),
    array('leftclass' => 'mx-red', 'righttext' => get_string('checkin_weekend_calculator_legend_0_left', 'local_mxschool')),
    array(
        'lefttext' => get_string('checkin_weekend_calculator_abbreviation_free', 'local_mxschool'),
        'righttext' => get_string('checkin_weekend_calculator_legend_free', 'local_mxschool')
    ),
    array(
        'lefttext' => get_string('checkin_weekend_calculator_abbreviation_closed', 'local_mxschool'),
        'righttext' => get_string('checkin_weekend_calculator_legend_closed', 'local_mxschool')
    )
);

$output = $PAGE->get_renderer('local_mxschool');
$reportrenderable = new \local_mxschool\output\report($table, null, $dropdowns, true);
$legendrenderable = new \local_mxschool\output\legend_table($rows);
$jsrenderable = new \local_mxschool\output\amd_module('local_mxschool/highlight_cells');

echo $output->header();
echo $output->heading(
    get_string('checkin_weekend_calculator_report_title', 'local_mxschool', $filter->dorm ? " for {$dorms[$filter->dorm]}" : '')
);
echo $output->render($reportrenderable);
echo $output->render($legendrenderable);
echo $output->render($jsrenderable);
echo $output->footer();
