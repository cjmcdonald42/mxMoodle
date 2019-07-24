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
 * Form for students to sign out to an on-campus location for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @subpackage  on_campus
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../mxschool/classes/mx_form.php');

class on_campus_form extends local_mxschool_form {

    /**
     * Form definition.
     */
    protected function definition() {
        $id = $this->_customdata['id'];
        $students = $this->_customdata['students'];
        $locations = $this->_customdata['locations'];

        $fields = array(
            '' => array(
                'id' => self::ELEMENT_HIDDEN_INT,
                'timecreated' => self::ELEMENT_HIDDEN_INT,
                'isstudent' => self::ELEMENT_HIDDEN_INT
            ),
            'info' => array(
                'student' => array('element' => 'select', 'options' => $students),
                'location' => array('element' => 'group', 'children' => array(
                    'select' => array('element' => 'select', 'options' => $locations),
                    'other' => self::ELEMENT_TEXT
                ))
            ),
            'permissions' => array(
                'locationwarning' => array('element' => 'static', 'name' => null),
                'permissionssubmitbuttons' => array(
                    'element' => 'group', 'displayname' => get_config('local_signout', 'on_campus_form_confirmation'),
                    'children' => array(
                        'permissionssubmityes' => array('element' => 'submit', 'text' => get_string('yes')),
                        'permissionssubmitno' => array('element' => 'cancel', 'text' => get_string('no'))
                    )
                )
            )
        );
        $this->set_fields($fields, 'on_campus_form', false, 'local_signout');

        $mform = $this->_form;
        $mform->hideIf('student', 'isstudent', 'eq');
        $mform->disabledIf('student', 'id', 'neq', '0');
        $mform->hideIf('location_other', 'location_select', 'neq', '-1');
    }

    /**
     * Validates the on-campus signout form before it can be submitted.
     * The checks performed are to ensure that all required fields are filled out.
     * Permissions checks are done in JavaScript.
     *
     * @return array of errors as "element_name"=>"error_description" or an empty array if there are no errors.
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
        if (!$data['location_select'] || ($data['location_select'] === '-1' && empty($data['location_other']))) {
            $errors['location'] = get_string('on_campus_form_error_nolocation', 'local_signout');
        }
        return $errors;
    }

}
