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
 * Form for editting student data for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
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
        $advisors = $this->_customdata['advisors'];
        $grades = array(9 => 9, 10 => 10, 11 => 11, 12 => 12);

        $hidden = array('id', 'userid', 'permissionsid');
        $fields = array(
            'student' => array(
                'firstname' => parent::ELEMENT_TEXT,
                'middlename' => parent::ELEMENT_TEXT,
                'lastname' => parent::ELEMENT_TEXT,
                'alternatename' => parent::ELEMENT_TEXT,
                'email' => parent::ELEMENT_TEXT,
                'admissionyear' => array('element' => 'text', 'type' => PARAM_INT),
                'grade' => array('element' => 'select', 'type' => PARAM_INT, 'options' => $grades),
                'gender' => parent::ELEMENT_TEXT,
                'advisor' => array('element' => 'select', 'type' => PARAM_INT, 'options' => $advisors),
                'dorm' => array('element' => 'select', 'type' => PARAM_INT, 'options' => $dorms),
                'room' => parent::ELEMENT_TEXT,
                'phonenumber' => parent::ELEMENT_TEXT,
                'birthdate' => parent::ELEMENT_TEXT
            ), 'permissions' => array(
                'overnight' => array('element' => 'radio', 'options' => array('Parent', 'Host')),
                'riding' => array('element' => 'radio', 'options' => array('Parent Permission', 'Over 21', 'Any Driver', 'Specific Drivers')),
                'comment' => parent::ELEMENT_TEXT,
                'rideshare' => array('element' => 'radio', 'options' => array('Yes', 'No', 'Parent')),
                'boston' => array('element' => 'radio', 'options' => array('Yes', 'No', 'Parent')),
                'town' => parent::ELEMENT_YES_NO,
                'passengers' => parent::ELEMENT_YES_NO,
                'swimcompetent' => parent::ELEMENT_YES_NO,
                'swimallowed' => parent::ELEMENT_YES_NO,
                'boatallowed' => parent::ELEMENT_YES_NO
            )
        );

        parent::set_fields($hidden, $fields, 'student_edit');
    }
}
