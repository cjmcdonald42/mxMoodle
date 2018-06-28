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
 * Services for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'local_mxschool_get_dorm_students' => array(
        'classname' => 'local_mxschool_external',
        'methodname' => 'get_dorm_students',
        'classpath' => 'local/mxschool/externallib.php',
        'description' => 'Queries the database to find all students in a specified dorm.',
        'type' => 'read',
        'ajax' => 'true',
        'capabilities' => 'local/mxschool:manage_weekend'
    ), 'local_mxschool_set_boolean_field' => array(
        'classname' => 'local_mxschool_external',
        'methodname' => 'set_boolean_field',
        'classpath' => 'local/mxschool/externallib.php',
        'description' => 'Sets a boolean field in the database.',
        'type' => 'write',
        'ajax' => 'true',
        'capabilities' => 'local/mxschool:manage_weekend'
    ), 'local_mxschool_send_email' => array(
        'classname' => 'local_mxschool_external',
        'methodname' => 'send_email',
        'classpath' => 'local/mxschool/externallib.php',
        'description' => 'Sends an email to users based on predefined a email class.',
        'type' => 'read',
        'ajax' => 'true',
        'capabilities' => 'local/mxschool:manage_weekend'
    ), 'local_mxschool_get_esignout_student_options' => array(
        'classname' => 'local_mxschool_external',
        'methodname' => 'get_esignout_student_options',
        'classpath' => 'local/mxschool/externallib.php',
        'description' => 'Queries the database to determine the type options, passenger list, driver list,
                          and permissions for a selected student.',
        'type' => 'read',
        'ajax' => 'true'
    ), 'local_mxschool_get_esignout_driver_details' => array(
        'classname' => 'local_mxschool_external',
        'methodname' => 'get_esignout_driver_details',
        'classpath' => 'local/mxschool/externallib.php',
        'description' => 'Queries the database to find the destination and departure time of an esignout driver record.',
        'type' => 'read',
        'ajax' => 'true'
    ), 'local_mxschool_sign_in' => array(
        'classname' => 'local_mxschool_external',
        'methodname' => 'sign_in',
        'classpath' => 'local/mxschool/externallib.php',
        'description' => 'Signs in an eSignout record and records the timestamp.',
        'type' => 'write',
        'ajax' => 'true'
    )
);
