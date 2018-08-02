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
 * Middlesex School's Dean's Block for the Dashboard.
 *
 * @package    block_mxschool_dash_dean
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../local/mxschool/classes/output/renderable.php');

class block_mxschool_dash_dean extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_mxschool_dash_dean');
    }

    public function get_content() {
        global $PAGE;
        if (isset($this->content)) {
            return $this->content;
        }

        $output = $PAGE->get_renderer('local_mxschool');

        $usermenu = new \local_mxschool\output\index(array(
            get_string('student_report', 'block_mxschool_dash_dean') => '/local/mxschool/user_management/student_report.php',
            get_string('faculty_report', 'block_mxschool_dash_dean') => '/local/mxschool/user_management/faculty_report.php',
            get_string('dorm_report', 'block_mxschool_dash_dean') => '/local/mxschool/user_management/dorm_report.php'
        ), get_string('user_menu', 'block_mxschool_dash_dean'));
        $checkinmenu = new \local_mxschool\output\index(array(
            get_string('generic_report', 'block_mxschool_dash_dean') => '/local/mxschool/checkin/generic_report.php',
            get_string('weekday_report', 'block_mxschool_dash_dean') => '/local/mxschool/checkin/weekday_report.php',
            get_string('weekend_report', 'block_mxschool_dash_dean') => '/local/mxschool/checkin/weekend_report.php',
            get_string('weekend_calculator', 'block_mxschool_dash_dean') => '/local/mxschool/checkin/weekend_calculator.php',
            get_string('checkin_preferences', 'block_mxschool_dash_dean') => '/local/mxschool/checkin/preferences.php'
        ), get_string('checkin_menu', 'block_mxschool_dash_dean'));
        $drivingmenu = new \local_mxschool\output\index(array(
            get_string('vehicle_report', 'block_mxschool_dash_dean') => '/local/mxschool/driving/vehicle_report.php',
            get_string('esignout_report', 'block_mxschool_dash_dean') => '/local/mxschool/driving/esignout_report.php',
            get_string('esignout_preferences', 'block_mxschool_dash_dean') => '/local/mxschool/driving/preferences.php'
        ), get_string('driving_menu', 'block_mxschool_dash_dean'));
        $advisorselectionmenu = new \local_mxschool\output\index(array(
            get_string('advisor_selection_report', 'block_mxschool_dash_dean') => '/local/mxschool/advisor_selection/advisor_report.php',
            get_string('advisor_selection_preferences', 'block_mxschool_dash_dean') => '/local/mxschool/advisor_selection/preferences.php'
        ), get_string('advisor_selection_menu', 'block_mxschool_dash_dean'));
        $roomingmenu = new \local_mxschool\output\index(array(
            get_string('rooming_report', 'block_mxschool_dash_dean') => '/local/mxschool/rooming/rooming_report.php',
            get_string('rooming_preferences', 'block_mxschool_dash_dean') => '/local/mxschool/rooming/preferences.php'
        ), get_string('rooming_menu', 'block_mxschool_dash_dean'));
        $vacationtravelmenu = new \local_mxschool\output\index(array(
            get_string('vacation_travel_report', 'block_mxschool_dash_dean') => '/local/mxschool/vacation_travel/vacation_report.php',
            get_string('vacation_travel_transportation_report', 'block_mxschool_dash_dean') =>
            '/local/mxschool/vacation_travel/transportation_report.php',
            get_string('vacation_travel_preferences', 'block_mxschool_dash_dean') => '/local/mxschool/vacation_travel/preferences.php'
        ), get_string('vacation_travel_menu', 'block_mxschool_dash_dean'));

        $this->content = new stdClass();
        if (has_capability('block/mxschool_dash_dean:access', context_system::instance())) {
            $this->content->text = $output->render($usermenu).$output->render($checkinmenu).$output->render($drivingmenu).$output->render($advisorselectionmenu).$output->render($roomingmenu).$output->render($vacationtravelmenu);
        }
        return $this->content;
    }

    public function specialization() {
        $this->title = get_string('blockname', 'block_mxschool_dash_dean');
    }
}
