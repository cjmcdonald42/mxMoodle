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
* Middlesex's Dashboard Block for the Deans.
*
* @package    block_mxschool_dash_dean
* @author     Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
* @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
* @copyright  2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_mxschool_dash_dean extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_mxschool_dash_dean');
    }

    public function get_content() {
        global $PAGE;
        if (isset($this->content)) {
            return $this->content;
        }

        $this->content = new stdClass();
        if (has_capability('block/mxschool_dash_dean:access', context_system::instance())) {
            $output = $PAGE->get_renderer('local_mxschool');
            $renderables = array(
                new local_mxschool\output\index(array(
                    get_string('user_management:student_report', 'block_mxschool_dash_dean')
                        => '/local/mxschool/user_management/student_report.php',
                    get_string('user_management:faculty_report', 'block_mxschool_dash_dean')
                        => '/local/mxschool/user_management/faculty_report.php',
                    get_string('user_management:dorm_report', 'block_mxschool_dash_dean')
                        => '/local/mxschool/user_management/dorm_report.php',
                    get_string('user_management:vehicle_report', 'block_mxschool_dash_dean')
                        => '/local/mxschool/user_management/vehicle_report.php',
                ), get_string('user_management', 'block_mxschool_dash_dean')),
                new local_mxschool\output\index(array(
                    get_string('checkin:generic_report', 'block_mxschool_dash_dean')
                        => '/local/mxschool/checkin/generic_report.php',
			    get_string('checkin:attendance_report', 'block_mxschool_dash_dean')
                       => '/local/mxschool/checkin/attendance_report.php',
                    get_string('checkin:weekday_report', 'block_mxschool_dash_dean')
                        => '/local/mxschool/checkin/weekday_report.php',
                    get_string('checkin:weekend_report', 'block_mxschool_dash_dean')
                        => '/local/mxschool/checkin/weekend_report.php',
                    get_string('checkin:weekend_calculator', 'block_mxschool_dash_dean')
                        => '/local/mxschool/checkin/weekend_calculator.php',
                    get_string('checkin:preferences', 'block_mxschool_dash_dean')
                        => '/local/mxschool/checkin/preferences.php'
                ), get_string('checkin', 'block_mxschool_dash_dean')),
			 new local_mxschool\output\index(array(
				get_string('deans_permission:report', 'block_mxschool_dash_dean')
				    => '/local/mxschool/deans_permission/report.php?approved=under_review',
				get_string('deans_permission:preferences', 'block_mxschool_dash_dean')
				    => '/local/mxschool/deans_permission/preferences.php'
			 ), get_string('deans_permission', 'block_mxschool_dash_dean')),
                new local_mxschool\output\index(array(
                    get_string('advisor_selection:report', 'block_mxschool_dash_dean')
                        => '/local/mxschool/advisor_selection/report.php',
                    get_string('advisor_selection:preferences', 'block_mxschool_dash_dean')
                        => '/local/mxschool/advisor_selection/preferences.php'
                ), get_string('advisor_selection', 'block_mxschool_dash_dean')),
                new local_mxschool\output\index(array(
                    get_string('rooming:report', 'block_mxschool_dash_dean')
                        => '/local/mxschool/rooming/report.php',
                    get_string('rooming:preferences', 'block_mxschool_dash_dean')
                        => '/local/mxschool/rooming/preferences.php'
                ), get_string('rooming', 'block_mxschool_dash_dean')),
                new local_mxschool\output\index(array(
                    get_string('vacation_travel:report', 'block_mxschool_dash_dean')
                        => '/local/mxschool/vacation_travel/report.php',
                    get_string('vacation_travel:transportation_report', 'block_mxschool_dash_dean')
                        => '/local/mxschool/vacation_travel/transportation_report.php',
                    get_string('vacation_travel:preferences', 'block_mxschool_dash_dean')
                        => '/local/mxschool/vacation_travel/preferences.php'
                ), get_string('vacation_travel', 'block_mxschool_dash_dean')),
                new local_mxschool\output\index(array(
                    get_string('signout:combined_report', 'block_mxschool_dash_dean')
                        => '/local/signout/combined_report.php',
                    get_string('signout:on_campus:report', 'block_mxschool_dash_dean')
                        => '/local/signout/on_campus/report.php',
                    get_string('signout:on_campus:duty_report', 'block_mxschool_dash_dean')
                        => '/local/signout/on_campus/duty_report.php',
                    get_string('signout:on_campus:preferences', 'block_mxschool_dash_dean')
                        => '/local/signout/on_campus/preferences.php',
                    get_string('signout:off_campus:report', 'block_mxschool_dash_dean')
                        => '/local/signout/off_campus/report.php',
                    get_string('signout:off_campus:preferences', 'block_mxschool_dash_dean')
                        => '/local/signout/off_campus/preferences.php'
                ), get_string('signout', 'block_mxschool_dash_dean'))
            );
            $this->content->text = array_reduce($renderables, function($html, $renderable) use($output) {
                return $html . $output->render($renderable);
            }, '');
        }
        return $this->content;
    }

    public function specialization() {
        $this->title = get_string('blockname', 'block_mxschool_dash_dean');
    }
}
