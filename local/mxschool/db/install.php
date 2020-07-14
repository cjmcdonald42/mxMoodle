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

    set_config('weekend_form_instructions_top', 'Please fill out the form entirely. Your form should be submitted to your Head of House no later than <b>10:30 PM on Friday</b>.<br>All relevant phone calls giving permission should also be received by Friday at 10:30 PM <i>(Voice mail messages are OK; Email messages are NOT)</i>.', 'local_mxschool');
    set_config('weekend_form_instructions_bottom', 'You may not leave for the weekend until you see your name on the \'OK\' list.<br>Permission phone calls should be addressed to <b>{hoh}</b> @ <b>{permissionsline}</b>.<br>If your plans change, you must get permission from <b>{hoh}</b>. <b>Remember to sign out.</b>', 'local_mxschool');
    set_config('weekend_form_warning_closed', 'The weekend you have selected is a closed weekend - you will need special permissions from the deans.', 'local_mxschool');

    set_config('advisor_form_enabled_who', 'all', 'local_mxschool');
    set_config('advisor_form_closing_warning', 'Your current advisor\'s advisory is closing, so you must provide choices for a new advisor.', 'local_mxschool');
    set_config('advisor_form_instructions', 'Please rank you top five advisor choices in descending order. You may rank less than five if your final choice is your current advisor.', 'local_mxschool');

    set_config('rooming_form_roommate_instructions', 'Because there are several one-room doubles on campus, there are years when students who prefer to be in a single must live in a double. If you have not lived in a one-room double before, please indicate with whom you would want to live if placed in one.', 'local_mxschool');

    set_config('vacation_form_returnenabled', '1', 'local_mxschool');

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
	    ))),
	    array('subpackage' => 'deans_permission', 'pages' => json_encode(array(
		   'preferences', 'form', 'report'
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
