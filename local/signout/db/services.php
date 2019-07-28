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
 * Services for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'local_signout_get_on_campus_student_options' => array(
        'classname' => 'local_signout_external',
        'methodname' => 'get_on_campus_student_options',
        'classpath' => 'local/signout/externallib.php',
        'description' => 'Queries the database to determine the location options and any warnings for a selected student.',
        'type' => 'read',
        'ajax' => 'true'
    ),
    'local_signout_get_off_campus_student_options' => array(
        'classname' => 'local_signout_external',
        'methodname' => 'get_off_campus_student_options',
        'classpath' => 'local/signout/externallib.php',
        'description' => 'Queries the database to determine the type options, passenger list, driver list,'
                         .'and permissions for a selected student.',
        'type' => 'read',
        'ajax' => 'true'
    ),
    'local_signout_get_off_campus_driver_details' => array(
        'classname' => 'local_signout_external',
        'methodname' => 'get_off_campus_driver_details',
        'classpath' => 'local/signout/externallib.php',
        'description' => 'Queries the database to find the destination and departure time of an off-campus signout driver record.',
        'type' => 'read',
        'ajax' => 'true'
    ),
    'local_signout_sign_in' => array(
        'classname' => 'local_signout_external',
        'methodname' => 'sign_in',
        'classpath' => 'local/signout/externallib.php',
        'description' => 'Signs in an eSignout record and records the timestamp.',
        'type' => 'write',
        'ajax' => 'true'
    ),
    'local_signout_confirm_signout' => array(
        'classname' => 'local_signout_external',
        'methodname' => 'confirm_signout',
        'classpath' => 'local/signout/externallib.php',
        'description' => 'Confirms an on-campus signout record and records the timestamp.',
        'type' => 'write',
        'ajax' => 'true'
    )
);
