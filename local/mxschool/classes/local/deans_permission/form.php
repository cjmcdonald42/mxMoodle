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
 * Form for students to submit deans permissions requests for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  deans_permission
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\deans_permission;

defined('MOODLE_INTERNAL') || die();

class form extends \local_mxschool\form {

    /**
     * Form definition.
     */
    protected function definition() {
        $students = $this->_customdata['students'];
	   $eventoptions = $this->_customdata['eventoptions'];

        $fields = array(
            '' => array(
                'id' => self::ELEMENT_HIDDEN_INT,
                'timecreated' => self::ELEMENT_HIDDEN_INT,
                'isstudent' => self::ELEMENT_HIDDEN_INT
            ),
		  'info' => array(
			  'student' => array('element' => 'select', 'options' => $students),
			  'event' => array('element' => 'select', 'options' => $eventoptions),
			  'event_info' => self::ELEMENT_LONG_TEXT_REQUIRED,
              'recurring' => self::ELEMENT_BOOLEAN_REQUIRED,
              'times_away' => self::ELEMENT_LONG_TEXT_REQUIRED
			  'sport' => self::ELEMENT_TEXT_REQUIRED,
			  'missing_sports' => self::ELEMENT_BOOLEAN_REQUIRED,
  			  'missing_studyhours' => self::ELEMENT_BOOLEAN_REQUIRED,
			  'missing_class' => self::ELEMENT_BOOLEAN_REQUIRED,
		  )

        );
        $this->set_fields($fields, 'deans_permission:form');

        $mform = $this->_form;
        $mform->hideIf('student', 'isstudent', 'eq');
    }

    /**
     * Validates the rooming form before it can be submitted.
     *
     * @return array of errors as "element_name"=>"error_description" or an empty array if there are no errors.
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
    }

}
