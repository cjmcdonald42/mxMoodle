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
 * Form for editing dorm data for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  user_management
 * @author      mxMoodle Development Team
 * @copyright   2022 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\user_management;

defined('MOODLE_INTERNAL') || die();

class dorm_edit_form extends \local_mxschool\form {

    /**
     * Form definition.
     */
    protected function definition() {
        $faculty = $this->_customdata['faculty'];

        $fields = array(
            '' => array(
                'id' => self::ELEMENT_HIDDEN_INT
            ),
            'dorm' => array(
                'name' => self::ELEMENT_TEXT_REQUIRED,
                'hoh' => array('element' => 'select', 'options' => $faculty, 'rules' => array('required')),
                'permissions_line' => self::ELEMENT_TEXT_REQUIRED,
                'dorm_log' => self::ELEMENT_TEXT_REQUIRED,
                'type' => array('element' => 'radio', 'options' => array('Boarding', 'Day', 'All'), 'rules' => array('required')),
                'gender' => array('element' => 'radio', 'options' => array('Boys', 'Girls', 'Ungendered'), 'rules' => array('required')),
                'available' => self::ELEMENT_BOOLEAN_REQUIRED
            )
        );
        $this->set_fields($fields, 'user_management:dorm_edit');
    }

}
