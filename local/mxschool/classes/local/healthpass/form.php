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
 * Form to submit daily intake for Middlesex Health Pass Plugin.
 *
 * @package     local_mxschool
 * @subpackage  healthpass
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\healthpass;

defined('MOODLE_INTERNAL') || die();

class form extends \local_mxschool\form {

    /**
     * Form definition.
     */
    protected function definition() {
	   // All possible temperature values
        $temps = array(
           '96' => '96', '97' => '97', '98' => '98', '99' => '99',
           '100' => '100', '101' => '101', '102' => '102', '103' => '103', '104' => '104', '105' => '105'
         );
	    $temp_decimals = array(
		    '.0' => '.0', '.1' => '.1', '.2' => '.2', '.3' => '.3', '.4' => '.4',
		    '.5' => '.5', '.6' => '.6', '.7' => '.7', '.8' => '.8',
		    '.9' => '.9'
	    );

	   // Get $users and $isManager from form page
        $users = $this->_customdata['users'];
	   $isManager = $this->_customdata['isManager'];

	   // Define fields
        $fields = array(
            '' => array(
                'id' => self::ELEMENT_HIDDEN_INT,
                'timecreated' => self::ELEMENT_HIDDEN_INT,
                'isstudent' => self::ELEMENT_HIDDEN_INT
            ),
            'health_info' => array(
                'name' => $isManager ?
			   array('element' => 'select', 'options' => $users)
			 : array('element' => 'static', 'text' => $users['name']),
                'body_temperature' => array('element' => 'group', 'children' => array(
				 'temp' => array('element' => 'select', 'options' => $temps),
				 'temp_decimal' => array('element' => 'select', 'options' => $temp_decimals)
			 )),
			 // NOTE: Update line 89 of healthpass/form.php and the langauge file if adding more health_info questions
                'health_info0' => self::ELEMENT_YES_NO_REQUIRED,
			 'health_info1' => self::ELEMENT_YES_NO_REQUIRED,
			 'health_info2' => self::ELEMENT_YES_NO_REQUIRED,
			 // 'health_info3' => self::ELEMENT_YES_NO_REQUIRED,
			 // 'health_info4' => self::ELEMENT_YES_NO_REQUIRED
			 // 'health_info5' => self::ELEMENT_YES_NO_REQUIRED
            ),
            'symptoms' => array(
                'symptom0' => self::ELEMENT_YES_NO,
                'symptom1' => self::ELEMENT_YES_NO,
                'symptom2' => self::ELEMENT_YES_NO,
                'symptom3' => self::ELEMENT_YES_NO,
                'symptom4' => self::ELEMENT_YES_NO,
                'symptom5' => self::ELEMENT_YES_NO,
                'symptom6' => self::ELEMENT_YES_NO,
			 // NOTE: Update line 107 of healthpass/form.php, the validation function below, and the language file if adding more symptoms
			 'symptom7' => self::ELEMENT_YES_NO,
			 // 'symptom8' => self::ELEMENT_YES_NO,
			 // 'symptom9' => self::ELEMENT_YES_NO,
			 // 'symptom10' => self::ELEMENT_YES_NO,
			 // 'symptom11' => self::ELEMENT_YES_NO,
			 // 'symptom12' => self::ELEMENT_YES_NO,
		 ),
        );
        $this->set_fields($fields, 'healthpass:form', false, 'local_mxschool', false);

        $mform = $this->_form;
	   $mform->setDefault('body_temperature_temp', '98');
	   $mform->setDefault('body_temperature_temp_decimal', '.6');

	   // Create 'I have no symptoms' button and then add 'Save Changes' and 'Cancel' buttons
	   $mform->addElement($mform->createElement('submit', 'no_symptoms', get_string('healthpass:form:no_symptoms_button', 'local_mxschool')));
	   $this->add_action_buttons();
    }

    /**
	* Validates the health form before it can be submitted.
	* The checks performed are to ensure that the user did not click 'I have no symptoms'
	* when yes has been selected for any of the symptoms.
	*
	* @return array of errors as "element_name"=>"error_description" or an empty array if there are no errors.
	*/
    public function validation($data, $files) {
	   global $DB;
	   $errors = parent::validation($data, $files);
	   if(array_key_exists('no_symptoms', $data)) { // if no_symptoms button is pressed but there are yes's
		   if(array_key_exists('symptom0', $data) and $data["symptom0"]=='Yes') $errors['symptom0'] = get_string('healthpass:form:error:no_symptoms_logic', 'local_mxschool');
		   if(array_key_exists('symptom1', $data) and $data["symptom1"]=='Yes') $errors['symptom1'] = get_string('healthpass:form:error:no_symptoms_logic', 'local_mxschool');
		   if(array_key_exists('symptom2', $data) and $data["symptom2"]=='Yes') $errors['symptom2'] = get_string('healthpass:form:error:no_symptoms_logic', 'local_mxschool');
		   if(array_key_exists('symptom3', $data) and $data["symptom3"]=='Yes') $errors['symptom3'] = get_string('healthpass:form:error:no_symptoms_logic', 'local_mxschool');
		   if(array_key_exists('symptom4', $data) and $data["symptom4"]=='Yes') $errors['symptom4'] = get_string('healthpass:form:error:no_symptoms_logic', 'local_mxschool');
		   if(array_key_exists('symptom5', $data) and $data["symptom5"]=='Yes') $errors['symptom5'] = get_string('healthpass:form:error:no_symptoms_logic', 'local_mxschool');
		   if(array_key_exists('symptom6', $data) and $data["symptom6"]=='Yes') $errors['symptom6'] = get_string('healthpass:form:error:no_symptoms_logic', 'local_mxschool');
		   if(array_key_exists('symptom7', $data) and $data["symptom7"]=='Yes') $errors['symptom7'] = get_string('healthpass:form:error:no_symptoms_logic', 'local_mxschool');
		   // if(array_key_exists('symptom8', $data) and $data["symptom8"]=='Yes') $errors['symptom8'] = get_string('healthpass:form:error:no_symptoms_logic', 'local_mxschool');
		   // if(array_key_exists('symptom9', $data) and $data["symptom9"]=='Yes') $errors['symptom9'] = get_string('healthpass:form:error:no_symptoms_logic', 'local_mxschool');
		   // if(array_key_exists('symptom10', $data) and $data["symptom10"]=='Yes') $errors['symptom10'] = get_string('healthpass:form:error:no_symptoms_logic', 'local_mxschool');
		   // if(array_key_exists('symptom11', $data) and $data["symptom11=='Yes') $errors['symptom11'] = get_string('healthpass:form:error:no_symptoms_logic', 'local_mxschool');
	   }
	   if(!array_key_exists('no_symptoms', $data)) { // if save changes is pressed but there are unset symptoms
		   if(!array_key_exists('symptom0', $data)) $errors['symptom0'] = get_string('healthpass:form:error:unset_symptom', 'local_mxschool');
		   if(!array_key_exists('symptom1', $data)) $errors['symptom1'] = get_string('healthpass:form:error:unset_symptom', 'local_mxschool');
		   if(!array_key_exists('symptom2', $data)) $errors['symptom2'] = get_string('healthpass:form:error:unset_symptom', 'local_mxschool');
		   if(!array_key_exists('symptom3', $data)) $errors['symptom3'] = get_string('healthpass:form:error:unset_symptom', 'local_mxschool');
		   if(!array_key_exists('symptom4', $data)) $errors['symptom4'] = get_string('healthpass:form:error:unset_symptom', 'local_mxschool');
		   if(!array_key_exists('symptom5', $data)) $errors['symptom5'] = get_string('healthpass:form:error:unset_symptom', 'local_mxschool');
		   if(!array_key_exists('symptom6', $data)) $errors['symptom6'] = get_string('healthpass:form:error:unset_symptom', 'local_mxschool');
		   if(!array_key_exists('symptom7', $data)) $errors['symptom7'] = get_string('healthpass:form:error:unset_symptom', 'local_mxschool');
		   // if(!array_key_exists('symptom8', $data)) $errors['symptom8'] = get_string('healthpass:form:error:unset_symptom', 'local_mxschool');
		   // if(!array_key_exists('symptom9', $data)) $errors['symptom9'] = get_string('healthpass:form:error:unset_symptom', 'local_mxschool');
		   // if(!array_key_exists('symptom10', $data)) $errors['symptom10'] = get_string('healthpass:form:error:unset_symptom', 'local_mxschool');
		   // if(!array_key_exists('symptom11', $data)) $errors['symptom11'] = get_string('healthpass:form:error:unset_symptom', 'local_mxschool');
	   }
	   return $errors;
    }
}
