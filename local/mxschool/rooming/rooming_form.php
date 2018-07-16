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
 * Form for students to submit rooming requests for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_form.php');

class rooming_form extends local_mxschool_form {

    /**
     * Form definition.
     */
    protected function definition() {
        $id = $this->_customdata['id'];
        $students = $this->_customdata['students'];
        $roomable = $this->_customdata['roomable'];
        $roomtypes = array(
            'Single' => get_string('room_type_single', 'local_mxschool'),
            'Double' => get_string('room_type_double', 'local_mxschool'),
            'Triple' => get_string('room_type_triple', 'local_mxschool'),
            'Quad' => get_string('room_type_quad', 'local_mxschool')
        );

        $fields = array('' => array(
            'id' => parent::ELEMENT_HIDDEN_INT,
            'timecreated' => parent::ELEMENT_HIDDEN_INT,
            'isstudent' => parent::ELEMENT_HIDDEN_INT
        ), 'info' => array(
            'student' => array('element' => 'select', 'options' => $students),
            'dorm' => array('element' => 'static'),
            'liveddouble' => array(
                'element' => 'advcheckbox', 'name' => null,
                'text' => get_config('local_mxschool', 'rooming_form_checkbox_instructions')
            )
        ), 'requests' => array(
            'roomtype' => array('element' => 'select', 'options' => $roomtypes),
            'dormmate1' => array('element' => 'select', 'options' => $roomable),
            'dormmate2' => array('element' => 'select', 'name' => null, 'options' => $roomable),
            'dormmate3' => array('element' => 'select', 'name' => null, 'options' => $roomable),
            'dormmate4' => array('element' => 'select', 'options' => $roomable),
            'dormmate5' => array('element' => 'select', 'name' => null, 'options' => $roomable),
            'dormmate6' => array('element' => 'select', 'name' => null, 'options' => $roomable),
            'instructions' => array('element' => 'static', 'name' => null),
            'roommate' => array('element' => 'select', 'options' => $roomable)
        ));
        parent::set_fields($fields, 'rooming_form');
    }
}
