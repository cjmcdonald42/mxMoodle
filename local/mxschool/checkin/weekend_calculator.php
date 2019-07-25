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
 * Weekend calculator report for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  checkin
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
$isstudent = user_is_student();
if (!$isstudent) {
    require_capability('local/mxschool:manage_weekend', context_system::instance());
}

$filter = new stdClass();
$filter->dorm = $isstudent ? '' : get_param_faculty_dorm(false);
$filter->semester = get_param_current_semester();

setup_mxschool_page('weekend_calculator', 'checkin');

$semesters = array('1' => get_string('first_semester', 'local_mxschool'), '2' => get_string('second_semester', 'local_mxschool'));
$startdate = get_config('local_mxschool', $filter->semester == 1 ? 'dorms_open_date' : 'second_semester_start_date');
$enddate = get_config('local_mxschool', $filter->semester == 1 ? 'second_semester_start_date' : 'dorms_close_date');
$weekends = $DB->get_records_sql(
    "SELECT id, sunday_time
     FROM {local_mxschool_weekend}
     WHERE sunday_time >= ? AND sunday_time < ? AND type <> 'Vacation'
     ORDER BY sunday_time", array($startdate, $enddate)
);

$table = new local_mxschool\local\checkin\weekend_calculator_table($filter, $weekends, $isstudent);
$dropdowns = array(new local_mxschool\output\dropdown('semester', $semesters, $filter->semester));
if (!$isstudent) {
    array_unshift($dropdowns, local_mxschool\output\dropdown::dorm_dropdown($filter->dorm, false));
}

$rows = array(
    array(
        'leftclass' => 'text-center',
        'lefttext' => get_string('checkin_weekend_calculator_abbreviation_offcampus', 'local_mxschool'),
        'righttext' => get_string('checkin_weekend_calculator_legend_offcampus', 'local_mxschool')
    ),
    array('leftclass' => 'table-success', 'righttext' => get_string('checkin_weekend_calculator_legend_3_left', 'local_mxschool')),
    array('leftclass' => 'table-info', 'righttext' => get_string('checkin_weekend_calculator_legend_2_left', 'local_mxschool')),
    array('leftclass' => 'table-warning', 'righttext' => get_string('checkin_weekend_calculator_legend_1_left', 'local_mxschool')),
    array('leftclass' => 'table-danger', 'righttext' => get_string('checkin_weekend_calculator_legend_0_left', 'local_mxschool')),
    array(
        'leftclass' => 'text-center', 'lefttext' => get_string('checkin_weekend_calculator_abbreviation_free', 'local_mxschool'),
        'righttext' => get_string('checkin_weekend_calculator_legend_free', 'local_mxschool')
    ),
    array(
        'leftclass' => 'text-center', 'lefttext' => get_string('checkin_weekend_calculator_abbreviation_closed', 'local_mxschool'),
        'righttext' => get_string('checkin_weekend_calculator_legend_closed', 'local_mxschool')
    )
);

$output = $PAGE->get_renderer('local_mxschool');
$reportrenderable = new local_mxschool\output\report($table, null, $dropdowns, array(), true);
$legendrenderable = new local_mxschool\output\legend_table(
    get_string('checkin_weekend_calculator_legend_header', 'local_mxschool'), $rows
);
$jsrenderable = new local_mxschool\output\amd_module('local_mxschool/highlight_cells');

echo $output->header();
echo $output->heading(get_string(
    'checkin_weekend_calculator_report_title', 'local_mxschool', !empty($filter->dorm) ? format_dorm_name($filter->dorm) . ' ' : ''
));
echo $output->render($reportrenderable);
echo $output->render($legendrenderable);
echo $output->render($jsrenderable);
echo $output->footer();
