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
 * Edit form for tutor records for Middlesex's Peer Tutoring Subplugin.
 *
 * @package     local_peertutoring
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../mxschool/classes/mx_form.php');

class tutor_edit_form extends local_mxschool_form {

    /**
     * Form definition.
     */
    protected function definition() {
        $id = $this->_customdata['id'];
        $students = $this->_customdata['students'];
        $departments = $this->_customdata['departments'];

        $departmentsparameters = array(
            'multiple' => true,
            'noselectionstring' => get_string('tutor_edit_form_departments_noselection', 'local_peertutoring'),
            'placeholder' => get_string('tutor_edit_form_departments_placeholder', 'local_peertutoring')
        );

        $fields = array(
            '' => array('id' => self::ELEMENT_HIDDEN_INT),
            'tutor' => array(
                'student' => array('element' => 'select', 'options' => $students),
                'departments' => array(
                    'element' => 'autocomplete', 'options' => $departments, 'parameters' => $departmentsparameters
                )
            )
        );
        $this->set_fields($fields, 'tutor_edit', false, 'local_peertutoring');

        $mform = $this->_form;
        $mform->disabledIf('student', 'id', 'neq', '0');
    }
}
