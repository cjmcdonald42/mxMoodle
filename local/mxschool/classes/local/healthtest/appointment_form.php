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
 * Form to schedule an appointment for Middlesex's Health Test Plugin
 *
 * @package     local_mxschool
 * @subpackage  healthtest
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2021 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\healthtest;

defined('MOODLE_INTERNAL') || die();

class appointment_form extends \local_mxschool\form {

    /**
     * Form definition.
     */
    protected function definition() {
	   // Get $users and $isManager from form page
       $users = $this->_customdata['users'];
	  $isManager = $this->_customdata['isManager'];
	  $userid = $this->_customdata['userid'];

	  $block_options = $isManager ? get_appointment_form_block_options() : get_appointment_form_block_options($userid);

	   // Define fields
        $fields = array(
            '' => array(
                'id' => self::ELEMENT_HIDDEN_INT,
                'timecreated' => self::ELEMENT_HIDDEN_INT,
                'isstudent' => self::ELEMENT_HIDDEN_INT
            ),
            'info' => array(
                'name' => $isManager ?
			   array('element' => 'select', 'options' => $users)
			 : array('element' => 'static', 'text' => $users['name']),
			 'block' => $block_options ?
			   array('element' => 'select', 'options' => $block_options)
			 : array('element' => 'static', 'text' => 'You have already scheduled a test for every testing cycle'),
        ));
        $this->set_fields($fields, 'healthtest:form');
        $mform = $this->_form;
    }

    /**
	* Validates the appointment form before it can be submitted.
	*
	* @return array of errors as "element_name"=>"error_description" or an empty array if there are no errors.
	*/
    public function validation($data, $files) {
	   global $DB;
	   $errors = parent::validation($data, $files);
	   return $errors;
    }
}
