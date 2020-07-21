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
 * Content for Middlesex's Dashboard Block for Faculty.
 *
 * @package    block_mxschool_dash_faculty
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
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
        if (isset($this->content)) {
            return $this->content;
        }

        $this->content = new stdClass();
        if (has_capability('block/mxschool_dash_faculty:access', context_system::instance())) {
            $output = $PAGE->get_renderer('local_mxschool');
            $renderable = new local_mxschool\output\index(array(
                get_string('student_report', 'block_mxschool_dash_faculty') => '/local/mxschool/user_management/student_report.php',
                get_string('vehicle_report', 'block_mxschool_dash_faculty') => '/local/mxschool/user_management/vehicle_report.php',
                get_string('duty_report', 'block_mxschool_dash_faculty') => '/local/signout/on_campus/duty_report.php',
                get_string('deans_permission_report', 'block_mxschool_dash_faculty') => '/local/mxschool/deans_permission/report.php?approved=under_review',
                get_string('attendance_report', 'block_mxschool_dash_faculty') => '/local/mxschool/checkin/attendance_report.php'
            ));
            $this->content->text = $output->render($renderable);
        }

        return $this->content;
    }

    public function specialization() {
        $this->title = get_string('blockname', 'block_mxschool_dash_faculty');
    }
}
