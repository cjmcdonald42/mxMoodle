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
 * External functions for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
require_once('locallib.php');
require_once('classes/mx_notifications.php');

class local_mxschool_external extends external_api {

    /**
     * Returns descriptions of the get_dorm_students() function's parameters.
     *
     * @return external_function_parameters Object holding array of parameters for the get_dorm_students() function.
     */
    public static function get_dorm_students_parameters() {
        return new external_function_parameters(array('dorm' => new external_value(PARAM_INT, 'The id of the dorm to query for.')));
    }

    /**
     * Queries the database to find all students in a specified dorm.
     *
     * @param int $dorm The id of the dorm to query for.
     * @return array The students in that dorm as {userid, name}.
     */
    public static function get_dorm_students($dorm) {
        external_api::validate_context(context_system::instance());
        $params = self::validate_parameters(self::get_dorm_students_parameters(), array('dorm' => $dorm));

        $list = $params['dorm'] ? get_dorm_student_list($params['dorm']) : get_boarding_student_list();
        $result = array();
        foreach ($list as $userid => $name) {
            $result[] = array('userid' => $userid, 'name' => $name);
        }
        return $result;
    }

    /**
     * Returns a description of the get_dorm_students() function's return values.
     *
     * @return external_multiple_structure Object describing the return values of the get_dorm_students() function.
     */
    public static function get_dorm_students_returns() {
        return new external_multiple_structure(
            new external_single_structure(array(
                'userid' => new external_value(PARAM_INT, 'user id of the student'),
                'name' => new external_value(PARAM_TEXT, 'name of the student')
            ))
        );
    }

    /**
     * Returns descriptions of the set_boolean_field() function's parameters.
     *
     * @return external_function_parameters Object holding array of parameters for the set_boolean_field() function.
     */
    public static function set_boolean_field_parameters() {
        return new external_function_parameters(array(
            'table' => new external_value(PARAM_TEXT, 'The table to update.'),
            'field' => new external_value(PARAM_TEXT, 'The field to update.'),
            'id' => new external_value(PARAM_INT, 'The id of the record to update.'),
            'value' => new external_value(PARAM_BOOL, 'The value to set.')
        ));
    }

    /**
     * Sets a boolean field in the database.
     *
     * @param string $table The table to update.
     * @param string $field The field to update.
     * @param int $id The id of the record to update.
     * @param bool $value The value to set.
     * @return bool True if the operation is succesful, false otherwise.
     */
    public static function set_boolean_field($table, $field, $id, $value) {
        external_api::validate_context(context_system::instance());
        $params = self::validate_parameters(self::set_boolean_field_parameters(), array(
            'table' => $table, 'field' => $field, 'id' => $id, 'value' => $value)
        );
        switch ($params['table']) {
            case 'local_mxschool_weekend_form':
                require_capability('local/mxschool:manage_weekend', context_system::instance());
                break;
            case 'local_mxschool_faculty':
                require_capability('local/mxschool:manage_faculty', context_system::instance());
                break;
            case 'local_mxschool_vt_site':
                require_capability('local/mxschool:manage_vacation_travel_preferences', context_system::instance());
                break;
            default:
                throw new moodle_exception('Invalid table.');
        }

        global $DB;
        $record = $DB->get_record($params['table'], array('id' => $params['id']));
        if (!$record || !isset($record->{$params['field']})) {
            return false;
        }
        $record->{$params['field']} = $params['value'];
        return $DB->update_record($params['table'], $record);
    }

    /**
     * Returns a description of the set_boolean_field() function's return value.
     *
     * @return external_value Object describing the return value of the set_boolean_field() function.
     */
    public static function set_boolean_field_returns() {
        return new external_value(PARAM_BOOL, 'True if the operation is succesful, false otherwise.');
    }

    /**
     * Returns descriptions of the send_email() function's parameters.
     *
     * @return external_function_parameters Object holding array of parameters for the send_email() function.
     */
    public static function send_email_parameters() {
        return new external_function_parameters(array(
            'emailclass' => new external_value(PARAM_TEXT, 'The class of the email to send.'),
            'emailparams' => new external_single_structure(array(
                'id' => new external_value(PARAM_INT, 'The id of a record to read from.')
            ))
        ));
    }

    /**
     * Sends an email to users based on predefined a email class.
     *
     * @param string $emailclass The class of the email to send.
     * @param array $emailparams Parameters for the email.
     * @return bool True if the email is successfully sent, false otherwise.
     */
    public static function send_email($emailclass, $emailparams) {
        external_api::validate_context(context_system::instance());
        $params = self::validate_parameters(self::send_email_parameters(), array(
            'emailclass' => $emailclass, 'emailparams' => $emailparams
        ));
        switch ($params['emailclass']) {
            case 'weekend_form_approved':
                require_capability('local/mxschool:manage_weekend', context_system::instance());
                break;
            case 'advisor_selection_notify_unsubmitted':
            case 'advisor_selection_notify_results':
                require_capability('local/mxschool:manage_advisor_selection', context_system::instance());
                break;
            case 'rooming_notify_unsubmitted':
                require_capability('local/mxschool:manage_rooming', context_system::instance());
                break;
            case 'vacation_travel_notify_unsubmitted':
                require_capability('local/mxschool:notify_vacation_travel', context_system::instance());
                break;
            default:
                throw new moodle_exception('Invalid email class.');
        }

        return mx_notifications::send_email($params['emailclass'], $params['emailparams']);
    }

    /**
     * Returns a description of the send_email() function's return value.
     *
     * @return external_value Object describing the return value of the send_email() function.
     */
    public static function send_email_returns() {
        return new external_value(PARAM_BOOL, 'True if the email is successfully sent, false otherwise.');
    }

    /**
     * Returns descriptions of the get_esignout_student_options() function's parameters.
     *
     * @return external_function_parameters Object holding array of parameters for the get_esignout_student_options() function.
     */
    public static function get_esignout_student_options_parameters() {
        return new external_function_parameters(array('userid' => new external_value(PARAM_INT, 'The user id of the student.')));
    }

    /**
     * Queries the database to determine the type options, passenger list, driver list,
     * and permissions for a selected student.
     *
     * @param int $userid The user id of the student.
     * @return stdClass With properties types, passengers, drivers, maydrivepassengers, mayridewith, specificdrivers.
     */
    public static function get_esignout_student_options($userid) {
        external_api::validate_context(context_system::instance());
        $params = self::validate_parameters(self::get_esignout_student_options_parameters(), array('userid' => $userid));

        global $DB;
        $result = new stdClass();
        $result->types = get_esignout_type_list($params['userid']);
        $list = get_passenger_list();
        $result->passengers = array();
        foreach ($list as $userid => $name) {
            if ($userid !== $params['userid']) {
                $result->passengers[] = array('userid' => $userid, 'name' => $name);
            }
        }
        $list = get_current_driver_list($params['userid']);
        $result->drivers = array();
        foreach ($list as $esignoutid => $name) {
            $result->drivers[] = array('esignoutid' => $esignoutid, 'name' => $name);
        }
        $result->maydrivepassengers = $DB->get_field(
            'local_mxschool_permissions', 'may_drive_passengers', array('userid' => $params['userid'])
        ) === 'Yes';
        $result->mayridewith = $DB->get_field('local_mxschool_permissions', 'may_ride_with', array('userid' => $params['userid']));
        $result->specificdrivers = $DB->get_field(
            'local_mxschool_permissions', 'ride_permission_details', array('userid' => $params['userid'])
        ) ?: '';
        return $result;
    }

    /**
     * Returns a description of the get_esignout_student_options() function's return values.
     *
     * @return external_single_structure Object describing the return values of the get_esignout_student_options() function.
     */
    public static function get_esignout_student_options_returns() {
        return new external_single_structure(array(
            'types' => new external_multiple_structure(
                new external_value(PARAM_TEXT, 'the identifier of the type')
            ), 'passengers' => new external_multiple_structure(
                new external_single_structure(array(
                    'userid' => new external_value(PARAM_INT, 'user id of the student'),
                    'name' => new external_value(PARAM_TEXT, 'name of the student')
                ))
            ), 'drivers' => new external_multiple_structure(
                new external_single_structure(array(
                    'esignoutid' => new external_value(PARAM_INT, 'id of the driver\'s esignout record'),
                    'name' => new external_value(PARAM_TEXT, 'name of the driver')
                ))
            ), 'maydrivepassengers' => new external_value(PARAM_BOOL, 'whether the student has permission to drive passengers'),
            'mayridewith' => new external_value(PARAM_TEXT, 'with whom the student has permission to be a passenger'),
            'specificdrivers' => new external_value(PARAM_TEXT, 'the comment for the student\'s riding permission')
        ));
    }

    /**
     * Returns descriptions of the get_esignout_driver_details() function's parameters.
     *
     * @return external_function_parameters Object holding array of parameters for the get_esignout_driver_details() function.
     */
    public static function get_esignout_driver_details_parameters() {
        return new external_function_parameters(array('esignoutid' => new external_value(PARAM_INT, 'The id of driver record.')));
    }

    /**
     * Queries the database to find the destination and departure time of an esignout driver record.
     *
     * @param int $esignoutid The id of driver record.
     * @return stdClass With properties destination, departurehour, departureminutes, and departureampm.
     * @throws coding_exception If the esignout record is not a driver record.
     */
    public static function get_esignout_driver_details($esignoutid) {
        external_api::validate_context(context_system::instance());
        $params = self::validate_parameters(self::get_esignout_driver_details_parameters(), array('esignoutid' => $esignoutid));

        return get_driver_inheritable_fields($params['esignoutid']);
    }

    /**
     * Returns a description of the get_esignout_driver_details() function's return values.
     *
     * @return external_single_structure Object describing the return values of the get_esignout_driver_details() function.
     */
    public static function get_esignout_driver_details_returns() {
        return new external_single_structure(array(
            'destination' => new external_value(PARAM_TEXT, 'the driver\'s destination'),
            'departurehour' => new external_value(PARAM_TEXT, 'the hour of the driver\'s departure time'),
            'departureminute' => new external_value(PARAM_TEXT, 'the minute of the driver\'s departure time'),
            'departureampm' => new external_value(PARAM_BOOL, 'whether the driver\'s departure time is am (0) or pm (1)')
        ));
    }

    /**
     * Returns descriptions of the sign_in() function's parameters.
     *
     * @return external_function_parameters Object holding array of parameters for the sign_in() function.
     */
    public static function sign_in_parameters() {
        return new external_function_parameters(array(
            'esignoutid' => new external_value(PARAM_INT, 'The id of ther record to sign in.'),
        ));
    }

    /**
     * Signs in an eSignout record and records the timestamp.
     *
     * @param int $esignoutid The id of the record to sign in.
     * @return string The text to display for the sign in time.
     * @throws coding_exception If the esignout record does not exist or is already signed in.
     */
    public static function sign_in($esignoutid) {
        external_api::validate_context(context_system::instance());
        $params = self::validate_parameters(self::sign_in_parameters(), array('esignoutid' => $esignoutid));

        return sign_in_esignout($params['esignoutid']);
    }

    /**
     * Returns a description of the sign_in() function's return value.
     *
     * @return external_value Object describing the return value of the sign_in() function.
     */
    public static function sign_in_returns() {
        return new external_value(PARAM_TEXT, 'The text to display for the sign in time.');
    }

    /**
     * Returns descriptions of the get_advisor_selection_student_options() function's parameters.
     *
     * @return external_function_parameters Object holding array of parameters for the get_advisor_selection_student_options() function.
     */
    public static function get_advisor_selection_student_options_parameters() {
        return new external_function_parameters(array('userid' => new external_value(PARAM_INT, 'The user id of the student.')));
    }

    /**
     * Queries the database to determine the current advisor, advisory status, and list of possible advisors
     * for a particular student as well as a list of students who have not completed the form.
     *
     * @param int $userid The user id of the student.
     * @return stdClass With properties students, current, closing, and available.
     */
    public static function get_advisor_selection_student_options($userid) {
        external_api::validate_context(context_system::instance());
        $params = self::validate_parameters(self::get_advisor_selection_student_options_parameters(), array('userid' => $userid));

        global $DB;
        $result = new stdClass();
        $list = get_student_without_advisor_form_list();
        $result->students = array();
        foreach ($list as $userid => $name) {
            $result->students[] = array('userid' => $userid, 'name' => $name);
        }
        $result->current = $DB->get_record_sql(
            "SELECT u.id AS userid, CONCAT(u.lastname, ', ', u.firstname) AS name
             FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.advisorid = u.id
             WHERE s.userid = ?", array($params['userid'])
        );
        $result->closing = $DB->get_field_sql(
            "SELECT f.advisory_closing
             FROM {local_mxschool_student} s LEFT JOIN {local_mxschool_faculty} f ON s.advisorid = f.userid
             WHERE s.userid = ?", array($params['userid'])
        );
        $list = get_available_advisor_list();
        $result->available = array();
        foreach ($list as $userid => $name) {
            $result->available[] = array('userid' => $userid, 'name' => $name);
        }
        return $result;
    }

    /**
     * Returns a description of the get_advisor_selection_student_options() function's return values.
     *
     * @return external_single_structure Object describing the return values of the get_advisor_selection_student_options() function.
     */
    public static function get_advisor_selection_student_options_returns() {
        return new external_single_structure(array(
            'students' => new external_multiple_structure(new external_single_structure(array(
                'userid' => new external_value(
                    PARAM_INT, 'the user id of the student who has not completed an advisor selection form'
                ), 'name' => new external_value(
                    PARAM_TEXT, 'the name of the student who has not completed an advisor selection form'
                )
            ))), 'current' => new external_single_structure(array(
                'userid' => new external_value(PARAM_INT, 'the user id of the student\' current advisor'),
                'name' => new external_value(PARAM_TEXT, 'the name of the student\' current advisor')
            )), 'closing' => new external_value(PARAM_BOOL, 'whether the student\'s advisory is closing'),
            'available' => new external_multiple_structure(new external_single_structure(array(
                'userid' => new external_value(PARAM_INT, 'the user id of the available faculty'),
                'name' => new external_value(PARAM_TEXT, 'the name of the available faculty')
            )))
        ));
    }

    /**
     * Returns descriptions of the select_advisor() function's parameters.
     *
     * @return external_function_parameters Object holding array of parameters for the select_advisor() function.
     */
    public static function select_advisor_parameters() {
        return new external_function_parameters(array(
            'student' => new external_value(PARAM_INT, 'The user id of the associated student.'),
            'choice' => new external_value(PARAM_INT, 'The user id of the chosen advisor.')
        ));
    }

    /**
     * Selects the advisor for a student.
     *
     * @param int $student The user id of the associated student.
     * @param int $choice The user id of the chosen advisor.
     * @return bool True if the operation is succesful, false otherwise.
     */
    public static function select_advisor($student, $choice) {
        external_api::validate_context(context_system::instance());
        require_capability('local/mxschool:manage_advisor_selection', context_system::instance());
        $params = self::validate_parameters(self::select_advisor_parameters(), array(
            'student' => $student, 'choice' => $choice
        ));

        global $DB;
        $record = $DB->get_record('local_mxschool_adv_selection', array('userid' => $params['student']));
        if (!$record) {
            return false;
        }
        $record->selectedid = $params['choice'];
        return $DB->update_record('local_mxschool_adv_selection', $record);
    }

    /**
     * Returns a description of the select_advisor() function's return value.
     *
     * @return external_value Object describing the return value of the select_advisor() function.
     */
    public static function select_advisor_returns() {
        return new external_value(PARAM_BOOL, 'True if the operation is succesful, false otherwise.');
    }

    /**
     * Returns descriptions of the get_rooming_student_options() function's parameters.
     *
     * @return external_function_parameters Object holding array of parameters for the get_rooming_student_options() function.
     */
    public static function get_rooming_student_options_parameters() {
        return new external_function_parameters(array('userid' => new external_value(PARAM_INT, 'The user id of the student.')));
    }

    /**
     * Queries the database to determine the current advisor, advisory status, and list of possible advisors
     * for a particular student as well as a list of students who have not completed the form.
     *
     * @param int $userid The user id of the student.
     * @return stdClass With properties students, dorm, gradedormmates, and dormmates.
     */
    public static function get_rooming_student_options($userid) {
        external_api::validate_context(context_system::instance());
        $params = self::validate_parameters(self::get_rooming_student_options_parameters(), array('userid' => $userid));

        global $DB;
        $result = new stdClass();
        $list = get_student_without_rooming_form_list();
        $result->students = array();
        foreach ($list as $userid => $name) {
            $result->students[] = array('userid' => $userid, 'name' => $name);
        }
        $result->dorm = $DB->get_field_sql(
            "SELECT d.name FROM {local_mxschool_student} s LEFT JOIN {local_mxschool_dorm} d ON s.dormid = d.id WHERE s.userid = ?",
            array($params['userid'])
        );
        $list = get_student_possible_same_grade_dormmate_list($params['userid']);
        $result->gradedormmates = array();
        foreach ($list as $userid => $name) {
            $result->gradedormmates[] = array('userid' => $userid, 'name' => $name);
        }
        $list = get_student_possible_dormmate_list($params['userid']);
        $result->dormmates = array();
        foreach ($list as $userid => $name) {
            $result->dormmates[] = array('userid' => $userid, 'name' => $name);
        }
        return $result;
    }

    /**
     * Returns a description of the get_rooming_student_options() function's return values.
     *
     * @return external_single_structure Object describing the return values of the get_rooming_student_options() function.
     */
    public static function get_rooming_student_options_returns() {
        return new external_single_structure(array(
            'students' => new external_multiple_structure(new external_single_structure(array(
                'userid' => new external_value(PARAM_INT, 'the user id of the student who has not completed a rooming form'),
                'name' => new external_value(PARAM_TEXT, 'the name of the student who has not completed a rooming form')
            ))), 'dorm' => new external_value(PARAM_TEXT, 'the name of the student\'s current dorm'),
            'gradedormmates' => new external_multiple_structure(new external_single_structure(array(
                'userid' => new external_value(PARAM_INT, 'the user id of the potential dormmate in the same grade'),
                'name' => new external_value(PARAM_TEXT, 'the name of the potential dormmate in the same grade')
            ))),
            'dormmates' => new external_multiple_structure(new external_single_structure(array(
                'userid' => new external_value(PARAM_INT, 'the user id of the potential dormmate'),
                'name' => new external_value(PARAM_TEXT, 'the name of the potential dormmate')
            )))
        ));
    }

    /**
     * Returns descriptions of the get_vacation_travel_options() function's parameters.
     *
     * @return external_function_parameters Object holding array of parameters for the get_vacation_travel_options() function.
     */
    public static function get_vacation_travel_options_parameters() {
        return new external_function_parameters(array('departure' => new external_single_structure(array(
            'mxtransportation' => new external_value(
                PARAM_BOOL, 'Whether the student has selected that they require school transportation.', VALUE_OPTIONAL
            ), 'type' => new external_value(PARAM_TEXT, 'The type of transportation specified.', VALUE_OPTIONAL)
        )), 'return' => new external_single_structure(array(
            'mxtransportation' => new external_value(
                PARAM_BOOL, 'Whether the student has selected that they require school transportation.', VALUE_OPTIONAL
            ), 'type' => new external_value(PARAM_TEXT, 'The type of transportation specified.', VALUE_OPTIONAL)
        ))));
    }

    /**
     * Queries the database to determine the available types and sites for a particular selection
     * as well as a list of students who have not completed the form.
     *
     * @param stdClass $departure Object which may have properties mxtransportation and type.
     * @param stdClass $return Object which may have properties mxtransportation and type.
     * @return stdClass With properties students, departure, and return.
     */
    public static function get_vacation_travel_options($departure, $return) {
        external_api::validate_context(context_system::instance());
        $params = self::validate_parameters(self::get_vacation_travel_options_parameters(), array(
            'departure' => $departure, 'return' => $return
        ));

        global $DB;
        $result = new stdClass();
        $list = get_student_without_vacation_travel_form_list();
        $result->students = array();
        foreach ($list as $userid => $name) {
            $result->students[] = array('userid' => $userid, 'name' => $name);
        }
        $result->departure = new stdClass();
        $result->departure->types = get_vacation_travel_type_list(
            isset($params['departure']['mxtransportation']) ? $params['departure']['mxtransportation'] : null
        );
        $list = get_vacation_travel_departure_sites_list(isset($params['departure']['type']) ? $params['departure']['type'] : null);
        $result->departure->sites = array();
        foreach ($list as $id => $name) {
            $result->departure->sites[] = (string)$id;
        }
        $result->return = new stdClass();
        $result->return->types = get_vacation_travel_type_list(
            isset($params['return']['mxtransportation']) ? $params['return']['mxtransportation'] : null
        );
        $list = get_vacation_travel_return_sites_list(isset($params['return']['type']) ? $params['return']['type'] : null);
        $result->return->sites = array();
        foreach ($list as $id => $name) {
            $result->return->sites[] = (string)$id;
        }
        return $result;
    }

    /**
     * Returns a description of the get_vacation_travel_options() function's return values.
     *
     * @return external_single_structure Object describing the return values of the get_vacation_travel_options() function.
     */
    public static function get_vacation_travel_options_returns() {
        return new external_single_structure(array(
            'students' => new external_multiple_structure(new external_single_structure(array(
                'userid' => new external_value(
                    PARAM_INT, 'the user id of the student who has not completed a vacation travel form'
                ), 'name' => new external_value(PARAM_TEXT, 'the name of the student who has not completed a vacation travel form')
            ))), 'departure' => new external_single_structure(array(
                'types' => new external_multiple_structure(
                    new external_value(PARAM_TEXT, 'the type which is available given the filter')
                ), 'sites' => new external_multiple_structure(
                    new external_value(PARAM_TEXT, 'the id of the site which is available given the filter')
                )
            )), 'return' => new external_single_structure(array(
                'types' => new external_multiple_structure(
                    new external_value(PARAM_TEXT, 'the type which is available given the filter')
                ), 'sites' => new external_multiple_structure(
                    new external_value(PARAM_TEXT, 'the id of the site which is available given the filter')
                )
            ))
        ));
    }

}
