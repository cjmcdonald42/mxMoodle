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
 * Form for students to submit rooming requests for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  rooming
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\rooming;

defined('MOODLE_INTERNAL') || die();

class form extends \local_mxschool\form {

    /**
     * Form definition.
     */
    protected function definition() {
        $students = $this->_customdata['students'];
        $roomable = $this->_customdata['roomable'];
        $roomtypes = $this->_customdata['roomtypes'];

        $fields = array(
            '' => array(
                'id' => self::ELEMENT_HIDDEN_INT,
                'timecreated' => self::ELEMENT_HIDDEN_INT,
                'isstudent' => self::ELEMENT_HIDDEN_INT
            ),
        );
        $this->set_fields($fields, 'rooming:form');

        $mform = $this->_form;
        $mform->hideIf('student', 'isstudent', 'eq');
        $mform->disabledIf('student', 'id', 'neq', '0');
    }

    /**
     * Validates the rooming form before it can be submitted.
     * The checks performed are to ensure that the student selected a room type,
     * that the student selected 3 dormmates from the same grade, that the student selected 3 dormmates from any grade,
     * and that the student selected a roommate.
     *
     * @return array of errors as "element_name"=>"error_description" or an empty array if there are no errors.
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
    }

}
