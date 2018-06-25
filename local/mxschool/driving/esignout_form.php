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
        $drivers = $this->_customdata['drivers'];
        $approvers = $this->_customdata['approvers'];

        $fields = array(
            '' => array(
                'id' => parent::ELEMENT_HIDDEN_INT,
                'timecreated' => parent::ELEMENT_HIDDEN_INT,
                'date' => parent::ELEMENT_HIDDEN_INT,
                'isstudent' => parent::ELEMENT_HIDDEN_INT
            ), 'info' => array(
                'student' => array('element' => 'select', 'options' => $students),
                'type' => array('element' => 'radio', 'options' => array('Driver', 'Passenger')),
                'driver' => array('element' => 'select', 'options' => $drivers)
            ), 'details' => array(
                'destination' => array('element' => 'text', 'type' => PARAM_TEXT, 'attributes' => array('size' => 40)),
                'departuretime' => parent::time_selector(15),
                'approver' => array('element' => 'select', 'options' => $approvers)
            )
        );
        parent::set_fields($fields, 'esignout_form', false);

        $mform = $this->_form;
        $mform->hideIf('student', 'isstudent', 'eq');
        $mform->disabledIf('student', 'id', 'neq', '0');
        $mform->disabledIf('type', 'id', 'neq', '0');
        $mform->hideIf('driver', 'type', 'neq', 'Passenger');
        $mform->disabledIf('destination', 'type', 'eq', 'Passenger');
        $mform->disabledIf('departuretime', 'type', 'eq', 'Passenger');
    }

    // TODO: Validation.

}
