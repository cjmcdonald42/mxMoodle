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
 * Middlesex School's Dean's Block for the Student Dashboard.
 *
 * @package    block_mxschool_dorm_student
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
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

        $links = has_capability('moodle/site:config', context_system::instance())
            || (user_is_student() && student_may_access_weekend($USER->id)) ? array(
            // Put any links in this array as displaytext => relative url.
            get_string('weekend_submit', 'block_mxschool_dorm_student') => '/local/mxschool/checkin/weekend_enter.php',
            get_string('weekend_calc', 'block_mxschool_dorm_student') => '/local/mxschool/checkin/weekend_calculator.php'
        ) : array();
        $output = $PAGE->get_renderer('local_mxschool');
        $renderable = new \local_mxschool\output\index($links);

        $this->content = new stdClass();
        if (count($links)) {
            $this->content->text = $output->render($renderable);
        }

        return $this->content;
    }

    public function specialization() {
        $this->title = get_string('blockname', 'block_mxschool_dorm_student');
    }
}
