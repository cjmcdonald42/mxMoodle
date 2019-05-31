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
 * Form for students to submit vacation travel details for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage vacation_travel
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_form.php');

class vacation_form extends local_mxschool_form {

    /**
     * @var bool $returnenabled Whether the return portion of the form should be included.
     */
    private $returnenabled;

    /**
     * Form definition.
     */
    protected function definition() {
        $id = $this->_customdata['id'];
        $this->returnenabled = $this->_customdata['returnenabled'];
        $students = $this->_customdata['students'];
        $depsites = $this->_customdata['depsites'];
        $retsites = $this->_customdata['retsites'];
        $types = $this->_customdata['types'];

        $dateparameters = array(
            'startyear' => strftime('%Y', get_config('local_mxschool', 'dorms_open_date')),
            'stopyear' => strftime('%Y', get_config('local_mxschool', 'dorms_close_date')),
            'timezone'  => core_date::get_server_timezone_object()
        );

        $fields = array('' => array(
            'id' => self::ELEMENT_HIDDEN_INT,
            'dep_id' => self::ELEMENT_HIDDEN_INT,
            'ret_id' => self::ELEMENT_HIDDEN_INT,
            'timecreated' => self::ELEMENT_HIDDEN_INT,
            'isstudent' => self::ELEMENT_HIDDEN_INT
        ), 'info' => array(
            'student' => array('element' => 'select', 'options' => $students),
            'destination' => self::ELEMENT_TEXT,
            'phone' => self::ELEMENT_TEXT
        ), 'departure' => array(
            'dep_mxtransportation' => self::ELEMENT_BOOLEAN,
            'dep_type' => array('element' => 'radio', 'options' => $types),
            'dep_site' => array('element' => 'radio', 'options' => $depsites, 'useradioindex' => true),
            'dep_details' => self::ELEMENT_TEXT,
            'dep_carrier' => self::ELEMENT_TEXT,
            'dep_number' => self::ELEMENT_TEXT,
            'dep_variable' => array('element' => 'group', 'children' => array(
                'time' => self::time_selector(15),
                'date' => array('element' => 'date_selector', 'parameters' => $dateparameters)
            )), 'dep_international' => self::ELEMENT_BOOLEAN
        ));
        if ($this->returnenabled) {
            $fields['return'] = array(
                'ret_mxtransportation' => self::ELEMENT_BOOLEAN,
                'ret_type' => array('element' => 'radio', 'options' => $types),
                'ret_site' => array('element' => 'radio', 'options' => $retsites, 'useradioindex' => true),
                'ret_details' => self::ELEMENT_TEXT,
                'ret_carrier' => self::ELEMENT_TEXT,
                'ret_number' => self::ELEMENT_TEXT,
                'ret_variable' => array('element' => 'group', 'children' => array(
                    'time' => self::time_selector(15),
                    'date' => array('element' => 'date_selector', 'parameters' => $dateparameters)
                )), 'ret_international' => self::ELEMENT_BOOLEAN
            );
        }
        $this->set_fields($fields, 'vacation_travel_form');

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
            $errors['destination'] = get_string('vacation_travel_form_error_nodestination', 'local_mxschool');
        }
        if (empty($data['phone'])) {
            $errors['phone'] = get_string('vacation_travel_form_error_nophone', 'local_mxschool');
        }
        if (!isset($data['dep_mxtransportation'])) {
            $errors['dep_mxtransportation'] = get_string('vacation_travel_form_error_nomxtransportation', 'local_mxschool');
        } else {
            if (!isset($data['dep_type'])) {
                $errors['dep_type'] = get_string('vacation_travel_form_error_notype', 'local_mxschool');
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
                        $errors['dep_site'] = get_string('vacation_travel_form_error_noairport', 'local_mxschool');
                    } else if ($data['dep_type'] === 'Train') {
                        $errors['dep_site'] = get_string('vacation_travel_form_error_nostation', 'local_mxschool');
                    } else {
                        $errors['dep_site'] = get_string('vacation_travel_form_error_nosite', 'local_mxschool');
                    }
                } else if (empty($data['dep_details'])) {
                    if ($data['dep_type'] === 'Car') {
                        $errors['dep_details'] = get_string('vacation_travel_form_error_nodriver', 'local_mxschool');
                    } else if ($data['dep_type'] === 'Non-MX Bus') {
                        $errors['dep_details'] = get_string('vacation_travel_form_error_nodetails', 'local_mxschool');
                    } else if ($data['dep_mxtransportation'] && $data['dep_site'] === '0') {
                        $errors['dep_details'] = get_string('vacation_travel_form_error_noother', 'local_mxschool');
                    }
                }
                if ($data['dep_type'] === 'Plane' || $data['dep_type'] === 'Bus' || $data['dep_type'] === 'Train') {
                    if (empty($data['dep_carrier'])) {
                        $errors['dep_carrier'] = get_string(
                            "vacation_travel_form_error_nocarrier_{$data['dep_type']}", 'local_mxschool'
                        );
                    }
                    if (empty($data['dep_number'])) {
                        $errors['dep_number'] = get_string(
                            "vacation_travel_form_error_nonumber_{$data['dep_type']}", 'local_mxschool'
                        );
                    }
                }
                if ($data['dep_mxtransportation'] && $data['dep_type'] === 'Plane' && !isset($data['dep_international'])) {
                    $errors['dep_international'] = get_string('vacation_travel_form_error_nointernational_dep', 'local_mxschool');
                }
            }
        }
        if ($this->returnenabled) {
            if (!isset($data['ret_mxtransportation'])) {
                $errors['ret_mxtransportation'] = get_string('vacation_travel_form_error_nomxtransportation', 'local_mxschool');
            } else {
                if (!isset($data['ret_type'])) {
                    $errors['ret_type'] = get_string('vacation_travel_form_error_notype', 'local_mxschool');
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
                            $errors['ret_site'] = get_string('vacation_travel_form_error_noairport', 'local_mxschool');
                        } else if ($data['ret_type'] === 'Train') {
                            $errors['ret_site'] = get_string('vacation_travel_form_error_nostation', 'local_mxschool');
                        } else {
                            $errors['ret_site'] = get_string('vacation_travel_form_error_nosite', 'local_mxschool');
                        }
                    } else if (empty($data['ret_details'])) {
                        if ($data['ret_type'] === 'Car') {
                            $errors['ret_details'] = get_string('vacation_travel_form_error_nodriver', 'local_mxschool');
                        } else if ($data['ret_type'] === 'Non-MX Bus') {
                            $errors['ret_details'] = get_string('vacation_travel_form_error_nodetails', 'local_mxschool');
                        } else if ($data['ret_mxtransportation'] && $data['ret_site'] === '0') {
                            $errors['ret_details'] = get_string('vacation_travel_form_error_noother', 'local_mxschool');
                        }
                    }
                    if ($data['ret_type'] === 'Plane' || $data['ret_type'] === 'Bus' || $data['ret_type'] === 'Train') {
                        if (empty($data['ret_carrier'])) {
                            $errors['ret_carrier'] = get_string(
                                "vacation_travel_form_error_nocarrier_{$data['ret_type']}", 'local_mxschool'
                            );
                        }
                        if (empty($data['ret_number'])) {
                            $errors['ret_number'] = get_string(
                                "vacation_travel_form_error_nonumber_{$data['ret_type']}", 'local_mxschool'
                            );
                        }
                    }
                    if ($data['ret_mxtransportation'] && $data['ret_type'] === 'Plane' && !isset($data['ret_international'])) {
                        $errors['ret_international'] = get_string(
                            'vacation_travel_form_error_nointernational_ret', 'local_mxschool'
                        );
                    }
                }
            }
            if (isset($depdatetime) && isset($retdatetime) && $depdatetime >= $retdatetime) {
                $errors['ret_variable'] = get_string('vacation_travel_form_error_outoforder', 'local_mxschool');
            }
        }
        return $errors;
    }

}
