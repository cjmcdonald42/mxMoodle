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

        $list = get_students_in_dorm_list($dorm);
        $result = array();
        foreach ($list as $userid => $name) {
            $result[] = array('userid' => $userid, 'name' => $name);
        }
        return $result;
    }

    /**
     * Returns a description of the get_dorm_students() function's return values.
     *
     * @return external_multiple_structure Object describing the returned values.
     */
    public static function get_dorm_students_returns() {
        return new external_multiple_structure(new external_single_structure(array(
                'userid' => new external_value(PARAM_INT, 'id of the student'),
                'name' => new external_value(PARAM_TEXT, 'name of the student')
        )));
    }

}
