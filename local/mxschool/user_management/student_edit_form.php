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
 * Form for editing student data for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage user_management
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_form.php');

class student_edit_form extends local_mxschool_form {

    /**
     * Form definition.
     */
    protected function definition() {
        $id = $this->_customdata['id'];
        $dorms = $this->_customdata['dorms'];
        $faculty = $this->_customdata['faculty'];

        $dateparameters = array(
            'startyear' => 2000, // Completely arbitrary.
            'stopyear' => (new DateTime('now', core_date::get_server_timezone_object()))->format('Y'),
            'timezone'  => core_date::get_server_timezone_object(),
            'optional' => true
        );

        $fields = array(
            '' => array(
                'id' => parent::ELEMENT_HIDDEN_INT,
                'userid' => parent::ELEMENT_HIDDEN_INT,
                'permissionsid' => parent::ELEMENT_HIDDEN_INT
            ), 'student' => array(
                'firstname' => parent::ELEMENT_TEXT_REQUIRED,
                'middlename' => parent::ELEMENT_TEXT,
                'lastname' => parent::ELEMENT_TEXT_REQUIRED,
                'alternatename' => parent::ELEMENT_TEXT,
                'email' => parent::ELEMENT_EMAIL_REQUIRED,
                'phonenumber' => parent::ELEMENT_TEXT,
                'birthday' => parent::ELEMENT_TEXT_REQUIRED,
                'admissionyear' => parent::ELEMENT_TEXT_REQUIRED,
                'grade' => array('element' => 'radio', 'options' => array(9, 10, 11, 12), 'rules' => array('required')),
                'gender' => array('element' => 'radio', 'options' => array('M', 'F'), 'rules' => array('required')),
                'advisor' => array('element' => 'select', 'options' => $faculty, 'rules' => array('required')),
                'isboarder' => array('element' => 'radio', 'options' => array('Boarder', 'Day'), 'rules' => array('required')),
                'isboardernextyear' => array(
                    'element' => 'radio', 'options' => array('Boarder', 'Day'), 'rules' => array('required')
                ), 'dorm' => array('element' => 'select', 'options' => $dorms, 'rules' => array('required')),
                'room' => parent::ELEMENT_TEXT
            ), 'permissions' => array(
                'overnight' => array('element' => 'radio', 'options' => array('Parent', 'Host')),
                'license' => array('element' => 'date_selector', 'parameters' => $dateparameters),
                'driving' => parent::ELEMENT_YES_NO,
                'passengers' => parent::ELEMENT_YES_NO,
                'riding' => array('element' => 'radio', 'options' => array('parent', '21', 'any', 'specific')),
                'ridingcomment' => parent::ELEMENT_TEXT_AREA,
                'rideshare' => array('element' => 'radio', 'options' => array('Yes', 'No', 'Parent')),
                'boston' => array('element' => 'radio', 'options' => array('Yes', 'No', 'Parent')),
                'swimcompetent' => parent::ELEMENT_YES_NO,
                'swimallowed' => parent::ELEMENT_YES_NO,
                'boatallowed' => parent::ELEMENT_YES_NO
            )
        );
        parent::set_fields($fields, 'student_edit', true);

        $mform = $this->_form;
        $mform->hideIf('ridingcomment', 'riding', 'neq', 'specific');
    }

}
