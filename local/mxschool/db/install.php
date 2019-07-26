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
 * Database installation steps for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_mxschool_install() {
    global $DB;

    $subpackages = array(
        array('subpackage' => 'user_management', 'pages' => json_encode(array(
            'student_report', 'faculty_report', 'dorm_report', 'vehicle_report', 'picture_import'
        ))),
        array('subpackage' => 'checkin', 'pages' => json_encode(array(
            'preferences', 'generic_report', 'weekday_report', 'weekend_form', 'weekend_report', 'weekend_calculator'
        ))),
        array('subpackage' => 'advisor_selection', 'pages' => json_encode(array('preferences', 'form', 'report'))),
        array('subpackage' => 'rooming', 'pages' => json_encode(array('preferences', 'form', 'report'))),
        array('subpackage' => 'vacation_travel', 'pages' => json_encode(array(
            'preferences', 'form', 'report', 'transportation_report'
        )))
    );
    foreach ($subpackages as $subpackage) {
        $DB->insert_record('local_mxschool_subpackage', (object) $subpackage);
    }

    $sites = array(
        array('name' => 'Logan', 'type' => 'Plane', 'enabled_departure' => 1, 'enabled_return' => 1),
        array('name' => 'South Station', 'type' => 'Train', 'enabled_departure' => 1, 'enabled_return' => 1),
        array('name' => '128 Westwood', 'type' => 'Train', 'enabled_departure' => 1, 'enabled_return' => 1),
        array('name' => 'South Station', 'type' => 'Bus', 'enabled_departure' => 1, 'enabled_return' => 1),
        array('name' => 'Stamford, CT', 'type' => 'NYC Direct', 'enabled_departure' => 1, 'enabled_return' => 1),
        array('name' => 'Upper East Side (NYC)', 'NYC Direct' => 'Plane', 'enabled_departure' => 1, 'enabled_return' => 1),
        array('name' => 'Penn Station', 'type' => 'NYC Direct', 'enabled_departure' => 1, 'enabled_return' => 1)
    );
    foreach ($sites as $site) {
        $DB->insert_record('local_mxschool_vt_site', (object) $site);
    }
}
