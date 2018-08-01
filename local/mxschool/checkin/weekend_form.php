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
 * @subpackage checkin
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
        $dorms = $this->_customdata['dorms'];
        $students = $this->_customdata['students'];

        $dateparameters = array(
            'startyear' => strftime('%Y', get_config('local_mxschool', 'dorms_open_date')),
            'stopyear' => strftime('%Y', get_config('local_mxschool', 'dorms_close_date')),
            'timezone'  => core_date::get_server_timezone_object()
        );

        $fields = array('' => array(
            'id' => parent::ELEMENT_HIDDEN_INT,
            'timecreated' => parent::ELEMENT_HIDDEN_INT,
            'isstudent' => parent::ELEMENT_HIDDEN_INT,
            'dorm' => array('element' => 'select', 'options' => $dorms),
            'student' => array('element' => 'select', 'options' => $students),
            'departure' => array('element' => 'group', 'children' => array(
                'time' => parent::time_selector(15),
                'date' => array('element' => 'date_selector', 'parameters' => $dateparameters)
            )), 'warning' => array('element' => 'static', 'name' => null),
            'return' => array('element' => 'group', 'children' => array(
                'time' => parent::time_selector(15),
                'date' => array('element' => 'date_selector', 'parameters' => $dateparameters)
            )), 'destination' => array('element' => 'text', 'type' => PARAM_TEXT, 'attributes' => array('size' => 40)),
            'transportation' => array('element' => 'text', 'type' => PARAM_TEXT, 'attributes' => array('size' => 40)),
            'phone' => parent::ELEMENT_TEXT
        ));
        parent::set_fields($fields, 'weekend_form', false);

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
            $errors['return'] = get_string('weekend_form_error_outoforder', 'local_mxschool');
        }
        $departurestartbound = new DateTime('now', core_date::get_server_timezone_object());
        $departurestartbound->setTimestamp($departure);
        $departureendbound = clone $departurestartbound;
        $departurestartbound->modify('+4 days'); // Map 0:00:00 Wednesday to 0:00:00 Sunday.
        $departureendbound->modify('-3 days'); // Map 0:00:00 Tuesday to 0:00:00 Sunday.
        $weekend = $DB->get_record_sql(
            "SELECT * FROM {local_mxschool_weekend} WHERE ? >= sunday_time AND ? < sunday_time",
            array($departurestartbound->getTimestamp(), $departureendbound->getTimestamp())
        );
        if ($weekend) {
            $returnstartbound = new DateTime('now', core_date::get_server_timezone_object());
            $returnstartbound->setTimestamp($return);
            $returnendbound = clone $returnstartbound;
            $returnstartbound->modify('+4 days'); // Map 0:00:00 Wednesday to 0:00:00 Sunday.
            $returnendbound->modify('-3 days'); // Map 0:00:00 Tuesday to 0:00:00 Sunday.
            if (
                $returnstartbound->getTimestamp() < (int)$weekend->sunday_time
                || $returnendbound->getTimestamp() >= (int)$weekend->sunday_time
            ) {
                $errors['return'] = get_string('weekend_form_error_indifferentweekends', 'local_mxschool');
            }
        } else {
            $errors['departure'] = get_string('weekend_form_error_notinweekend', 'local_mxschool');
        }
        if ($data['destination'] === '') {
            $errors['destination'] = get_string('weekend_form_error_nodestination', 'local_mxschool');
        }
        if ($data['transportation'] === '') {
            $errors['transportation'] = get_string('weekend_form_error_notransportation', 'local_mxschool');
        }
        if ($data['phone'] === '') {
            $errors['phone'] = get_string('weekend_form_error_nophone', 'local_mxschool');
        }
        return $errors;
    }
}
