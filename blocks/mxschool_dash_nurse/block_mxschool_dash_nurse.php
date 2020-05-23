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
 * Content for Middlesex's Dashboard Block for the Healthcenter.
 *
 * @package     block_mxschool_dash_nurse
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

class block_mxschool_dash_nurse extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_mxschool_dash_nurse');
    }

    public function get_content() {
        global $PAGE;
        if (isset($this->content)) {
            return $this->content;
        }

        $this->content = new stdClass();
        if (has_capability('block/mxschool_dash_nurse:access', context_system::instance())) {
            $output = $PAGE->get_renderer('local_mxschool');
            $renderables = array(
                new local_mxschool\output\index(array(
                    get_string('healthpass:submit_form', 'block_mxschool_dash_nurse')
                        => '/local/mxschool/healthpass/form.php',
                    get_string('healthpass:report', 'block_mxschool_dash_nurse')
                        => '/local/mxschool/healthpass/report.php',
                    get_string('healthpass:preferences', 'block_mxschool_dash_nurse')
                            => '/local/mxschool/healthpass/preferences.php',
                ),  get_string('healthpass', 'block_mxschool_dash_nurse')),
                new local_mxschool\output\index(array(
                    get_string('user_management:student_report', 'block_mxschool_dash_nurse')
                        => '/local/mxschool/user_management/student_report.php',
                    get_string('user_management:faculty_report', 'block_mxschool_dash_nurse')
                        => '/local/mxschool/user_management/faculty_report.php',
                    get_string('user_management:vehicle_report', 'block_mxschool_dash_nurse')
                        => '/local/mxschool/user_management/vehicle_report.php',
                ), get_string('user_management', 'block_mxschool_dash_nurse')),
                new local_mxschool\output\index(array(
                    get_string('signout:on_campus:report', 'block_mxschool_dash_nurse')
                        => '/local/signout/on_campus/report.php',
                    get_string('signout:off_campus:report', 'block_mxschool_dash_nurse')
                        => '/local/signout/off_campus/report.php',
                ), get_string('signout', 'block_mxschool_dash_nurse'))
            );
            $this->content->text = array_reduce($renderables, function($html, $renderable) use($output) {
                return $html . $output->render($renderable);
            }, '');
        }
        return $this->content;
    }

    public function specialization() {
        $this->title = get_string('blockname', 'block_mxschool_dash_nurse');
    }
}
