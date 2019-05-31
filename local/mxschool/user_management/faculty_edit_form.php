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
 * Form for editing faculty data for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage user_management
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_form.php');

class faculty_edit_form extends local_mxschool_form {

    /**
     * Form definition.
     */
    protected function definition() {
        $id = $this->_customdata['id'];
        $dorms = $this->_customdata['dorms'];

        $fields = array(
            '' => array(
                'id' => self::ELEMENT_HIDDEN_INT,
                'userid' => self::ELEMENT_HIDDEN_INT
            ), 'faculty' => array(
                'firstname' => self::ELEMENT_TEXT_REQUIRED,
                'middlename' => self::ELEMENT_TEXT,
                'lastname' => self::ELEMENT_TEXT_REQUIRED,
                'alternatename' => self::ELEMENT_TEXT,
                'email' => self::ELEMENT_EMAIL_REQUIRED,
                'facultycode' => self::ELEMENT_TEXT,
                'dorm' => array('element' => 'select', 'options' => $dorms),
                'approvesignout' => self::ELEMENT_BOOLEAN_REQUIRED,
                'advisoryavailable' => self::ELEMENT_BOOLEAN_REQUIRED,
                'advisoryclosing' => self::ELEMENT_BOOLEAN_REQUIRED
            )
        );
        $this->set_fields($fields, 'faculty_edit');
    }

}
