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
 * Form for editting dorm data for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_form.php');

class dorm_edit_form extends local_mxschool_form {

    /**
     * Form definition.
     */
    protected function definition() {
        $id = $this->_customdata['id'];
        $faculty = $this->_customdata['faculty'];

        $fields = array(
            '' => array('id' => parent::ELEMENT_HIDDEN_INT),
            'dorm' => array(
                'name' => parent::ELEMENT_TEXT_REQUIRED,
                'abbreviation' => parent::ELEMENT_TEXT_REQUIRED,
                'hoh' => array('element' => 'select', 'options' => $faculty, 'rules' => array('required')),
                'permissionsline' => parent::ELEMENT_TEXT_REQUIRED,
                'type' => array('element' => 'radio', 'options' => array('Boarding', 'Day', 'All'), 'rules' => array('required')),
                'gender' => array('element' => 'radio', 'options' => array('Boys', 'Girls', 'All'), 'rules' => array('required')),
                'available' => parent::ELEMENT_YES_NO_REQUIRED
            )
        );
        parent::set_fields($fields, 'dorm_edit');
    }

}
