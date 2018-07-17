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
 * Form for peer tutors to submit tutoring records for Middlesex School's Peer Tutoring Subplugin.
 *
 * @package    local_peertutoring
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../mxschool/classes/mx_form.php');

class tutoring_form extends local_mxschool_form {

    /**
     * Form definition.
     */
    protected function definition() {
        $id = $this->_customdata['id'];
        $tutors = $this->_customdata['tutors'];
        $students = $this->_customdata['students'];
        $departments = $this->_customdata['departments'];
        $courses = $this->_customdata['courses'];
        $types = $this->_customdata['types'];
        $ratings = $this->_customdata['ratings'];

        $dateparameters = array(
            'startyear' => strftime('%Y', get_config('local_mxschool', 'dorms_open_date')),
            'stopyear' => strftime('%Y', get_config('local_mxschool', 'dorms_close_date')),
            'timezone'  => core_date::get_server_timezone_object()
        );

        $fields = array(
            '' => array(
                'id' => parent::ELEMENT_HIDDEN_INT,
                'timecreated' => parent::ELEMENT_HIDDEN_INT,
                'istutor' => parent::ELEMENT_HIDDEN_INT
            ), 'info' => array(
                'tutor' => array('element' => 'select', 'options' => $tutors),
                'tutoringdate' => array('element' => 'date_selector', 'parameters' => $dateparameters),
                'student' => array('element' => 'select', 'options' => $students)
            ), 'details' => array(
                'department' => array('element' => 'select', 'options' => $departments),
                'course' => array('element' => 'select', 'options' => $courses),
                'topic' => parent::ELEMENT_TEXT,
                'type' => array('element' => 'group', 'children' => array(
                    'select' => array('element' => 'select', 'options' => $types),
                    'other' => parent::ELEMENT_TEXT
                )), 'rating' => array('element' => 'select', 'options' => $ratings),
                'notes' => parent::ELEMENT_TEXT_AREA
            )
        );
        parent::set_fields($fields, 'tutoring_form', false, 'local_peertutoring');

        $mform = $this->_form;
        $mform->hideIf('tutor', 'istutor', 'eq');
        $mform->hideIf('type_other', 'type_select', 'neq', '5');
    }

    /**
     * Validates the tutoring form before it can be submitted.
     * The checks performed are to ensure that all required fields are filled out.
     *
     * @return array of errors as "element_name"=>"error_description" or an empty array if there are no errors.
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
        if (!$data['department']) {
            $errors['department'] = get_string('tutoring_form_error_nodepartment', 'local_peertutoring');
        }
        if (!$data['course']) {
            $errors['course'] = get_string('tutoring_form_error_nocourse', 'local_peertutoring');
        }
        if (!$data['topic']) {
            $errors['topic'] = get_string('tutoring_form_error_notopic', 'local_peertutoring');
        }
        if (!$data['type_select']) {
            $errors['type'] = get_string('tutoring_form_error_notype', 'local_peertutoring');
        } else if ($data['type_select'] === '5' && $data['type_other'] === '') {
            $errors['type'] = get_string('tutoring_form_error_notype', 'local_peertutoring');
        }
        if (!$data['rating']) {
            $errors['rating'] = get_string('tutoring_form_error_norating', 'local_peertutoring');
        }
        return $errors;
    }

}
