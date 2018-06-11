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
 * Weekend form for students to submit for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_form.php');

class weekend_form extends local_mxschool_form {

    /**
     * Form definition.
     */
    protected function definition() {
        $id = $this->_customdata['id'];
        $students = $this->_customdata['students'];

        strftime('%Y', get_config('local_mxschool', 'dorms_open_date'));
        $datetimeoptions = array(
            'startyear' => strftime('%Y', get_config('local_mxschool', 'dorms_open_date')),
            'stopyear' => strftime('%Y', get_config('local_mxschool', 'dorms_close_date')),
            'timezone'  => core_date::get_server_timezone_object(), 'step' => 15
        );

        $hidden = array('id', 'isstudent');
        $fields = array('form' => array(
            'student' => array('element' => 'select', 'options' => $students, 'rules' => array('required')),
            'departuretime' => array(
                'element' => 'date_time_selector', 'options' => $datetimeoptions, 'rules' => array('required')
            ),
            'returntime' => array(
                'element' => 'date_time_selector', 'options' => $datetimeoptions, 'rules' => array('required')
            ),
            'destination' => parent::ELEMENT_TEXT_AREA_REQUIRED,
            'transportation' => parent::ELEMENT_TEXT_AREA_REQUIRED,
            'phone' => parent::ELEMENT_TEXT_REQUIRED
        ));
        parent::set_fields($hidden, $fields, 'weekend_form');
        $this->_form->hideIf('student', 'isstudent', 'eq');
    }

    /**
     * Validates the weekend form before it can be submitted.
     * The checks performed are to ensure that the departure time is before the return time and the occur within the same weekend.
     *
     * @return array of errors as "element_name"=>"error_description" or an empty array if there are no errors.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        return $errors;
    }
}
