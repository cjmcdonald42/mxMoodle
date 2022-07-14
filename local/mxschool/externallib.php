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
 * External functions for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @author      mxMoodle Development Team
 * @copyright   2022 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
require_once(__DIR__.'/locallib.php');

class local_mxschool_external extends external_api {

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
     * @throws coding_exception If the table does not exist.
     */
    public static function set_boolean_field($table, $field, $id, $value) {
        external_api::validate_context(context_system::instance());
        $params = self::validate_parameters(self::set_boolean_field_parameters(), array(
            'table' => $table, 'field' => $field, 'id' => $id, 'value' => $value)
        );
        switch ($params['table']) { // HACK: These capabilities should really not be hardcoded because it is not expandable.
            case 'local_mxschool_weekend_form':
                require_capability('local/mxschool:manage_weekend', context_system::instance());
                $page = get_string('checkin:weekend_report', 'local_mxschool');
                break;
            case 'local_mxschool_faculty':
                require_capability('local/mxschool:manage_faculty', context_system::instance());
                $page = get_string('user_management:faculty_report', 'local_mxschool');
                break;
            case 'local_mxschool_vt_site':
                require_capability('local/mxschool:manage_vacation_travel_preferences', context_system::instance());
                $page = get_string('vacation_travel:site_report', 'local_mxschool');
                break;
            case 'local_mxschool_deans_perm':
                require_capability('local/mxschool:manage_deans_permission', context_system::instance());
                $page = get_string('deans_permission:report', 'local_mxschool');
            case 'local_mxschool_attendance':
                $page = get_string('checkin:attendance_report', 'local_mxschool');
                break;
		  case 'local_mxschool_healthtest':
                $page = get_string('healthtest:test_report', 'local_mxschool');
                break;
            default:
                throw new coding_exception("Unsupported table: {$params['table']}.");
        }

        global $DB;
        $record = $DB->get_record($params['table'], array('id' => $params['id']));
        if (!$record || !isset($record->{$params['field']})) {
            return false;
        }
        $record->{$params['field']} = $params['value'];
        if (isset($record->time_modified)) {
            $record->time_modified = time();
        }
        local_mxschool\event\record_updated::create(array('other' => array('page' => $page)))->trigger();
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
     * @throws coding_exception If the email class does not exist or the specified record does not exist.
     */
    public static function send_email($emailclass, $emailparams) {
        external_api::validate_context(context_system::instance());
        $params = self::validate_parameters(self::send_email_parameters(), array(
            'emailclass' => $emailclass, 'emailparams' => $emailparams
        ));
        switch ($params['emailclass']) { // HACK: These capabilities should really not be hardcoded because it is not expandable.
            case 'weekend_form_approved':
                require_capability('local/mxschool:manage_weekend', context_system::instance());
                return (new local_mxschool\local\checkin\weekend_form_approved($params['emailparams']['id']))->send();
            case 'advisor_selection_notify_unsubmitted':
                require_capability('local/mxschool:manage_advisor_selection', context_system::instance());
                return (new local_mxschool\local\advisor_selection\bulk_unsubmitted())->send();
            case 'advisor_selection_notify_results':
                require_capability('local/mxschool:manage_advisor_selection', context_system::instance());
                return (new local_mxschool\local\advisor_selection\bulk_results())->send();
            case 'rooming_notify_unsubmitted':
                require_capability('local/mxschool:manage_rooming', context_system::instance());
                return (new local_mxschool\local\rooming\bulk_unsubmitted())->send();
            case 'vacation_travel_notify_unsubmitted':
                require_capability('local/mxschool:notify_vacation_travel', context_system::instance());
                return (new local_mxschool\local\vacation_travel\bulk_unsubmitted())->send();
            case 'healthpass_notify_unsubmitted':
                require_capability('local/mxschool:manage_healthpass', context_system::instance());
                return (new local_mxschool\local\healthpass\bulk_unsubmitted())->send();
            case 'healthpass_overridden':
                require_capability('local/mxschool:manage_healthpass', context_system::instance());
                return (new local_mxschool\local\healthpass\healthpass_overridden($params['emailparams']['id']))->send();
            case 'healthtest_notify_reminder':
                require_capability('local/mxschool:manage_healthpass', context_system::instance());
                $testers = get_tomorrows_tester_list();
                foreach($testers as $tester) {
                    (new local_mxschool\local\healthtest\healthtest_reminder($tester))->send();
                }
                return 1;
            case 'healthtest_notify_missed':
                require_capability('local/mxschool:manage_healthpass', context_system::instance());
                $missed_testers = get_todays_missed_tester_list();
                foreach($missed_testers as $tester) {
                    (new local_mxschool\local\healthtest\healthtest_missed($tester))->send();
                }
                return 1;
            case 'deans_permission_notify_student':
                require_capability('local/mxschool:manage_deans_permission', context_system::instance());
                return (new local_mxschool\local\deans_permission\notify_student($params['emailparams']['id']))->send();
            case 'deans_permission_notify_healthcenter':
                require_capability('local/mxschool:manage_deans_permission', context_system::instance());
                return (new local_mxschool\local\deans_permission\notify_healthcenter($params['emailparams']['id']))->send();
            default:
                throw new coding_exception("Unsupported email class: {$params['emailclass']}.");
        }
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

        $list = $params['dorm'] ? get_student_in_dorm_list($params['dorm']) : get_boarding_student_list();
        return convert_associative_to_object($list);
    }

    /**
     * Returns a description of the get_dorm_students() function's return values.
     *
     * @return external_multiple_structure Object describing the return values of the get_dorm_students() function.
     */
    public static function get_dorm_students_returns() {
        return new external_multiple_structure(
            new external_single_structure(array(
                'value' => new external_value(PARAM_INT, 'user id of the student'),
                'text' => new external_value(PARAM_TEXT, 'name of the student')
            ))
        );
    }

    /**
     * Returns descriptions of the get_weekend_type() function's parameters.
     *
     * @return external_function_parameters Object holding array of parameters for the get_weekend_type() function.
     */
    public static function get_weekend_type_parameters() {
        return new external_function_parameters(array(
            'datetime' => new external_single_structure(array(
                'hour' => new external_value(PARAM_INT, 'The hour field.'),
                'minute' => new external_value(PARAM_INT, 'The minute field.'),
                'ampm' => new external_value(PARAM_BOOL, 'Whether the time is PM.'),
                'day' => new external_value(PARAM_INT, 'The day field.'),
                'month' => new external_value(PARAM_INT, 'The month field.'),
                'year' => new external_value(PARAM_INT, 'The year field.')
            ))
        ));
    }

    /**
     * Queries the database to determine the type of a weekend specified by a timestamp.
     *
     * @param int $datetime An array of time fields indicating the weekend to check.
     * @return string The type of the weekend.
     */
    public static function get_weekend_type($datetime) {
        external_api::validate_context(context_system::instance());
        $params = self::validate_parameters(self::get_weekend_type_parameters(), array('datetime' => $datetime));

        global $DB;
        $startbound = generate_datetime();
        $startbound->setDate($params['datetime']['year'], $params['datetime']['month'], $params['datetime']['day']);
        $startbound->setTime($params['datetime']['hour'] % 12 + $params['datetime']['ampm'] * 12, $params['datetime']['minute']);
        $endbound = clone $startbound;
        $startbound->modify('+4 days'); // Map 0:00:00 Wednesday to 0:00:00 Sunday.
        $endbound->modify('-3 days'); // Map 0:00:00 Tuesday to 0:00:00 Sunday.
        return $DB->get_field_select(
            'local_mxschool_weekend', 'type', '? >= sunday_time AND ? < sunday_time',
            array($startbound->getTimestamp(), $endbound->getTimestamp())
        ) ?: '';
    }

    /**
     * Returns a description of the get_weekend_type() function's return values.
     *
     * @return external_multiple_structure Object describing the return values of the get_weekend_type() function.
     */
    public static function get_weekend_type_returns() {
        return new external_value(PARAM_TEXT, "the type of the weekend, '' if not a valid weekend");
    }

    /**
     * Returns descriptions of the get_advisor_selection_student_options() function's parameters.
     *
     * @return external_function_parameters Object holding array of parameters
     *                                      for the get_advisor_selection_student_options() function.
     */
    public static function get_advisor_selection_student_options_parameters() {
        return new external_function_parameters(array('userid' => new external_value(PARAM_INT, 'The user id of the student.')));
    }

    /**
     * Queries the database to determine the current advisor, advisory status, and list of possible advisors
     * for a particular student as well as a list of students who have not completed the form.
     *
     * @param int $userid The user id of the student.
     * @return stdClass Object with properties students, current, closing, and available.
     */
    public static function get_advisor_selection_student_options($userid) {
        external_api::validate_context(context_system::instance());
        $params = self::validate_parameters(self::get_advisor_selection_student_options_parameters(), array('userid' => $userid));

        global $DB;
        $result = new stdClass();
        $result->students = convert_associative_to_object(get_student_without_advisor_form_list());
        $result->current = new stdClass();
        $result->current->value = $DB->get_field('local_mxschool_student', 'advisorid', array('userid' => $params['userid']));
        $result->current->text = format_faculty_name($result->current->value);
        $result->closing = $DB->get_field('local_mxschool_faculty', 'advisory_closing', array('userid' => $result->current->value));
        $result->available = convert_associative_to_object(get_available_advisor_list());
        return $result;
    }

    /**
     * Returns a description of the get_advisor_selection_student_options() function's return values.
     *
     * @return external_single_structure Object describing the return values
     *                                   of the get_advisor_selection_student_options() function.
     */
    public static function get_advisor_selection_student_options_returns() {
        return new external_single_structure(array(
            'students' => new external_multiple_structure(new external_single_structure(array(
                'value' => new external_value(
                    PARAM_INT, 'the user id of the student who has not completed an advisor selection form'
                ),
                'text' => new external_value(
                    PARAM_TEXT, 'the name of the student who has not completed an advisor selection form'
                )
            ))),
            'current' => new external_single_structure(array(
                'value' => new external_value(PARAM_INT, 'the user id of the student\' current advisor'),
                'text' => new external_value(PARAM_TEXT, 'the name of the student\' current advisor')
            )),
            'closing' => new external_value(PARAM_BOOL, 'whether the student\'s advisory is closing'),
            'available' => new external_multiple_structure(new external_single_structure(array(
                'value' => new external_value(PARAM_INT, 'the user id of the available faculty'),
                'text' => new external_value(PARAM_TEXT, 'the name of the available faculty')
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

        global $DB, $PAGE;
        $record = $DB->get_record('local_mxschool_adv_selection', array('userid' => $params['student']));
        if (!$record) {
            return false;
        }
        $record->selectedid = $params['choice'];
        $record->time_modified = time();
        local_mxschool\event\record_updated::create(array('other' => array(
            'page' => get_string('advisor_selection:report', 'local_mxschool')
        )))->trigger();
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
     * @return stdClass Object with properties students, dorm, roomtypes, gradedormmates, and dormmates.
     */
    public static function get_rooming_student_options($userid) {
        external_api::validate_context(context_system::instance());
        $params = self::validate_parameters(self::get_rooming_student_options_parameters(), array('userid' => $userid));

        global $DB;
        $result = new stdClass();
        $result->students = convert_associative_to_object(get_student_without_rooming_form_list());
        $record = $DB->get_record('local_mxschool_student', array('userid' => $params['userid']), 'dormid AS dorm, gender');
        $result->dorm = format_dorm_name($record->dorm);
        $result->roomtypes = convert_associative_to_object(get_room_type_list($record->gender));
        $result->gradedormmates = convert_associative_to_object(get_student_possible_same_grade_dormmate_list($params['userid']));
        $result->dormmates = convert_associative_to_object(get_student_possible_dormmate_list($params['userid']));
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
                'value' => new external_value(PARAM_INT, 'the user id of the student who has not completed a rooming form'),
                'text' => new external_value(PARAM_TEXT, 'the name of the student who has not completed a rooming form')
            ))),
            'dorm' => new external_value(PARAM_TEXT, 'the name of the student\'s current dorm'),
            'roomtypes' => new external_multiple_structure(new external_single_structure(array(
                'value' => new external_value(PARAM_TEXT, 'the internal name of the available room type'),
                'text' => new external_value(PARAM_TEXT, 'the localized name of the available room type')
            ))),
            'gradedormmates' => new external_multiple_structure(new external_single_structure(array(
                'value' => new external_value(PARAM_INT, 'the user id of the potential dormmate in the same grade'),
                'text' => new external_value(PARAM_TEXT, 'the name of the potential dormmate in the same grade')
            ))),
            'dormmates' => new external_multiple_structure(new external_single_structure(array(
                'value' => new external_value(PARAM_INT, 'the user id of the potential dormmate'),
                'text' => new external_value(PARAM_TEXT, 'the name of the potential dormmate')
            )))
        ));
    }

    /**
     * Returns descriptions of the get_vacation_travel_options() function's parameters.
     *
     * @return external_function_parameters Object holding array of parameters for the get_vacation_travel_options() function.
     */
    public static function get_vacation_travel_options_parameters() {
        return new external_function_parameters(array(
            'departure' => new external_single_structure(array(
                'mxtransportation' => new external_value(
                    PARAM_BOOL, 'Whether the student has selected that they require school transportation.', VALUE_OPTIONAL
                ),
                'type' => new external_value(PARAM_TEXT, 'The type of transportation specified.', VALUE_OPTIONAL),
                'site' => new external_value(PARAM_TEXT, 'The site of transportation specified.', VALUE_OPTIONAL)
            )),
            'return' => new external_single_structure(array(
                'mxtransportation' => new external_value(
                    PARAM_BOOL, 'Whether the student has selected that they require school transportation.', VALUE_OPTIONAL
                ),
                'type' => new external_value(PARAM_TEXT, 'The type of transportation specified.', VALUE_OPTIONAL),
                'site' => new external_value(PARAM_TEXT, 'The site of transportation specified.', VALUE_OPTIONAL)
            ))
        ));
    }

    /**
     * Queries the database to determine the available types and sites for a particular selection
     * as well as any default times and a list of students who have not completed the form.
     *
     * @param stdClass $departure Object which may have properties mxtransportation, type, and site.
     * @param stdClass $return Object which may have properties mxtransportation, type, and site.
     * @return stdClass Object with properties students, departure, and return.
     */
    public static function get_vacation_travel_options($departure, $return) {
        external_api::validate_context(context_system::instance());
        $params = self::validate_parameters(self::get_vacation_travel_options_parameters(), array(
            'departure' => $departure, 'return' => $return
        ));

        global $DB;
        $result = new stdClass();
        $result->students = convert_associative_to_object(get_student_without_vacation_travel_form_list());
        $result->departure = new stdClass();
        $result->departure->types = get_vacation_travel_type_list($params['departure']['mxtransportation'] ?? null);
        $list = get_vacation_travel_departure_sites_list($params['departure']['type'] ?? null);
        $result->departure->sites = array_map(function($id) {
            return (string) $id;
        }, array_keys($list));
        if (isset($params['departure']['site'])) {
            $default = $DB->get_field_select(
                'local_mxschool_vt_site', 'default_departure_time', 'id = ? AND deleted = 0 AND enabled_departure = 1',
                array($params['departure']['site'])
            );
            $result->departure->default = enumerate_timestamp($default, 15);
        } else {
            $result->departure->default = enumerate_timestamp(false);
        }
        $result->return = new stdClass();
        $result->return->types = get_vacation_travel_type_list($params['return']['mxtransportation'] ?? null);
        $list = get_vacation_travel_return_sites_list($params['return']['type'] ?? null);
        $result->return->sites = array_map(function($id) {
            return (string) $id;
        }, array_keys($list));
        if (isset($params['return']['site'])) {
            $default = $DB->get_field_select(
                'local_mxschool_vt_site', 'default_return_time', 'id = ? AND deleted = 0 AND enabled_return = 1',
                array($params['return']['site'])
            );
            $result->return->default = enumerate_timestamp($default, 15);
        } else {
            $result->return->default = enumerate_timestamp(false);
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
                'value' => new external_value(PARAM_INT, 'the user id of the student who has not completed a vacation travel form'),
                'text' => new external_value(PARAM_TEXT, 'the name of the student who has not completed a vacation travel form')
            ))),
            'departure' => new external_single_structure(array(
                'types' => new external_multiple_structure(
                    new external_value(PARAM_TEXT, 'the type which is available given the filter')
                ),
                'sites' => new external_multiple_structure(
                    new external_value(PARAM_TEXT, 'the id of the site which is available given the filter')
                ),
                'default' => new external_single_structure(array(
                    'year' => new external_value(PARAM_TEXT, 'the year of the default time for the transportation, if applicable'),
                    'month' => new external_value(
                        PARAM_TEXT, 'the month of the default time for the transportation, if applicable'
                    ),
                    'day' => new external_value(PARAM_TEXT, 'the day of the default time for the transportation, if applicable'),
                    'hour' => new external_value(PARAM_TEXT, 'the hour of the default time for the transportation, if applicable'),
                    'minute' => new external_value(
                        PARAM_TEXT, 'the minute of the default time for the transportation, if applicable'
                    ),
                    'ampm' => new external_value(PARAM_TEXT, 'the AM/PM of the default time for the transportation, if applicable')
                ))
            )),
            'return' => new external_single_structure(array(
                'types' => new external_multiple_structure(
                    new external_value(PARAM_TEXT, 'the type which is available given the filter')
                ),
                'sites' => new external_multiple_structure(
                    new external_value(PARAM_TEXT, 'the id of the site which is available given the filter')
                ),
                'default' => new external_single_structure(array(
                    'year' => new external_value(PARAM_TEXT, 'the year of the default time for the transportation, if applicable'),
                    'month' => new external_value(
                        PARAM_TEXT, 'the month of the default time for the transportation, if applicable'
                    ),
                    'day' => new external_value(PARAM_TEXT, 'the day of the default time for the transportation, if applicable'),
                    'hour' => new external_value(PARAM_TEXT, 'the hour of the default time for the transportation, if applicable'),
                    'minute' => new external_value(
                        PARAM_TEXT, 'the minute of the default time for the transportation, if applicable'
                    ),
                    'ampm' => new external_value(PARAM_TEXT, 'the AM/PM of the default time for the transportation, if applicable')
                ))
            ))
        ));
    }

  /**
	* Returns descriptions of the update_comment() function's parameters.
	*
	* @return external_function_parameters Object holding array of parameters for the update_healthform_comment() function.
	*/
	public static function update_comment_parameters() {
		return new external_function_parameters(array(
			'id' => new external_value(PARAM_INT, 'The id of the user whose health comment to update.'),
			'text' => new external_value(PARAM_TEXT, 'The text of the new health comment.'),
			'table' => new external_value(PARAM_TEXT, 'The table to edit the comment for'),
		));
	}

	/**
	* Given the text and the user's id, updates healthform comment
	*
	* @param int id, the id of the column to change
	* @param String text, the text for the comment
	* @return boolean true if succesful
	*/
	public static function update_comment($id, $text, $table) {
		 external_api::validate_context(context_system::instance());
		 $params = self::validate_parameters(self::update_comment_parameters(), array(
		     'id' => $id, 'text' => $text, 'table' => $table)
		 );
		 global $DB;
		 if ($table == 'local_mxschool_healthpass') {
			 if(!$DB->record_exists('local_mxschool_healthpass', array('userid' => $id))) {
				  $record = new stdClass();
				  $record->userid = $userid;
				  $record->status = 'placeholder';
				  $record->body_temperature = 'temp';
				  $record->comment = $text;
				  $record->form_submitted = 0;
				  $DB->insert_record('local_mxschool_healthpass', $record);
				  return true;
			 }
			 $DB->set_field($table, 'comment', $text, array('userid' => $id));
			 return true;
		 }
		 if ($table == 'local_mxschool_deans_perm_0') {
			 $DB->set_field('local_mxschool_deans_perm', 'internal_comment', $text, array('id' => $id));
			  return true;
		 }
		 if ($table == 'local_mxschool_deans_perm_1') {
			 $DB->set_field('local_mxschool_deans_perm', 'external_comment', $text, array('id' => $id));
			  return true;
		 }
		 $DB->set_field($table, 'comment', $text, array('id' => $id));
		  return true;
		}

	/**
	 * Returns a description of the update_healthform_comment() function's return value.
	 *
	 * @return external_value Object describing the return value of the update_healthform_comment() function.
	 */
	public static function update_comment_returns() {
	    return new external_value(PARAM_BOOL, 'True if the operation is succesful, false otherwise.');
	}

	/**
	* Returns descriptions of the do_alternating_button_action() function's parameters.
	*
	* @return external_function_parameters Object holding array of parameters for the update_healthform_comment() function.
	*/
	public static function do_alternating_button_action_parameters() {
		return new external_function_parameters(array(
			'id' => new external_value(PARAM_INT, 'The id of the alternating button'),
			'name' => new external_value(PARAM_TEXT, 'The name of the alternating button.'),
			'value' => new external_value(PARAM_INT, 'The value of the alternating button'),
			'package' => new external_value(PARAM_TEXT, 'The name of the package of which the alternating button is a part')
		));
	}

	/**
	* Given an alternating button's info, performs a specific action.
	*
	* @param int id, the id of the button
	* @param string name, the name of the button.
	* @param int value, the value of the button, 0-2.
	* @param string package_name, the name of the package.
	*/
	public static function do_alternating_button_action($id, $name, $value, $package) {
		 external_api::validate_context(context_system::instance());
		 $params = self::validate_parameters(self::do_alternating_button_action_parameters(), array(
			'id' => $id, 'name' => $name, 'value' => $value, 'package' => $package)
		 );
		 global $DB;
		 $new_value = $params['value'] + 1;
		 if($new_value==3) $new_value = 0;
		 switch($params['package']){
			case 'deans_permission':
				$field = '';
			 	switch($params['name']) {
					case 'sports':
						$field = 'sports_perm';
						if($new_value == 1) (new local_mxschool\local\deans_permission\sports_permission_request($params['id']))->send();
						break;
					case 'studyhours':
						$field = 'studyhours_perm';
						break;
					case 'class':
						$field = 'class_perm';
						if($new_value == 1) (new local_mxschool\local\deans_permission\class_permission_request($params['id']))->send();
						break;
					case 'deans':
						if($new_value == 2) (new local_mxschool\local\deans_permission\deans_permission_approved($params['id']))->send();
						$field = 'dean_perm';
						break;
					default:
						throw new coding_exception("Unsupported name '{$params['name']}' for package {$params['package']}.");
				}
			     $DB->set_field('local_mxschool_deans_perm', $field, $new_value, array('id' => $params['id']));
				break;
			default:
				throw new coding_exception("Unsupported package: {$params['package']}.");
				break;
		}
		return true;
	}
	/**
	* Returns descriptions of the do_alternating_button_action() function's returns.
	*
	* @return external_value Object describing the return value of the update_healthform_comment() function.
	*/
	public static function do_alternating_button_action_returns() {
	    return new external_value(PARAM_BOOL, 'True if the operation is succesful, false otherwise.');
	}

 /**
 * Returns descriptions of the update_healthform_override_status() function's parameters.
 *
 * @return external_function_parameters Object holding array of parameters for the update_healthform_comment() function.
 */
    public static function update_healthform_override_status_parameters() {
    	  return new external_function_parameters(array(
		   'userid' => new external_value(PARAM_INT, 'The id of the user whose health comment to update.'),
		   'status' => new external_value(PARAM_TEXT, 'The current health status of the user.'),
		   'override_status' => new external_value(PARAM_TEXT, 'The current override status of the user.')
    	   ));
    }

    /**
    * Given a healthform's current override status, updates it accordingly
    *
    * @param int userid, the user's id.
    * @param String status, the user's current healthform Status
    * @param String override_status, the user's current override status
    * @return boolean true if successful
    */
    public static function update_healthform_override_status($userid, $status, $override_status) {
     external_api::validate_context(context_system::instance());
	$params = self::validate_parameters(self::update_healthform_override_status_parameters(), array(
	    'userid' => $userid, 'status' => $status, 'override_status' => $override_status)
	);
	global $DB;
	switch($override_status) {
		case 'Not Overridden':
		    $DB->execute(
			    "UPDATE {local_mxschool_healthpass} hp
				SET hp.override_status = 'Under Review'
				WHERE hp.userid = {$userid}"
		    );
		    return true;
		    break;
	    case 'Under Review':
		   $new_status = $status=='Denied' ? 'Approved' : 'Denied';
		   $DB->execute(
			   "UPDATE {local_mxschool_healthpass} hp
			    SET hp.override_status = 'Overridden', hp.status = '{$new_status}'
			    WHERE hp.userid = {$userid}"
		   );
		   return true;
		   break;
	    case 'Overridden':
		    $new_status = $status=='Denied' ? 'Approved' : 'Denied';
		    $DB->execute(
			    "UPDATE {local_mxschool_healthpass} hp
				SET hp.override_status = 'Not Overridden', hp.status = '{$new_status}'
				WHERE hp.userid = {$userid}"
		    );
		    return true;
		    break;
	    default:
		    throw new \coding_exception("Unknown override status in database: {$override_status}");
	  }
    }

   /**
   * Returns a description of the update_healthform_override_status() function's return value.
   *
   * @return external_value Object describing the return value of the update_healthform_override_status() function.
   */
    public static function update_healthform_override_status_returns() {
    		return new external_value(PARAM_BOOL, 'True if the operation is succesful, false otherwise.');
    }

    /**
    * Returns descriptions of the update_approve_deny_cell() function's parameters.
    *
    * @return external_function_parameters Object holding array of parameters for the approve_deny_cell() function.
    */
    public static function update_approve_deny_cell_parameters() {
		 return new external_function_parameters(array(
			  'id' => new external_value(PARAM_INT, 'The id of row to update'),
			  'field' => new external_value(PARAM_TEXT, 'The field to update'),
			  'table' => new external_value(PARAM_TEXT, 'The table to update'),
			  'new_value' => new external_value(PARAM_INT, 'The new value to insert into the database'),
		  ));
    }

    /**
    * Updates an approved/deny cell
    *
    * @param int id, the id of the row to update
    * @param String field, the field to update
    * @param String table, the table to updat
    * @param int new_value, the new value
    * @return boolean true when succesful
    */
	public static function update_approve_deny_cell($id, $field, $table, $new_value) {
		external_api::validate_context(context_system::instance());
		$params = self::validate_parameters(self::update_approve_deny_cell_parameters(), array(
		    'id' => $id, 'field' => $field, 'table' => $table, 'new_value' => $new_value)
		);
		global $DB;
		$DB->set_field($table, $field, $new_value, array('id' => $id));
		if($table == 'local_mxschool_deans_perm') {
			if($new_value == 1) {
			    return (new local_mxschool\local\deans_permission\deans_permission_approved($id))->send();
			}
			else if($new_value == 2) {
			    return (new local_mxschool\local\deans_permission\deans_permission_denied($id))->send();
			}
		}
		return true;
	}

	/**
	 * Returns a description of the update_approve_deny_cell() function's return value.
	 *
	 * @return external_value Object describing the return value of the update_healthform_comment() function.
	 */
	public static function update_approve_deny_cell_returns() {
	    return new external_value(PARAM_BOOL, 'True if the operation is succesful, false otherwise.');
	}
}
