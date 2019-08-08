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
 * Form for students to sign out to an off-campus location for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @subpackage  off_campus
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_signout\local\off_campus;

defined('MOODLE_INTERNAL') || die();

class form extends \local_mxschool\form {

    /**
     * Form definition.
     */
    protected function definition() {
        $students = $this->_customdata['students'];
        $types = $this->_customdata['types'];
        $passengers = $this->_customdata['passengers'];
        $drivers = $this->_customdata['drivers'];
        $approvers = $this->_customdata['approvers'];

        $passengerparameters = array(
            'multiple' => true, 'noselectionstring' => get_string('off_campus_form_passengers_noselection', 'local_signout'),
            'placeholder' => get_string('off_campus_form_passengers_placeholder', 'local_signout')
        );

        $fields = array(
            '' => array(
                'id' => self::ELEMENT_HIDDEN_INT,
                'timecreated' => self::ELEMENT_HIDDEN_INT,
                'departure_date' => self::ELEMENT_HIDDEN_INT,
                'isstudent' => self::ELEMENT_HIDDEN_INT
            ),
            'info' => array(
                'student' => array('element' => 'select', 'options' => $students),
                'type' => array('element' => 'group', 'children' => array(
                    'select' => array('element' => 'select', 'options' => $types),
                    'other' => self::ELEMENT_TEXT
                )),
                'passengers' => array(
                    'element' => 'autocomplete', 'options' => $passengers, 'parameters' => $passengerparameters
                ),
                'driverwarning' => array('element' => 'static', 'name' => 'passengers'),
                'instructions' => array('element' => 'static', 'name' => null),
                'driver' => array('element' => 'select', 'options' => $drivers)
            ),
            'details' => array(
                'destination' => array('element' => 'text', 'type' => PARAM_TEXT, 'attributes' => array('size' => 40)),
                'departure_time' => self::time_selector(15),
                'approver' => array('element' => 'select', 'options' => $approvers)
            ),
            'permissions' => array(
                'passengerwarning' => array('element' => 'static', 'name' => null),
                'ridesharewarning' => array('element' => 'static', 'name' => null),
                'permissionssubmitbuttons' => array(
                    'element' => 'group', 'displayname' => get_config('local_signout', 'off_campus_form_confirmation'),
                    'children' => array(
                        'permissionssubmityes' => array('element' => 'submit', 'text' => get_string('yes')),
                        'permissionssubmitno' => array('element' => 'cancel', 'text' => get_string('no'))
                    )
                )
            )
        );
        $this->set_fields($fields, 'off_campus_form', false, 'local_signout');

        $mform = $this->_form;
        $mform->hideIf('student', 'isstudent', 'eq');
        $mform->disabledIf('student', 'id', 'neq', '0');
        $mform->hideIf('type_other', 'type_select', 'neq', '-1');
        $mform->disabledIf('type', 'id', 'neq', '0');
    }

    /**
     * Validates the off-campus signout form before it can be submitted.
     * The checks performed are to ensure that all required fields are filled out.
     * Permissions checks are done in JavaScript.
     *
     * @return array of errors as "element_name"=>"error_description" or an empty array if there are no errors.
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
        if (!$data['type_select'] || ($data['type_select'] === '-1' && empty($data['type_other']))) {
            $errors['type'] = get_string('off_campus_form_error_notype', 'local_signout');
        }
        if (empty($data['destination'])) {
            $errors['destination'] = get_string('off_campus_form_error_nodestination', 'local_signout');
        }
        $permissions = $DB->get_field('local_signout_type', 'required_permissions', array('id' => $data['type_select']));
        if ($permissions === 'passenger' && !$data['driver']) {
            $errors['driver'] = get_string('off_campus_form_error_nodriver', 'local_signout');
        }
        if ($permissions || $data['type_select'] === '-1') {
            $errors['approver'] = get_string('off_campus_form_error_noapprover', 'local_signout');
        }
        return $errors;
    }

}
