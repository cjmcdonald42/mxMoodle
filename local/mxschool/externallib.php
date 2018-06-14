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
require_once(__DIR__.'/locallib.php');

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
     * @return array The students in that dorm as [userid, name].
     */
    public static function get_dorm_students($dorm) {
        external_api::validate_context(context_system::instance());
        require_capability('local/mxschool:manage_weekend', context_system::instance());
        $params = self::validate_parameters(self::get_dorm_students_parameters(), array('dorm' => $dorm));

        $list = get_students_in_dorm_list($params['dorm']);
        $result = array();
        foreach ($list as $userid => $name) {
            $result[] = array('userid' => $userid, 'name' => $name);
        }
        return $result;
    }

    /**
     * Returns a description of the get_dorm_students() function's return values.
     *
     * @return external_multiple_structure Object describing the return values.
     */
    public static function get_dorm_students_returns() {
        return new external_multiple_structure(new external_single_structure(array(
                'userid' => new external_value(PARAM_INT, 'id of the student'),
                'name' => new external_value(PARAM_TEXT, 'name of the student')
        )));
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
     * @return bool True if successful, false otherwise.
     */
    public static function set_boolean_field($table, $field, $id, $value) {
        external_api::validate_context(context_system::instance());
        // This may need to change in the future to make this function more reusable.
        require_capability('local/mxschool:manage_weekend', context_system::instance());
        $params = self::validate_parameters(self::set_boolean_field_parameters(), array(
            'table' => $table, 'field' => $field, 'id' => $id, 'value' => $value)
        );

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
     * @return external_value Object describing the return value.
     */
    public static function set_boolean_field_returns() {
        return new external_value(PARAM_BOOL, 'True if the operation was succesful, false otherwise.');
    }

}
