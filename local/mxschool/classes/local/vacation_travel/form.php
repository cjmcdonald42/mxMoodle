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
 * Form for students to submit vacation travel plans and transportation needs for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  vacation_travel
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\vacation_travel;

defined('MOODLE_INTERNAL') || die();

class form extends \local_mxschool\form {

	/**
	 * @var bool $departureenabled Whether the departure portion of the form should be included.
	 */
	private $departureenabled;
    /**
     * @var bool $returnenabled Whether the return portion of the form should be included.
     */
    private $returnenabled;

    /**
     * Form definition.
     */
    protected function definition() {
	   $this->departureenabled = $this->_customdata['departureenabled'];
        $this->returnenabled = $this->_customdata['returnenabled'];
        $students = $this->_customdata['students'];
        $depsites = $this->_customdata['depsites'];
        $retsites = $this->_customdata['retsites'];
        $types = $this->_customdata['types'];

        $fields = array(
            '' => array(
                'id' => self::ELEMENT_HIDDEN_INT,
                'timecreated' => self::ELEMENT_HIDDEN_INT,
                'isstudent' => self::ELEMENT_HIDDEN_INT,
                'dep_id' => self::ELEMENT_HIDDEN_INT
            ),
            'info' => array(
                'student' => array('element' => 'select', 'options' => $students),
                'destination' => self::ELEMENT_TEXT,
                'phone' => self::ELEMENT_TEXT
		 ));
	if ($this->departureenabled) {
		$fields['']['dep_id'] = self::ELEMENT_HIDDEN_INT;
		$fields['departure'] = array (
			'dep_mxtransportation' => self::ELEMENT_BOOLEAN,
			'dep_type' => array('element' => 'radio', 'options' => $types),
			'dep_site' => array('element' => 'radio', 'options' => $depsites, 'useradioindex' => true),
			'dep_details' => self::ELEMENT_TEXT,
			'dep_carrier' => self::ELEMENT_TEXT,
			'dep_number' => self::ELEMENT_TEXT,
			'dep_variable' => array('element' => 'group', 'children' => array(
			    'time' => self::time_selector(15),
			    'date' => array('element' => 'date_selector', 'options' => self::date_options_school_year())
			)),
			'dep_international' => self::ELEMENT_BOOLEAN
		 );
	}
        if ($this->returnenabled) {
            $fields['']['ret_id'] = self::ELEMENT_HIDDEN_INT;
            $fields['return'] = array(
                'ret_mxtransportation' => self::ELEMENT_BOOLEAN,
                'ret_type' => array('element' => 'radio', 'options' => $types),
                'ret_site' => array('element' => 'radio', 'options' => $retsites, 'useradioindex' => true),
                'ret_details' => self::ELEMENT_TEXT,
                'ret_carrier' => self::ELEMENT_TEXT,
                'ret_number' => self::ELEMENT_TEXT,
                'ret_variable' => array('element' => 'group', 'children' => array(
                    'time' => self::time_selector(15),
                    'date' => array('element' => 'date_selector', 'options' => self::date_options_school_year())
                )),
                'ret_international' => self::ELEMENT_BOOLEAN
            );
        }
        $this->set_fields($fields, 'vacation_travel:form');

        $mform = $this->_form;
        $mform->hideIf('student', 'isstudent', 'eq');
        $mform->disabledIf('student', 'id', 'neq', '0');
    }

    /**
     * Validates the vacation travel form before it can be submitted.
     * The checks performed are to ensure that all required fields are filled out.
     *
     * @return array of errors as "element_name"=>"error_description" or an empty array if there are no errors.
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
        if (empty($data['destination'])) {
            $errors['destination'] = get_string('vacation_travel:form:error:no_destination', 'local_mxschool');
        }
        if (empty($data['phone'])) {
            $errors['phone'] = get_string('vacation_travel:form:error:no_phone', 'local_mxschool');
        }
	   if($this->departureenabled) {
	        if (!isset($data['dep_mxtransportation'])) {
	            $errors['dep_mxtransportation'] = get_string('vacation_travel:form:error:no_mxtransportation', 'local_mxschool');
	        } else {
	            if (!isset($data['dep_type'])) {
	                $errors['dep_type'] = get_string('vacation_travel:form:error:no_type', 'local_mxschool');
	            } else {
	                $depdatetime = generate_timestamp($data, 'dep_variable');
	                if (isset($data['dep_site'])) {
	                    $depdefault = $DB->get_field(
	                        'local_mxschool_vt_site', 'default_departure_time', array('id' => $data['dep_site'])
	                    );
	                    if ($depdefault) {
	                        $depdatetime = $depdefault;
	                    }
	                }
	                if ($data['dep_mxtransportation'] && !isset($data['dep_site'])) {
	                    if ($data['dep_type'] === 'Plane') {
	                        $errors['dep_site'] = get_string('vacation_travel:form:error:no_airport', 'local_mxschool');
	                    } else if ($data['dep_type'] === 'Train') {
	                        $errors['dep_site'] = get_string('vacation_travel:form:error:no_station', 'local_mxschool');
	                    } else {
	                        $errors['dep_site'] = get_string('vacation_travel:form:error:no_site', 'local_mxschool');
	                    }
	                } else if (empty($data['dep_details'])) {
	                    if ($data['dep_type'] === 'Car') {
	                        $errors['dep_details'] = get_string('vacation_travel:form:error:no_driver', 'local_mxschool');
	                    } else if ($data['dep_type'] === 'Non-MX Bus') {
	                        $errors['dep_details'] = get_string('vacation_travel:form:error:no_details', 'local_mxschool');
	                    } else if ($data['dep_mxtransportation'] && $data['dep_site'] === '0') {
	                        $errors['dep_details'] = get_string('vacation_travel:form:error:no_other', 'local_mxschool');
	                    }
	                }
	                if ($data['dep_type'] === 'Plane' || $data['dep_type'] === 'Bus' || $data['dep_type'] === 'Train') {
	                    if (empty($data['dep_carrier'])) {
	                        $errors['dep_carrier'] = get_string(
	                            "vacation_travel:form:error:no_carrier:{$data['dep_type']}", 'local_mxschool'
	                        );
	                    }
	                    if (empty($data['dep_number'])) {
	                        $errors['dep_number'] = get_string(
	                            "vacation_travel:form:error:no_number:{$data['dep_type']}", 'local_mxschool'
	                        );
	                    }
	                }
	                if ($data['dep_mxtransportation'] && $data['dep_type'] === 'Plane' && !isset($data['dep_international'])) {
	                    $errors['dep_international'] = get_string('vacation_travel:form:error:no_international:dep', 'local_mxschool');
	                }
	            }
		  }
        }
        if ($this->returnenabled) {
            if (!isset($data['ret_mxtransportation'])) {
                $errors['ret_mxtransportation'] = get_string('vacation_travel:form:error:no_mxtransportation', 'local_mxschool');
            } else {
                if (!isset($data['ret_type'])) {
                    $errors['ret_type'] = get_string('vacation_travel:form:error:no_type', 'local_mxschool');
                } else {
                    $retdatetime = generate_timestamp($data, 'ret_variable');
                    if (isset($data['ret_site'])) {
                        $retdefault = $DB->get_field(
                            'local_mxschool_vt_site', 'default_return_time', array('id' => $data['ret_site'])
                        );
                        if ($retdefault) {
                            $retdatetime = $retdefault;
                        }
                    }
                    if ($data['ret_mxtransportation'] && !isset($data['ret_site'])) {
                        if ($data['ret_type'] === 'Plane') {
                            $errors['ret_site'] = get_string('vacation_travel:form:error:no_airport', 'local_mxschool');
                        } else if ($data['ret_type'] === 'Train') {
                            $errors['ret_site'] = get_string('vacation_travel:form:error:no_station', 'local_mxschool');
                        } else {
                            $errors['ret_site'] = get_string('vacation_travel:form:error:no_site', 'local_mxschool');
                        }
                    } else if (empty($data['ret_details'])) {
                        if ($data['ret_type'] === 'Car') {
                            $errors['ret_details'] = get_string('vacation_travel:form:error:no_driver', 'local_mxschool');
                        } else if ($data['ret_type'] === 'Non-MX Bus') {
                            $errors['ret_details'] = get_string('vacation_travel:form:error:no_details', 'local_mxschool');
                        } else if ($data['ret_mxtransportation'] && $data['ret_site'] === '0') {
                            $errors['ret_details'] = get_string('vacation_travel:form:error:no_other', 'local_mxschool');
                        }
                    }
                    if ($data['ret_type'] === 'Plane' || $data['ret_type'] === 'Bus' || $data['ret_type'] === 'Train') {
                        if (empty($data['ret_carrier'])) {
                            $errors['ret_carrier'] = get_string(
                                "vacation_travel:form:error:no_carrier:{$data['ret_type']}", 'local_mxschool'
                            );
                        }
                        if (empty($data['ret_number'])) {
                            $errors['ret_number'] = get_string(
                                "vacation_travel:form:error:no_number:{$data['ret_type']}", 'local_mxschool'
                            );
                        }
                    }
                    if ($data['ret_mxtransportation'] && $data['ret_type'] === 'Plane' && !isset($data['ret_international'])) {
                        $errors['ret_international'] = get_string(
                            'vacation_travel:form:error:no_international:ret', 'local_mxschool'
                        );
                    }
                }
            }
            if (isset($depdatetime) && isset($retdatetime) && $depdatetime >= $retdatetime) {
                $errors['ret_variable'] = get_string('vacation_travel:form:error:out_of_order', 'local_mxschool');
            }
        }
        return $errors;
    }

}
