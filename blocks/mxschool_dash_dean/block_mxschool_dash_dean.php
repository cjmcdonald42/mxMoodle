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
        $renderable = new \local_mxschool\output\index(array(
          // Put any links in this array as displaytext => relative url.
            get_string('users_link', 'block_mxschool_dash_dean') => '/local/mxschool/user_management/index.php',
            get_string('checkin_link', 'block_mxschool_dash_dean') => '/local/mxschool/checkin/index.php',
            get_string('esignout_link', 'block_mxschool_dash_dean') => '/local/mxschool/driving/index.php',
            get_string('advisor_link', 'block_mxschool_dash_dean') => '/local/mxschool/advisor_selection/index.php',
            get_string('rooming_link', 'block_mxschool_dash_dean') => '/local/mxschool/rooming/index.php',
            get_string('vacation_link', 'block_mxschool_dash_dean') => '/local/mxschool/vacation_travel/index.php'
        ));

        $this->content = new stdClass();
        $this->content->text = $output->render($renderable);;
        $this->content->footer = ''; // Add a footer here if desired.

        return $this->content;
    }

    public function specialization() {
        $this->title = get_string('blockname', 'block_mxschool_dash_dean');
    }
}
