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
 * Form for students to sign out for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_form.php');

class esignout_form extends local_mxschool_form {

    /**
     * Form definition.
     */
    protected function definition() {
        $id = $this->_customdata['id'];
        $students = $this->_customdata['students'];
        $types = $this->_customdata['types'];
        $passengers = $this->_customdata['passengers'];
        $drivers = $this->_customdata['drivers'];
        $approvers = $this->_customdata['approvers'];

        $passengerparameters = array(
            'multiple' => true, 'noselectionstring' => get_string('esignout_form_passengers_noselection', 'local_mxschool'),
            'placeholder' => get_string('esignout_form_passengers_placeholder', 'local_mxschool')
        );

        $fields = array(
            '' => array(
                'id' => parent::ELEMENT_HIDDEN_INT,
                'timecreated' => parent::ELEMENT_HIDDEN_INT,
                'date' => parent::ELEMENT_HIDDEN_INT,
                'isstudent' => parent::ELEMENT_HIDDEN_INT
            ), 'info' => array(
                'student' => array('element' => 'select', 'options' => $students),
                'type' => array('element' => 'group', 'children' => array(
                    'select' => array('element' => 'radio', 'options' => $types),
                    'other' => parent::ELEMENT_TEXT
                )), 'passengers' => array(
                    'element' => 'autocomplete', 'options' => $passengers, 'parameters' => $passengerparameters
                ), 'passengerswarning' => array(
                    'element' => 'static', 'name' => 'passengers',
                    'text' => get_string('esignout_form_passengers_warning', 'local_mxschool')
                ), 'driver' => array('element' => 'select', 'options' => $drivers)
            ), 'details' => array(
                'destination' => array('element' => 'text', 'type' => PARAM_TEXT, 'attributes' => array('size' => 40)),
                'departuretime' => parent::time_selector(15),
                'approver' => array('element' => 'select', 'options' => $approvers)
            )
        );
        parent::set_fields($fields, 'esignout_form', false);

        $mform = $this->_form;
        $mform->addElement('header', 'permissions', get_string('esignout_form_header_permissions', 'local_mxschool'));
        $mform->setExpanded('permissions');
        $mform->addElement('static', 'parentwarning', '', get_string('esignout_form_parent_warning', 'local_mxschool'));
        $mform->addElement('static', 'specificwarning', '', get_string('esignout_form_specific_warning', 'local_mxschool'));
        $buttonarray = array(
            $mform->createElement(
                'submit', 'permissionssubmityes', get_string('esignout_form_permissions_submit_yes', 'local_mxschool')
            ), $mform->createElement(
                'cancel', 'permissionssubmitno', get_string('esignout_form_permissions_submit_no', 'local_mxschool')
            )
        );
        $mform->addGroup(
            $buttonarray, 'permissionssubmitbuttons', get_string('esignout_form_permissions_submit', 'local_mxschool'), ' ', false
        );

        $mform->hideIf('student', 'isstudent', 'eq');
        $mform->disabledIf('student', 'id', 'neq', '0');
        $mform->disabledIf('type', 'id', 'neq', '0');
        $mform->disabledIf('destination', 'type_select', 'eq', 'Passenger');
        $mform->disabledIf('departuretime', 'type_select', 'eq', 'Passenger');
    }

    /**
     * Validates the weekend form before it can be submitted.
     * The checks performed are...
     *
     * @return array of errors as "element_name"=>"error_description" or an empty array if there are no errors.
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
        if (!isset($data['type_select'])) {
            $errors['type'] = get_string('esignout_form_error_notype', 'local_mxschool');
        } else if ($data['type_select'] === 'Other' && $data['type_other'] === '') {
            $errors['type'] = get_string('esignout_form_error_notype', 'local_mxschool');
        } else if ($data['type_select'] === 'Passenger' && !$data['driver']) {
            $errors['driver'] = get_string('esignout_form_error_nodriver', 'local_mxschool');
        }
        if ($data['destination'] === '') {
            $errors['destination'] = get_string('esignout_form_error_nodestination', 'local_mxschool');
        }
        if (!$data['approver']) {
            $errors['approver'] = get_string('esignout_form_error_noapprover', 'local_mxschool');
        }
        return $errors;
    }

}
