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
 * @package    block_mxschool_dash_student
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../local/mxschool/classes/output/renderable.php');

class block_mxschool_dash_student extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_mxschool_dash_student');
    }

    public function get_content() {
        global $PAGE;
        if (isset($this->content)) {
            return $this->content;
        }

        $output = $PAGE->get_renderer('local_mxschool');
        $renderable = new \local_mxschool\output\index(array(
          // Put any links in this array as displaytext => relative url.
            get_string('esignout_form', 'block_mxschool_dash_student') => '/local/mxschool/driving/esignout_enter.php',
            get_string('esignout_report', 'block_mxschool_dash_student') => '/local/mxschool/driving/esignout_report.php',
            get_string('advisor_selection', 'block_mxschool_dash_student') => '/local/mxschool/advisor_selection/advisor_enter.php',
            get_string('rooming', 'block_mxschool_dash_student') => '/local/mxschool/rooming/rooming_enter.php',
            get_string('vacation', 'block_mxschool_dash_student') => '/local/mxschool/vacation_travel/vacation_enter.php'
        ));

        $this->content = new stdClass();
        $this->content->text = $output->render($renderable);;
        $this->content->footer = ''; // Add a footer here if desired.

        return $this->content;
    }

    public function specialization() {
        $this->title = get_string('blockname', 'block_mxschool_dash_student');
    }
}
