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
 * Content for Middlesex's Faculty Dashboard Block
 *
 * @package    block_mxschool_dash_faculty
 * @author     mxMoodle Development Team
 * @copyright  2022 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__.'/../../local/mxschool/locallib.php');

class block_mxschool_dash_faculty extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_mxschool_dash_faculty');
    }

    public function get_content() {
        global $PAGE, $USER;
        if (isset($this->content)) { return $this->content; }

        $this->content = new stdClass();
        if (has_capability('block/mxschool_dash_faculty:access', context_system::instance())) {
            $output = $PAGE->get_renderer('local_mxschool');

            $renderables = array(
                new local_mxschool\output\index(array(
                    get_string('student:data_report', 'block_mxschool_dash_faculty')
                        => '/local/mxschool/user_management/student_report.php',
                    get_string('student:peer_tutoring_report', 'block_mxschool_dash_faculty')
                        => '/local/peertutoring/report.php',
                    get_string('student:vehicle_report', 'block_mxschool_dash_faculty')
                        => '/local/mxschool/user_management/vehicle_report.php'
                ), get_string('student:header', 'block_mxschool_dash_faculty')),

                new local_mxschool\output\index(array(
                    get_string('duty:on_campus_report', 'block_mxschool_dash_faculty')
                        => '/local/signout/on_campus/duty_report.php',
                    get_string('duty:attendance_report', 'block_mxschool_dash_faculty')
                        => '/local/mxschool/checkin/attendance_report.php'
                ), get_string('duty:header', 'block_mxschool_dash_faculty')),
            );

            if(has_capability('local/peertutoring:manage', context_system::instance())) {
                array_push($renderables,
                    new local_mxschool\output\index(array(
                        get_string('peer_tutoring:form', 'block_mxschool_dash_faculty')
                            => '/local/peertutoring/form.php',
                        get_string('peer_tutoring:report', 'block_mxschool_dash_faculty')
                            => '/local/peertutoring/report.php?advisor=',
                        get_string('peer_tutoring:preferences', 'block_mxschool_dash_faculty')
                            => '/local/peertutoring/preferences.php'
                    ), get_string('peer_tutoring:header', 'block_mxschool_dash_faculty')),
                );
            }

            if(has_capability('local/mxschool:manage_vacation_travel', context_system::instance())) {
                array_push($renderables,
                    new local_mxschool\output\index(array(
                        get_string('transportation:report', 'block_mxschool_dash_faculty')
                            => '/local/mxschool/vacation_travel/transportation_report.php'
                    ), get_string('transportation:header', 'block_mxschool_dash_faculty')),
                );
            }

            if(has_capability('local/mxschool:manage_deans_permission', context_system::instance())) {
                array_push($renderables,
                    new local_mxschool\output\index(array(
                        get_string('deans_permission:report', 'block_mxschool_dash_faculty')
                            => '/local/mxschool/deans_permission/report.php?status=under_review'
                    ), get_string('deans_permission:header', 'block_mxschool_dash_faculty')),
                );
            }

            $this->content->text = array_reduce($renderables, function($html, $renderable) use($output) {
                return $html . $output->render($renderable);
            }, '');

            return $this->content;
        }
    }

    public function specialization() {
        $this->title = get_string('blockname', 'block_mxschool_dash_faculty');
    }
}
