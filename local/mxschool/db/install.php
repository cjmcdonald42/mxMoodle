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
 * Database installation steps for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_mxschool_install() {
    global $DB;

    set_config('weekend_form_instructions_top', 'Please fill out the form entirely. Your form should be submitted to your Head of House no later than <b>10:30 PM on Friday</b>.<br>All relevant phone calls giving permission should also be received by Friday at 10:30 PM <i>(Voice mail messages are OK; Email messages are NOT)</i>.', 'local_mxschool');
    set_config('weekend_form_instructions_bottom', 'You may not leave for the weekend until you see your name on the \'OK\' list.<br>Permission phone calls should be addressed to <b>{hoh}</b> @ <b>{permissionsline}</b>.<br>If your plans change, you must get permission from <b>{hoh}</b>. <b>Remember to sign out.</b>', 'local_mxschool');

    set_config('esignout_form_iperror', 'You must be on Middlesex\'s network to access this form.', 'local_mxschool');
    set_config('esignout_report_iperror', 'You must be on Middlesex\'s network to sign in.', 'local_mxschool');
    set_config('esignout_form_instructions', 'Your driver must have submitted a form to be in the list below.', 'local_mxschool');
    set_config('esignout_form_warning_nopassengers', 'Your permissions indicate that you may not drive passengers.', 'local_mxschool');
    set_config('esignout_form_warning_needparent', 'Your permissions indicate that you need a call from your parent.', 'local_mxschool');
    set_config('esignout_form_warning_onlyspecific', 'Your permissions indicate that you may only be the passenger of the following drivers: ', 'local_mxschool');
    set_config('esignout_form_confirmation', 'Have you recieved the required permissions?', 'local_mxschool');

    set_config('esignout_notification_warning_driver', 'None.', 'local_mxschool');
    set_config('esignout_notification_warning_any', 'None.', 'local_mxschool');
    set_config('esignout_notification_warning_parent', 'This student requires parent permission to be the passenger of another student.', 'local_mxschool');
    set_config('esignout_notification_warning_specific', 'This student only has permission to the be the passenger of the following drivers: ', 'local_mxschool');
    set_config('esignout_notification_warning_over21', 'This student does NOT have permission to be the passenger of anyone under 21.', 'local_mxschool');

    set_config('advisor_form_closing_warning', 'Your current advisor\'s advisory is closing, so you must provide choices for a new advisor.', 'local_mxschool');
    set_config('advisor_form_instructions', 'Please rank you top five advisor choices in descending order. You may rank less than five if your final choice is your current advisor.', 'local_mxschool');

    set_config('rooming_form_roommate_instructions', 'Because there are several one-room doubles on campus, there are years when students who prefer to be in a single must live in a double. If you have not lived in a one-room double before, please indicate with whom you would want to live if placed in one.', 'local_mxschool');

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
