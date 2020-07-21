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
 * Form for editing student data for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  user_management
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\user_management;

defined('MOODLE_INTERNAL') || die();

class student_edit_form extends \local_mxschool\form {

    /**
     * Form definition.
     */
    protected function definition() {
        $dorms = $this->_customdata['dorms'];
        $faculty = $this->_customdata['faculty'];

        $dateoptions = array(
            'startyear' => 2000, // Completely arbitrary.
            'stopyear' => format_date('Y'),
            'timezone' => \core_date::get_user_timezone_object(),
            'optional' => true
        );

        $fields = array(
            '' => array(
                'id' => self::ELEMENT_HIDDEN_INT,
                'userid' => self::ELEMENT_HIDDEN_INT,
                'permissionsid' => self::ELEMENT_HIDDEN_INT
            ),
            'student' => array(
                'firstname' => self::ELEMENT_TEXT_REQUIRED,
                'middlename' => self::ELEMENT_TEXT,
                'lastname' => self::ELEMENT_TEXT_REQUIRED,
                'alternatename' => self::ELEMENT_TEXT,
                'email' => self::ELEMENT_EMAIL_REQUIRED,
                'phone_number' => self::ELEMENT_TEXT,
                'birthday' => self::ELEMENT_TEXT_REQUIRED,
                'admission_year' => self::ELEMENT_TEXT_REQUIRED,
                'grade' => array('element' => 'radio', 'options' => array(9, 10, 11, 12), 'rules' => array('required')),
                'gender' => array('element' => 'radio', 'options' => array('M', 'F'), 'rules' => array('required')),
                'advisor' => array('element' => 'select', 'options' => $faculty, 'rules' => array('required')),
                'is_boarder' => array('element' => 'radio', 'options' => array('Boarder', 'Day'), 'rules' => array('required')),
                'is_boarder_next_year' => array(
                    'element' => 'radio', 'options' => array('Boarder', 'Day'), 'rules' => array('required')
                ),
                'dorm' => array('element' => 'select', 'options' => $dorms, 'rules' => array('required')),
                'room' => self::ELEMENT_TEXT,
                'picture' => self::ELEMENT_TEXT
            ),
            'permissions' => array(
			 'overnight' => array('element' => 'radio', 'options' => array('Host', 'Parent')),
                'may_drive_with_over_21' => array('element' => 'radio', 'options' => array('Yes', 'No', 'Parent')),
                'may_drive_with_anyone' => array('element' => 'radio', 'options' => array('Yes', 'No', 'Parent')),
                'may_use_rideshare' => array('element' => 'radio', 'options' => array('Yes', 'No', 'Parent')),
                'may_travel_to_regional_cities' => array('element' => 'radio', 'options' => array('Yes', 'No', 'Parent')),
                'may_drive_passengers' => array('element' => 'radio', 'options' => array('Yes', 'No', 'Parent')),
                'swim_allowed' => self::ELEMENT_YES_NO,
                'boat_allowed' => self::ELEMENT_YES_NO
            )
        );
        $this->set_fields($fields, 'user_management:student_edit', true);

        $mform = $this->_form;
        $mform->hideIf('ridingcomment', 'riding', 'neq', 'specific');
    }

}
