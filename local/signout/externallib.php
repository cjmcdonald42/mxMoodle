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
 * External functions for Middlesex School's eSignout Subplugin.
 *
 * @package    local_signout
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
require_once(__DIR__.'/locallib.php');

class local_signout_external extends external_api {

    /**
     * Returns descriptions of the get_off_campus_student_options() function's parameters.
     *
     * @return external_function_parameters Object holding array of parameters for the get_off_campus_student_options() function.
     */
    public static function get_off_campus_student_options_parameters() {
        return new external_function_parameters(array('userid' => new external_value(PARAM_INT, 'The user id of the student.')));
    }

    /**
     * Queries the database to determine the type options, passenger list, driver list,
     * and permissions for a selected student.
     *
     * @param int $userid The user id of the student.
     * @return stdClass With properties types, passengers, drivers, maydrivepassengers, mayridewith, specificdrivers.
     */
    public static function get_off_campus_student_options($userid) {
        external_api::validate_context(context_system::instance());
        $params = self::validate_parameters(self::get_off_campus_student_options_parameters(), array('userid' => $userid));

        global $DB;
        $result = new stdClass();
        $result->types = get_off_campus_type_list($params['userid']);
        $result->passengers = convert_associative_to_object(get_permitted_passenger_list());
        $result->passengers = array_filter($result->passengers, function($passenger) use($params) {
            return $passenger['value'] !== $params['userid'];
        });
        $result->drivers = convert_associative_to_object(get_current_driver_list($params['userid']));
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
     * Returns a description of the get_off_campus_student_options() function's return values.
     *
     * @return external_single_structure Object describing the return values of the get_off_campus_student_options() function.
     */
    public static function get_off_campus_student_options_returns() {
        return new external_single_structure(array(
            'types' => new external_multiple_structure(
                new external_value(PARAM_TEXT, 'the identifier of the type')
            ),
            'passengers' => new external_multiple_structure(
                new external_single_structure(array(
                    'value' => new external_value(PARAM_INT, 'user id of the student'),
                    'text' => new external_value(PARAM_TEXT, 'name of the student')
                ))
            ),
            'drivers' => new external_multiple_structure(
                new external_single_structure(array(
                    'value' => new external_value(PARAM_INT, 'id of the driver\'s off-campus signout record'),
                    'text' => new external_value(PARAM_TEXT, 'name of the driver')
                ))
            ),
            'maydrivepassengers' => new external_value(PARAM_BOOL, 'whether the student has permission to drive passengers'),
            'mayridewith' => new external_value(PARAM_TEXT, 'with whom the student has permission to be a passenger'),
            'specificdrivers' => new external_value(PARAM_TEXT, 'the comment for the student\'s riding permission')
        ));
    }

    /**
     * Returns descriptions of the get_off_campus_driver_details() function's parameters.
     *
     * @return external_function_parameters Object holding array of parameters for the get_off_campus_driver_details() function.
     */
    public static function get_off_campus_driver_details_parameters() {
        return new external_function_parameters(array('offcampusid' => new external_value(PARAM_INT, 'The id of driver record.')));
    }

    /**
     * Queries the database to find the destination and departure time of an off-campus signout driver record.
     *
     * @param int $offcampusid The id of driver record.
     * @return stdClass With properties destination, departurehour, departureminutes, and departureampm.
     * @throws coding_exception If the off-campus signout record is not a driver record.
     */
    public static function get_off_campus_driver_details($offcampusid) {
        external_api::validate_context(context_system::instance());
        $params = self::validate_parameters(self::get_off_campus_driver_details_parameters(), array('offcampusid' => $offcampusid));

        return get_driver_inheritable_fields($params['offcampusid']);
    }

    /**
     * Returns a description of the get_off_campus_driver_details() function's return values.
     *
     * @return external_single_structure Object describing the return values of the get_off_campus_driver_details() function.
     */
    public static function get_off_campus_driver_details_returns() {
        return new external_single_structure(array(
            'destination' => new external_value(PARAM_TEXT, 'the driver\'s destination'),
            'departurehour' => new external_value(PARAM_TEXT, 'the hour of the driver\'s departure time'),
            'departureminute' => new external_value(PARAM_TEXT, 'the minute of the driver\'s departure time'),
            'departureampm' => new external_value(PARAM_BOOL, 'whether the driver\'s departure time is am (0) or pm (1)')
        ));
    }

    /**
     * Returns descriptions of the get_on_campus_student_options() function's parameters.
     *
     * @return external_function_parameters Object holding array of parameters for the get_on_campus_student_options() function.
     */
    public static function get_on_campus_student_options_parameters() {
        return new external_function_parameters(array('userid' => new external_value(PARAM_INT, 'The user id of the student.')));
    }

    /**
     * Queries the database to determine the location options and permissions for a selected student.
     *
     * @param int $userid The user id of the student.
     * @return stdClass With properties types, passengers, drivers, maydrivepassengers, mayridewith, specificdrivers.
     */
    public static function get_on_campus_student_options($userid) {
        external_api::validate_context(context_system::instance());
        $params = self::validate_parameters(self::get_on_campus_student_options_parameters(), array('userid' => $userid));

        global $DB;
        $result = new stdClass();
        $result->grade = $DB->get_field('local_mxschool_student', 'grade', array('userid' => $params['userid']));
        $result->locations = convert_associative_to_object(get_on_campus_location_list($result->grade));
        return $result;
    }

    /**
     * Returns a description of the get_on_campus_student_options() function's return values.
     *
     * @return external_single_structure Object describing the return values of the get_on_campus_student_options() function.
     */
    public static function get_on_campus_student_options_returns() {
        return new external_single_structure(array(
            'locations' => new external_multiple_structure(
                new external_single_structure(array(
                    'value' => new external_value(PARAM_INT, 'id of the location'),
                    'text' => new external_value(PARAM_TEXT, 'name of the location')
                ))
            ),
            'grade' => new external_value(PARAM_INT, 'the student\'s grade')
        ));
    }

    /**
     * Returns descriptions of the sign_in() function's parameters.
     *
     * @return external_function_parameters Object holding array of parameters for the sign_in() function.
     */
    public static function sign_in_parameters() {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'The id of the record to sign in.'),
            'table' => new external_value(PARAM_TEXT, 'The table of the record to sign in.')
        ));
    }

    /**
     * Signs in an eSignout record and records the timestamp.
     *
     * @param int $id The id of the record to sign in.
     * @param string $table The table of the record to sign in.
     * @return string The text to display for the sign-in time.
     * @throws coding_exception If the table is invalid or
     *                          if the eSignout record does not exist, doesn't belong to this user, or has already been signed in.
     */
    public static function sign_in($id, $table) {
        external_api::validate_context(context_system::instance());
        $params = self::validate_parameters(self::sign_in_parameters(), array('id' => $id, 'table' => $table));

        global $DB, $USER;
        switch ($params['table']) {
            case 'local_signout_off_campus':
                $page = get_string('off_campus_report', 'local_signout');
                break;
            case 'local_signout_on_campus':
                $page = get_string('on_campus_report', 'local_signout');
                break;
            default:
                throw new coding_exception("Unsupported table: {$params['table']}.");
        }
        $record = $DB->get_record($params['table'], array('id' => $params['id']));
        if (!$record || $USER->id != $record->userid || $record->sign_in_time) {
            throw new coding_exception(
                'eSignout record either doesn\'t exist, doesn\' belong to this user, or has already been signed in'
            );
        }
        $record->sign_in_time = $record->time_modified = time();
        $DB->update_record($params['table'], $record);
        \local_mxschool\event\record_updated::create(array('other' => array('page' => $page)))->trigger();
        return format_date('g:i A', $record->sign_in_time);
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
     * Returns descriptions of the confirm_signout() function's parameters.
     *
     * @return external_function_parameters Object holding array of parameters for the confirm_signout() function.
     */
    public static function confirm_signout_parameters() {
        return new external_function_parameters(array('id' => new external_value(PARAM_INT, 'The id of the record to confirm.')));
    }

    /**
     * Confirms an on-campus signout record and records the timestamp.
     *
     * @param int $id The id of the record to confirm.
     * @return string The text to display for the sign in time.
     * @throws coding_exception If the on-campus signout record does not exist or has already been confirmed.
     */
    public static function confirm_signout($id) {
        external_api::validate_context(context_system::instance());
        $params = self::validate_parameters(self::confirm_signout_parameters(), array('id' => $id));

        global $DB, $USER;
        require_capability('local/signout:confirm_on_campus', context_system::instance());
        $record = $DB->get_record('local_signout_on_campus', array('id' => $params['id']));
        if (!$record || $record->confirmation_time) {
            throw new coding_exception('on-campus signout record either doesn\'t exist or has already been confirmed');
        }
        $record->confirmation_time = $record->time_modified = time();
        $record->confirmerid = $USER->id;
        $DB->update_record('local_signout_on_campus', $record);
        \local_mxschool\event\record_updated::create(array('other' => array(
            'page' => get_string('duty_report', 'local_signout')
        )))->trigger();
        $result = new stdClass();
        $result->confirmationtime = format_date('g:i A', $record->confirmation_time);
        $result->confirmer = $DB->get_field_sql(
            "SELECT CONCAT(lastname, ', ', firstname) FROM {user} WHERE id = ?", array($record->confirmerid)
        );
        return $result;
    }

    /**
     * Returns a description of the confirm_signout() function's return value.
     *
     * @return external_single_structure Object describing the return value of the confirm_signout() function.
     */
    public static function confirm_signout_returns() {
        return new external_single_structure(array(
            'confirmationtime' => new external_value(PARAM_TEXT, 'The time when the record was confirmed.'),
            'confirmer' => new external_value(PARAM_TEXT, 'The name of the user who confirmed the record.'),
        ));
    }

}
