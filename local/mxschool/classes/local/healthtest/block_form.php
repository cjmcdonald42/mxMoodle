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
 * Form to create Testing Blocks for Middlesex's Dorm and Student Functions Plugin.
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

class block_form extends \local_mxschool\form {

    /**
     * Form definition.
     */
    protected function definition() {
        $testing_cycle_options = $this->_customdata['cycle_options'];

        $fields = array(
            '' => array(
                'id' => self::ELEMENT_HIDDEN_INT,
                'isstudent' => self::ELEMENT_HIDDEN_INT
            ),
		  'info' => array(
			  'testing_cycle' => array('element' => 'select', 'options' => $testing_cycle_options),
			  'start_time' => self::time_selector(5),
			  'end_time' => self::time_selector(5),
			  'date' => array('element' => 'date_selector'),
			  'max_testers' => self::ELEMENT_TEXT_REQUIRED
		  )
        );
        $this->set_fields($fields, 'healthtest:block_form');

        $mform = $this->_form;
    }

    /**
     * Validates the rooming form before it can be submitted.
     *
     * @return array of errors as "element_name"=>"error_description" or an empty array if there are no errors.
     */
    public function validation($data, $files) {
        global $DB;
	   $errors = parent::validation($data, $files);
	   if(!is_numeric($data['max_testers'])) $errors['max_testers'] = get_string('healthpass:preferences:error:not_numeric', 'local_mxschool');
	   return $errors;
    }

}
