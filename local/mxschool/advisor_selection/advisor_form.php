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
 * Advisor selection form for students to submit for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage advisor_selection
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_form.php');

class advisor_form extends local_mxschool_form {

    /**
     * Form definition.
     */
    protected function definition() {
        $id = $this->_customdata['id'];
        $students = $this->_customdata['students'];
        $faculty = $this->_customdata['faculty'];

        $fields = array(
            '' => array(
                'id' => self::ELEMENT_HIDDEN_INT,
                'timecreated' => self::ELEMENT_HIDDEN_INT,
                'isstudent' => self::ELEMENT_HIDDEN_INT
            ),
            'info' => array(
                'student' => array('element' => 'select', 'options' => $students),
                'current' => array('element' => 'static'),
                'keepcurrent' => self::ELEMENT_BOOLEAN,
                'warning' => array('element' => 'static', 'name' => null)
            ),
            'options' => array(
                'instructions' => array('element' => 'static', 'name' => null),
                'option1' => array('element' => 'select', 'options' => $faculty),
                'option2' => array('element' => 'select', 'options' => $faculty),
                'option3' => array('element' => 'select', 'options' => $faculty),
                'option4' => array('element' => 'select', 'options' => $faculty),
                'option5' => array('element' => 'select', 'options' => $faculty)
            ),
            'deans' => array(
                'selected' => array('element' => 'select', 'options' => $faculty)
            )
        );
        $this->set_fields($fields, 'advisor_selection_form');

        $mform = $this->_form;
        $mform->hideIf('student', 'isstudent', 'eq');
        $mform->disabledIf('student', 'id', 'neq', '0');
    }

    /**
     * Validates the advisor selection form before it can be submitted.
     * The checks performed are to ensure that the student selected whether to keep the current advisor
     * and that the student has selected a sufficient number of advisors.
     *
     * @return array of errors as "element_name"=>"error_description" or an empty array if there are no errors.
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
        if ($data['keepcurrent'] === '') {
            $errors['keepcurrent'] = get_string('advisor_form_error_nokeepcurrent', 'local_mxschool');
        }
        if ($data['keepcurrent'] === '0') {
            $current = $DB->get_field('local_mxschool_student', 'advisorid', array('userid' => $data['student']));
            for ($i = 1; $i <= 5; $i++) {
                if (!$data["option{$i}"]) {
                    $errors["option{$i}"] = get_string('advisor_form_error_incomplete', 'local_mxschool');
                    break;
                }
                if ($data["option{$i}"] === $current) {
                    break;
                }
            }
        }
        return $errors;
    }
}
