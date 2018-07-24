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
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_form.php');

class vacation_form extends local_mxschool_form {

    /**
     * Form definition.
     */
    protected function definition() {
        $id = $this->_customdata['id'];
        $students = $this->_customdata['students'];
        $depsites = $this->_customdata['depsites'];
        $retsites = $this->_customdata['retsites'];
        $types = array('Car', 'Plane', 'Bus', 'Train', 'NYC Direct', 'Non-MX Bus');

        $dateparameters = array(
            'startyear' => strftime('%Y', get_config('local_mxschool', 'dorms_open_date')),
            'stopyear' => strftime('%Y', get_config('local_mxschool', 'dorms_close_date')),
            'timezone'  => core_date::get_server_timezone_object()
        );

        $fields = array('' => array(
            'id' => parent::ELEMENT_HIDDEN_INT,
            'dep_id' => parent::ELEMENT_HIDDEN_INT,
            'ret_id' => parent::ELEMENT_HIDDEN_INT,
            'timecreated' => parent::ELEMENT_HIDDEN_INT,
            'isstudent' => parent::ELEMENT_HIDDEN_INT
        ), 'info' => array(
            'student' => array('element' => 'select', 'options' => $students),
            'dorm' => array('element' => 'static'),
            'destination' => parent::ELEMENT_TEXT,
            'phone' => parent::ELEMENT_TEXT
        ), 'departure' => array(
            'dep_campus' => array('element' => 'group', 'separator' => '&nbsp;', 'children' => array(
                'time' => parent::time_selector(15),
                'date' => array('element' => 'date_selector', 'parameters' => $dateparameters)
            )),
            'dep_mxtransportation' => parent::ELEMENT_BOOLEAN,
            'dep_type' => array('element' => 'radio', 'options' => $types),
            'dep_site' => array('element' => 'group', 'children' => array(
                'radio' => array('element' => 'radio', 'options' => $depsites, 'useradioindex' => true),
                'other' => parent::ELEMENT_TEXT
            )),
            'dep_carrier' => parent::ELEMENT_TEXT,
            'dep_number' => parent::ELEMENT_TEXT,
            'dep_transportation' => array('element' => 'group', 'separator' => '&nbsp;', 'children' => array(
                'time' => parent::time_selector(15),
                'date' => array('element' => 'date_selector', 'parameters' => $dateparameters)
            )),
            'dep_international' => parent::ELEMENT_BOOLEAN
        ), 'return' => array(
            'ret_campus' => array('element' => 'group', 'separator' => '&nbsp;', 'children' => array(
                'time' => parent::time_selector(15),
                'date' => array('element' => 'date_selector', 'parameters' => $dateparameters)
            )),
            'ret_mxtransportation' => parent::ELEMENT_BOOLEAN,
            'ret_type' => array('element' => 'radio', 'options' => $types),
            'ret_site' => array('element' => 'group', 'children' => array(
                'radio' => array('element' => 'radio', 'options' => $retsites, 'useradioindex' => true),
                'other' => parent::ELEMENT_TEXT
            )),
            'ret_carrier' => parent::ELEMENT_TEXT,
            'ret_number' => parent::ELEMENT_TEXT,
            'ret_transportation' => array('element' => 'group', 'separator' => '&nbsp;', 'children' => array(
                'time' => parent::time_selector(15),
                'date' => array('element' => 'date_selector', 'parameters' => $dateparameters)
            )),
            'ret_international' => parent::ELEMENT_BOOLEAN
        ));
        parent::set_fields($fields, 'vacation_travel_form');

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
        if ($data['destination'] === '') {
            $errors['destination'] = get_string('vacation_travel_form_error_nodestination', 'local_mxschool');
        }
        if ($data['phone'] === '') {
            $errors['phone'] = get_string('vacation_travel_form_error_nophone', 'local_mxschool');
        }
        if (!isset($data['dep_mxtransportation'])) {
            $errors['dep_mxtransportation'] = get_string('vacation_travel_form_error_nomxtransportation', 'local_mxschool');
        } else {
            if (!isset($data['dep_type'])) {
                $errors['dep_type'] = get_string('vacation_travel_form_error_notype', 'local_mxschool');
            } else {
                if (!isset($data['dep_site_radio'])) {
                    $errors['dep_site'] = get_string('vacation_travel_form_error_nosite', 'local_mxschool');
                } else if ($data['dep_site_radio'] === '0' && $data['dep_site_other'] === '') {
                    if ($data['dep_type'] === 'Car') {
                        $errors['dep_site'] = get_string('vacation_travel_form_error_nodriver', 'local_mxschool');
                    } else if ($data['dep_type'] === 'Non-MX Bus') {
                        $errors['dep_site'] = get_string('vacation_travel_form_error_nodetails', 'local_mxschool');
                    } else {
                        $errors['dep_site'] = get_string('vacation_travel_form_error_nosite', 'local_mxschool');
                    }
                }
                if ($data['dep_type'] === 'Plane') {
                    if ($data['dep_carrier'] === '') {
                        $errors['dep_carrier'] = get_string('vacation_travel_form_error_nocarrier_Plane', 'local_mxschool');
                    }
                    if ($data['dep_number'] === '') {
                        $errors['dep_number'] = get_string('vacation_travel_form_error_nonumber_Plane', 'local_mxschool');
                    }
                    if (!isset($data['dep_international'])) {
                        $errors['dep_international'] =
                        get_string('vacation_travel_form_error_nointernational_dep', 'local_mxschool');
                    }
                } else if ($data['dep_type'] === 'Bus') {
                    if ($data['dep_carrier'] === '') {
                        $errors['dep_carrier'] = get_string('vacation_travel_form_error_nocarrier_Bus', 'local_mxschool');
                    }
                    if ($data['dep_number'] === '') {
                        $errors['dep_number'] = get_string('vacation_travel_form_error_nonumber_Bus', 'local_mxschool');
                    }
                } else if ($data['dep_type'] === 'Train') {
                    if ($data['dep_carrier'] === '') {
                        $errors['dep_carrier'] = get_string('vacation_travel_form_error_nocarrier_Train', 'local_mxschool');
                    }
                    if ($data['dep_number'] === '') {
                        $errors['dep_number'] = get_string('vacation_travel_form_error_nonumber_Train', 'local_mxschool');
                    }
                }
            }
        }
        if (!isset($data['ret_mxtransportation'])) {
            $errors['ret_mxtransportation'] = get_string('vacation_travel_form_error_nomxtransportation', 'local_mxschool');
        } else {
            if (!isset($data['ret_type'])) {
                $errors['ret_type'] = get_string('vacation_travel_form_error_notype', 'local_mxschool');
            } else {
                if (!isset($data['ret_site_radio'])) {
                    $errors['ret_site'] = get_string('vacation_travel_form_error_nosite', 'local_mxschool');
                } else if ($data['ret_type'] === 'Plane') {
                    if ($data['ret_carrier'] === '') {
                        $errors['ret_carrier'] = get_string('vacation_travel_form_error_nocarrier_Plane', 'local_mxschool');
                    }
                    if ($data['ret_number'] === '') {
                        $errors['ret_number'] = get_string('vacation_travel_form_error_nonumber_Plane', 'local_mxschool');
                    }
                    if (!isset($data['ret_international'])) {
                        $errors['ret_international'] =
                        get_string('vacation_travel_form_error_nointernational_ret', 'local_mxschool');
                    }
                } else if ($data['ret_type'] === 'Bus') {
                    if ($data['ret_carrier'] === '') {
                        $errors['ret_carrier'] = get_string('vacation_travel_form_error_nocarrier_Bus', 'local_mxschool');
                    }
                    if ($data['ret_number'] === '') {
                        $errors['ret_number'] = get_string('vacation_travel_form_error_nonumber_Bus', 'local_mxschool');
                    }
                } else if ($data['ret_type'] === 'Train') {
                    if ($data['ret_carrier'] === '') {
                        $errors['ret_carrier'] = get_string('vacation_travel_form_error_nocarrier_Train', 'local_mxschool');
                    }
                    if ($data['ret_number'] === '') {
                        $errors['ret_number'] = get_string('vacation_travel_form_error_nonumber_Train', 'local_mxschool');
                    }
                }
            }
        }
        return $errors;
    }

}
