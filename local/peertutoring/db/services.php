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
 * Services for Middlesex's Peer Tutoring Subplugin.
 *
 * @package     local_peertutoring
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'local_peertutoring_get_available_tutors' => array(
        'classname' => 'local_peertutoring_external',
        'methodname' => 'get_available_tutors',
        'classpath' => 'local/peertutoring/externallib.php',
        'description' => 'Queries the database to determine the list of students who are available to be added as peer tutors.',
        'type' => 'read',
        'ajax' => 'true'
    ),
    'local_peertutoring_get_department_courses' => array(
        'classname' => 'local_peertutoring_external',
        'methodname' => 'get_department_courses',
        'classpath' => 'local/peertutoring/externallib.php',
        'description' => 'Queries the database to find all courses in a specified department.',
        'type' => 'read',
        'ajax' => 'true'
    ),
    'local_peertutoring_get_tutor_options' => array(
        'classname' => 'local_peertutoring_external',
        'methodname' => 'get_tutor_options',
        'classpath' => 'local/peertutoring/externallib.php',
        'description' => 'Queries the database to determine the approved departments for a specified tutor',
        'type' => 'read',
        'ajax' => 'true'
    )
);
