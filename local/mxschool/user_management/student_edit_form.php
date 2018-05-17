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

require_once($CFG->libdir.'/formslib.php');

class student_edit_form extends moodleform {

    private const ELEMENT_TEXT = array('element' => 'text', 'type' => PARAM_TEXT);
    private const ELEMENT_YES_NO = array('element' => 'radio', 'options' => array('Yes', 'No'));

    /**
     * Form definition.
     */
    protected function definition() {
        $mform = $this->_form;
        $id = $this->_customdata['id'];
        $dorms = $this->_customdata['dorms'];
        $advisors = $this->_customdata['advisors'];
        $grades = array(9 => 9, 10 => 10, 11 => 11, 12 => 12);

        $hidden = array('id', 'userid', 'dormid', 'advisorid', 'permissionsid');
        $fields = array(
            'student' => array(
                'firstname' => self::ELEMENT_TEXT,
                'middlename' => self::ELEMENT_TEXT,
                'lastname' => self::ELEMENT_TEXT,
                'alternatename' => self::ELEMENT_TEXT,
                'email' => self::ELEMENT_TEXT,
                'admissionyear' => array('element' => 'text', 'type' => PARAM_INT),
                'grade' => array('element' => 'select', 'type' => PARAM_INT, 'options' => $grades),
                'gender' => self::ELEMENT_TEXT,
                'advisor' => array('element' => 'select', 'type' => PARAM_INT, 'options' => $advisors),
                'dorm' => array('element' => 'select', 'type' => PARAM_INT, 'options' => $dorms),
                'room' => self::ELEMENT_TEXT,
                'phonenumber' => self::ELEMENT_TEXT,
                'birthdate' => self::ELEMENT_TEXT
            ), 'permissions' => array(
                'overnight' => array('element' => 'radio', 'options' => array('Parent', 'Host')),
                'riding' => array('element' => 'radio', 'options' => array('Parent Permission', 'Over 21', 'Any Driver', 'Specific Drivers')),
                'comment' => self::ELEMENT_TEXT,
                'rideshare' => array('element' => 'radio', 'options' => array('Yes', 'No', 'Parent')),
                'boston' => array('element' => 'radio', 'options' => array('Yes', 'No', 'Parent')),
                'town' => self::ELEMENT_YES_NO,
                'passengers' => self::ELEMENT_YES_NO,
                'swimcompetent' => self::ELEMENT_YES_NO,
                'swimallowed' => self::ELEMENT_YES_NO,
                'boatallowed' => self::ELEMENT_YES_NO
            )
        );

        $this->add_action_buttons();
        foreach ($hidden as $name) {
            $mform->addElement('hidden', $name, null);
            $mform->setType($name, PARAM_INT);
        }
        foreach ($fields as $category => $categoryfields) {
            $mform->addElement('header', $category, get_string("student_edit_header_{$category}", 'local_mxschool'));
            foreach ($categoryfields as $name => $element) {
                $displayname = get_string("student_edit_{$category}_{$name}", 'local_mxschool');
                switch($element['element']) {
                    case 'select':
                        $mform->addElement($element['element'], $name, $displayname, $element['options']);
                        break;
                    case 'radio':
                        $buttons = array();
                        foreach ($element['options'] as $option) {
                            $buttons[] = $mform->createElement('radio', $name, '', $option, $option);
                        }
                        $mform->addGroup($buttons, "{$name}group", $displayname, array(' '), false);
                        break;
                    default:
                        $mform->addElement($element['element'], $name, $displayname);
                }
                if (isset($element['type'])) {
                    $mform->setType($name, $element['type']);
                }
            }
        }
        $this->add_action_buttons();
    }
}
