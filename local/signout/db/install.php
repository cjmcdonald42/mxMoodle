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
 * Database installation steps for Middlesex's off_campus Subplugin.
 *
 * @package     local_signout
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_signout_install() {
    global $DB;

    $subpackages = array(
        array('package' => 'signout', 'pages' => json_encode(array('combined_report' => 'combined_report.php'))),
        array('package' => 'signout', 'subpackage' => 'on_campus', 'pages' => json_encode(array(
            'preferences' => 'preferences.php', 'form' => 'on_campus_enter.php', 'report' => 'on_campus_report.php',
            'duty_report' => 'duty_report.php'
        ))),
        array('package' => 'signout', 'subpackage' => 'off_campus', 'pages' => json_encode(array(
            'preferences' => 'preferences.php', 'form' => 'off_campus_enter.php', 'report' => 'off_campus_report.php'
        )))
    );
    foreach ($subpackages as $subpackage) {
        $DB->insert_record('local_mxschool_subpackage', (object) $subpackage);
    }

    $locations = array(
        array('name' => 'Health Center', 'grade' => 9),
        array('name' => 'Supervised Study Hall', 'grade' => 9),
        array('name' => 'Library', 'grade' => 11),
        array('name' => 'Terry Room', 'grade' => 11),
        array('name' => 'Tech Center', 'grade' => 11),
        array('name' => 'Rachel Carson Center', 'grade' => 11),
        array('name' => 'Clay Centenial Center Lobby', 'grade' => 11),
        array('name' => 'Bass Arts Pavilion', 'grade' => 11),
        array('name' => 'StuFac', 'grade' => 12),
        array('name' => 'Gym', 'grade' => 12),
        array('name' => 'On Campus', 'grade' => 12, 'warning' => 'You need face-to-face permission from the person on duty in your dorm to sign out \'On Campus.\'')
    );
    foreach ($locations as $location) {
        $DB->insert_record('local_signout_location', (object) $location);
    }
}
