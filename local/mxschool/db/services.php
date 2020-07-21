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
 * Services for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'local_mxschool_set_boolean_field' => array(
        'classname' => 'local_mxschool_external',
        'methodname' => 'set_boolean_field',
        'classpath' => 'local/mxschool/externallib.php',
        'description' => 'Sets a boolean field in the database.',
        'type' => 'write',
        'ajax' => 'true'
    ),
    'local_mxschool_send_email' => array(
        'classname' => 'local_mxschool_external',
        'methodname' => 'send_email',
        'classpath' => 'local/mxschool/externallib.php',
        'description' => 'Sends an email to users based on predefined a email class.',
        'type' => 'read',
        'ajax' => 'true'
    ),
    'local_mxschool_get_dorm_students' => array(
        'classname' => 'local_mxschool_external',
        'methodname' => 'get_dorm_students',
        'classpath' => 'local/mxschool/externallib.php',
        'description' => 'Queries the database to find all students in a specified dorm.',
        'type' => 'read',
        'ajax' => 'true'
    ),
    'local_mxschool_get_weekend_type' => array(
        'classname' => 'local_mxschool_external',
        'methodname' => 'get_weekend_type',
        'classpath' => 'local/mxschool/externallib.php',
        'description' => 'Queries the database to determine the type of a weekend specified by a timestamp.',
        'type' => 'read',
        'ajax' => 'true'
    ),
    'local_mxschool_get_advisor_selection_student_options' => array(
        'classname' => 'local_mxschool_external',
        'methodname' => 'get_advisor_selection_student_options',
        'classpath' => 'local/mxschool/externallib.php',
        'description' => 'Queries the database to determine the current advisor, advisory status, and list of possible advisors'
                       . 'for a particular student as well as a list of students who have not completed the form.',
        'type' => 'read',
        'ajax' => 'true'
    ),
    'local_mxschool_select_advisor' => array(
        'classname' => 'local_mxschool_external',
        'methodname' => 'select_advisor',
        'classpath' => 'local/mxschool/externallib.php',
        'description' => 'Selects the advisor for a student.',
        'type' => 'write',
        'ajax' => 'true',
        'capabilities' => 'local/mxschool:manage_advisor_selection'
    ),
    'local_mxschool_get_rooming_student_options' => array(
        'classname' => 'local_mxschool_external',
        'methodname' => 'get_rooming_student_options',
        'classpath' => 'local/mxschool/externallib.php',
        'description' => 'Queries the database to determine the current dorm, list of available room types,'
                       . 'list of possible dormmates in the same grade,'
                       . 'and list of possible dormmates in any grade for a particular student'
                       . 'as well as a list of students who have not completed the form.',
        'type' => 'read',
        'ajax' => 'true'
    ),
    'local_mxschool_get_vacation_travel_options' => array(
        'classname' => 'local_mxschool_external',
        'methodname' => 'get_vacation_travel_options',
        'classpath' => 'local/mxschool/externallib.php',
        'description' => 'Queries the database to determine the available types and sites for a particular selection'
                       . 'as well as any default times and a list of students who have not completed the form.',
        'type' => 'read',
        'ajax' => 'true'
   ),
   'local_mxschool_update_comment' => array(
	  'classname' => 'local_mxschool_external',
	  'methodname' => 'update_comment',
	  'classpath' => 'local/mxschool/externallib.php',
	  'description' => 'Updates the comment field in a given table',
	  'type' => 'write',
	  'ajax' => 'true'
  ),
  'local_mxschool_do_alternating_button_action' => array(
    'classname' => 'local_mxschool_external',
    'methodname' => 'do_alternating_button_action',
    'classpath' => 'local/mxschool/externallib.php',
    'description' => 'Given an alternating buttons info, do a specific action',
    'type' => 'write',
    'ajax' => 'true'
  ),
    'local_mxschool_update_healthform_override_status' => array(
	   'classname' => 'local_mxschool_external',
	   'methodname' => 'update_healthform_override_status',
	   'classpath' => 'local/mxschool/externallib.php',
	   'description' => 'Updates the given users healthform override status.',
	   'type' => 'write',
	   'ajax' => 'true'
    )
);
