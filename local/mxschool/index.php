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
 * Main index page for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('locallib.php');
require_once(__DIR__.'/classes/output/renderable.php');

if (!has_capability('moodle/site:config', context_system::instance())) {
    redirect(new moodle_url('/my'));
}

admin_externalpage_setup('main_index');

$url = '/local/mxschool/index.php';
$title = get_string('pluginname', 'local_mxschool');

setup_generic_page($url, $title);

$output = $PAGE->get_renderer('local_mxschool');
$usermanagement = new \local_mxschool\output\index(array(
    get_string('student_report', 'local_mxschool') => '/local/mxschool/user_management/student_report.php',
    get_string('faculty_report', 'local_mxschool') => '/local/mxschool/user_management/faculty_report.php',
    get_string('dorm_report', 'local_mxschool') => '/local/mxschool/user_management/dorm_report.php'
), get_string('user_management', 'local_mxschool'));
$checkin = new \local_mxschool\output\index(array(
    get_string('checkin_preferences', 'local_mxschool') => '/local/mxschool/checkin/preferences.php',
    get_string('generic_report', 'local_mxschool') => '/local/mxschool/checkin/generic_report.php',
    get_string('weekday_report', 'local_mxschool') => '/local/mxschool/checkin/weekday_report.php',
    get_string('weekend_form', 'local_mxschool') => '/local/mxschool/checkin/weekend_enter.php',
    get_string('weekend_report', 'local_mxschool') => '/local/mxschool/checkin/weekend_report.php',
    get_string('weekend_calculator', 'local_mxschool') => '/local/mxschool/checkin/weekend_calculator.php'
), get_string('checkin', 'local_mxschool'));
$driving = new \local_mxschool\output\index(array(
    get_string('esignout_preferences', 'local_mxschool') => '/local/mxschool/driving/preferences.php',
    get_string('vehicle_report', 'local_mxschool') => '/local/mxschool/driving/vehicle_report.php',
    get_string('esignout', 'local_mxschool') => '/local/mxschool/driving/esignout_enter.php',
    get_string('esignout_report', 'local_mxschool') => '/local/mxschool/driving/esignout_report.php'
), get_string('driving', 'local_mxschool'));
$advisorselection = new \local_mxschool\output\index(array(
    get_string('advisor_selection_preferences', 'local_mxschool') => '/local/mxschool/advisor_selection/preferences.php',
    get_string('advisor_selection_form', 'local_mxschool') => '/local/mxschool/advisor_selection/advisor_enter.php',
    get_string('advisor_selection_report', 'local_mxschool') => '/local/mxschool/advisor_selection/advisor_report.php'
), get_string('advisor_selection', 'local_mxschool'));
$rooming = new \local_mxschool\output\index(array(
    get_string('rooming_preferences', 'local_mxschool') => '/local/mxschool/rooming/preferences.php',
    get_string('rooming_form', 'local_mxschool') => '/local/mxschool/rooming/rooming_enter.php',
    get_string('rooming_report', 'local_mxschool') => '/local/mxschool/rooming/rooming_report.php'
), get_string('rooming', 'local_mxschool'));
$vacationtravel = new \local_mxschool\output\index(array(
    get_string('vacation_travel_preferences', 'local_mxschool') => '/local/mxschool/vacation_travel/preferences.php',
    get_string('vacation_travel_form', 'local_mxschool') => '/local/mxschool/vacation_travel/vacation_enter.php',
    get_string('vacation_travel_report', 'local_mxschool') => '/local/mxschool/vacation_travel/vacation_report.php',
    get_string('vacation_travel_transportation_report', 'local_mxschool') =>
    '/local/mxschool/vacation_travel/transportation_report.php'
), get_string('vacation_travel', 'local_mxschool'));
$peertutoring = new \local_mxschool\output\index(array(
    get_string('preferences', 'local_peertutoring') => '/local/peertutoring/preferences.php',
    get_string('tutoring_form', 'local_peertutoring') => '/local/peertutoring/tutoring_enter.php',
    get_string('tutoring_report', 'local_peertutoring') => '/local/peertutoring/tutoring_report.php'
), get_string('peertutoring', 'local_peertutoring'));

echo $output->header();
echo $output->heading($title);
echo $output->render($usermanagement);
echo $output->render($checkin);
echo $output->render($driving);
echo $output->render($advisorselection);
echo $output->render($rooming);
echo $output->render($vacationtravel);
echo $output->render($peertutoring);
echo $output->footer();
