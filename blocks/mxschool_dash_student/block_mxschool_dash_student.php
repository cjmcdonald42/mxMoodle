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
 
 class block_mxschool_dash_student extends block_list {
    
    function init() {
        $this->title = get_string('blockname', 'block_mxschool_dash_student');
    }
    
    function get_content() {
        global $CFG, $USER, $OUTPUT, $DB, $PAGE;
    
        if ($this->content !== null) {
            return $this->content;
        }
 
        $this->content         =  new stdClass;
        $this->content->text = '';
        $this->content->items  = array();
        $this->content->icons  = array();
        $this->content->footer = 'Footer goes here...';
 
    $this->content->items[] = html_writer::tag('a','Advisor Selection Form',array('href' => 'http://moodledev.mxschool.edu/moodle/local/mxschool/advisor_selection/index.php'));
    
    return $this->content;
    }
 }

