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
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\healthpass;

defined('MOODLE_INTERNAL') || die();

class form extends \local_mxschool\form {

    /**
     * Form definition.
     */
    protected function definition() {
        $temps = array('<95', '96', '97', '98', '99', '100', '101', '102', '103', '104', '104+');
        $students = get_student_list();

        $fields = array(
            '' => array(
                'id' => self::ELEMENT_HIDDEN_INT,
                'timecreated' => self::ELEMENT_HIDDEN_INT,
                'isstudent' => self::ELEMENT_HIDDEN_INT
            ),
            'health_info' => array(
                'userid' => array('element' => 'select', 'options' => $students),
                'body_temperature' => array('element' => 'select', 'options' => $temps),
                'anyone_sick_at_home' => self::ELEMENT_BOOLEAN,
            ),
            'symptoms' => array(
                'has_fever' => self::ELEMENT_BOOLEAN,
                'has_sore_throat' => self::ELEMENT_BOOLEAN,
                'has_cough' => self::ELEMENT_BOOLEAN,
                'has_runny_nose' => self::ELEMENT_BOOLEAN,
                'has_muscle_aches' => self::ELEMENT_BOOLEAN,
                'has_loss_of_sense' => self::ELEMENT_BOOLEAN,
                'has_short_breath' => self::ELEMENT_BOOLEAN,
            )
        );
        $this->set_fields($fields, 'healthpass:form');

        $mform = $this->_form;
        $mform->hideIf('student', 'isstudent', 'eq');
        $mform->disabledIf('student', 'id', 'neq', '0');
    }
}
