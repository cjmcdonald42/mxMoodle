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
 * Form for students to submit weekend travel plans for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  checkin
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\checkin;

defined('MOODLE_INTERNAL') || die();

class weekend_form extends \local_mxschool\form {

    /**
     * Form definition.
     */
    protected function definition() {
        $dorms = $this->_customdata['dorms'];
        $students = $this->_customdata['students'];

        $fields = array(
            '' => array(
                'id' => self::ELEMENT_HIDDEN_INT,
                'timecreated' => self::ELEMENT_HIDDEN_INT,
                'isstudent' => self::ELEMENT_HIDDEN_INT,
                'dorm' => array('element' => 'select', 'options' => $dorms),
                'student' => array('element' => 'select', 'options' => $students),
                'departure' => array('element' => 'group', 'children' => array(
                    'time' => self::time_selector(15),
                    'date' => array('element' => 'date_selector', 'options' => self::date_options_school_year())
                )),
                'warning' => array('element' => 'static', 'name' => null),
                'return' => array('element' => 'group', 'children' => array(
                    'time' => self::time_selector(15),
                    'date' => array('element' => 'date_selector', 'options' => self::date_options_school_year())
                )),
                'destination' => array('element' => 'text', 'type' => PARAM_TEXT, 'attributes' => array('size' => 40)),
                'transportation' => array('element' => 'text', 'type' => PARAM_TEXT, 'attributes' => array('size' => 40)),
                'phone' => self::ELEMENT_TEXT
            )
        );
        $this->set_fields($fields, 'checkin:weekend_form', false);

        $mform = $this->_form;
        $mform->hideIf('dorm', 'isstudent', 'eq');
        $mform->disabledIf('dorm', 'id', 'neq', '0');
        $mform->hideIf('student', 'isstudent', 'eq');
        $mform->disabledIf('student', 'id', 'neq', '0');
    }

    /**
     * Validates the weekend form before it can be submitted.
     * The checks performed are to ensure that the departure time is before the return time and the occur within the same weekend.
     *
     * @return array of errors as "element_name"=>"error_description" or an empty array if there are no errors.
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
        $departure = generate_timestamp($data, 'departure');
        $return = generate_timestamp($data, 'return');
        if ($departure >= $return) {
            $errors['return'] = get_string('checkin:weekend_form:error:out_of_order', 'local_mxschool');
        }
        $departurestartbound = generate_datetime($departure);
        $departureendbound = clone $departurestartbound;
        $departurestartbound->modify('+4 days'); // Map 0:00:00 Wednesday to 0:00:00 Sunday.
        $departureendbound->modify('-3 days'); // Map 0:00:00 Tuesday to 0:00:00 Sunday.
        $weekend = $DB->get_field_select(
            'local_mxschool_weekend', 'sunday_time', '? >= sunday_time AND ? < sunday_time',
            array($departurestartbound->getTimestamp(), $departureendbound->getTimestamp())
        );
        if ($weekend) {
            $returnstartbound = generate_datetime($return);
            $returnstartbound->modify('+4 days'); // Map 0:00:00 Wednesday to 0:00:00 Sunday.
            $returnendbound = generate_datetime($return);
            $returnendbound->modify('-3 days'); // Map 0:00:00 Tuesday to 0:00:00 Sunday.
            if (
                $returnstartbound->getTimestamp() < (int) $weekend
                || $returnendbound->getTimestamp() >= (int) $weekend
            ) {
                $errors['return'] = get_string('checkin:weekend_form:error:in_different_weekends', 'local_mxschool');
            }
        } else {
            $errors['departure'] = get_string('checkin:weekend_form:error:not_in_weekend', 'local_mxschool');
        }
        if (empty($data['destination'])) {
            $errors['destination'] = get_string('checkin:weekend_form:error:no_destination', 'local_mxschool');
        }
        if (empty($data['transportation'])) {
            $errors['transportation'] = get_string('checkin:weekend_form:error:no_transportation', 'local_mxschool');
        }
        if (empty($data['phone'])) {
            $errors['phone'] = get_string('checkin:weekend_form:error:no_phone', 'local_mxschool');
        }
        return $errors;
    }
}
