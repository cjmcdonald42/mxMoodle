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
 * Middlesex's Dean's Block for the Student Dashboard.
 *
 * @package    block_mxschool_dorm_student
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../local/mxschool/classes/output/renderable.php');
require_once(__DIR__.'/../../local/mxschool/locallib.php');

class block_mxschool_dorm_student extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_mxschool_dorm_student');
    }

    public function get_content() {
        global $PAGE, $USER;
        if (isset($this->content)) {
            return $this->content;
        }

        $this->content = new stdClass();
        if (user_is_admin() || (user_is_student() && student_may_access_weekend($USER->id))) {
            $output = $PAGE->get_renderer('local_mxschool');
            $renderable = new \local_mxschool\output\index(array(
                get_string('weekend_submit', 'block_mxschool_dorm_student') => '/local/mxschool/checkin/weekend_enter.php',
                get_string('weekend_calculator', 'block_mxschool_dorm_student') => '/local/mxschool/checkin/weekend_calculator.php'
            ));
            $this->content->text = $output->render($renderable);
        }

        return $this->content;
    }

    public function specialization() {
        $this->title = get_string('blockname', 'block_mxschool_dorm_student');
    }
}
