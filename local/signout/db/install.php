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

    set_config('on_campus_form_enabled', '1', 'local_signout');
    set_config('on_campus_form_ipenabled', '1', 'local_signout');
    set_config('on_campus_confirmation_enabled', '0', 'local_signout');
    set_config('on_campus_refresh_rate', '60', 'local_signout');
    set_config('on_campus_confirmation_undo_window', '5', 'local_signout');
    set_config('on_campus_form_iperror', 'You must be on Middlesex\'s network to access this form.', 'local_signout');
    set_config('on_campus_signin_iperror_boarder', 'You must be on Middlesex\'s network to sign back in to your dorm.', 'local_signout');
    set_config('on_campus_signin_iperror_day', 'You must be on Middlesex\'s network to be going home.', 'local_signout');
    set_config('on_campus_form_warning_underclassmen', 'You need special permission to go to any \'other\' location.', 'local_signout');
    set_config('on_campus_form_warning_juniors', 'You need special permission to go to a non-academic location.', 'local_signout');
    set_config('on_campus_form_confirmation', 'Have you received the required permissions?', 'local_signout');

    set_config('off_campus_edit_window', '30', 'local_signout');
    set_config('off_campus_trip_window', '30', 'local_signout');
    set_config('off_campus_form_enabled', '1', 'local_signout');
    set_config('off_campus_form_permissions_active', '0', 'local_signout');
    set_config('off_campus_form_ipenabled', '1', 'local_signout');
    set_config('off_campus_form_iperror', 'You must be on Middlesex\'s network to access this form.', 'local_signout');
    set_config('off_campus_signin_iperror', 'You must be on Middlesex\'s network to sign in.', 'local_signout');
    set_config('off_campus_form_instructions_passenger', 'Your driver must have submitted a form and selected you as a passenger to appear in the list below.', 'local_signout');
    set_config('off_campus_form_instructions_bottom', 'You will have {minutes} minutes to edit your form once you have submitted it.', 'local_signout');
    set_config('off_campus_form_confirmation', 'Have you received the required permissions?', 'local_signout');
    set_config('off_campus_form_warning_driver_nopassengers', 'Your permissions indicate that you may not drive other students.', 'local_signout');
    set_config('off_campus_form_warning_passenger_parent', 'Your permissions indicate that you need a call from your parent to ride with another student.', 'local_signout');
    set_config('off_campus_form_warning_passenger_specific', 'Your permissions indicate that you may only ride with the following drivers:', 'local_signout');
    set_config('off_campus_form_warning_passenger_over21', 'Your permissions indicate that you are not allowed to ride with a driver who is under 21.', 'local_signout');
    set_config('off_campus_form_warning_rideshare_parent', 'Your permissions indicate that you need a call from your parent to use a car service.', 'local_signout');
    set_config('off_campus_form_warning_rideshare_notallowed', 'Your permissions indicate that you are not allowed to use a car service.', 'local_signout');
    set_config('off_campus_notification_warning_driver_nopassengers', 'This student does NOT have permission to drive other students.', 'local_signout');
    set_config('off_campus_notification_warning_passenger_parent', 'This student requires parent permission to ride with another student.', 'local_signout');
    set_config('off_campus_notification_warning_passenger_specific', 'This student only has permission to ride with of the following drivers:', 'local_signout');
    set_config('off_campus_notification_warning_passenger_over21', 'This student does NOT have permission to ride with anyone under 21.', 'local_signout');
    set_config('off_campus_notification_warning_rideshare_parent', 'This student requires parent permission to use a car service.', 'local_signout');
    set_config('off_campus_notification_warning_rideshare_notallowed', 'This student does NOT have permission to use a car service.', 'local_signout');
    set_config('off_campus_notification_warning_irregular', '[Irregular] ', 'local_signout');

    $subpackages = array(
        array('package' => 'signout', 'pages' => json_encode(array('combined_report'))),
        array('package' => 'signout', 'subpackage' => 'on_campus', 'pages' => json_encode(array(
            'preferences', 'form', 'report', 'duty_report'
        ))),
        array('package' => 'signout', 'subpackage' => 'off_campus', 'pages' => json_encode(array('preferences', 'form', 'report')))
    );
    foreach ($subpackages as $subpackage) {
        $DB->insert_record('local_mxschool_subpackage', (object) $subpackage);
    }

    $locations = array(
        array('name' => 'Health Center'),
        array('name' => 'Supervised Study Hall'),
        array('name' => 'Play Rehearsal / Tech'),
        array('name' => 'Library', 'grade' => 11),
        array('name' => 'Terry Room', 'grade' => 11),
        array('name' => 'Tech Center', 'grade' => 11),
        array('name' => 'Rachel Carson Center', 'grade' => 11),
        array('name' => 'Clay Centenial Center Lobby', 'grade' => 11),
        array('name' => 'Bass Arts Pavilion', 'grade' => 11),
        array('name' => 'StuFac', 'grade' => 12),
        array('name' => 'Gym', 'grade' => 12),
        array(
            'name' => 'On Campus', 'grade' => 12,
            'warning' => 'You need face-to-face permission from the person on duty in your dorm to sign out \'On Campus.\''
        )
    );
    foreach ($locations as $location) {
        $DB->insert_record('local_signout_location', (object) $location);
    }

    $types = array(
        array('required_permissions' => 'driver', 'name' => 'Driving', 'grade' => 11, 'boarding_status' => 'Day'),
        array('required_permissions' => 'passenger', 'name' => 'Riding with Another Student', 'grade' => 11),
        array('required_permissions' => 'rideshare', 'name' => 'Car Service'),
        array('name' => 'Riding with Your Parent', 'form_warning' => 'You need face-to-face permission from one of your dorm faculty, or your parents need to have called the permissions line.'),
        array('name' => 'Town Shuttle'),
        array('name' => 'Weekend Activity', 'weekend_only' => 1),
        array('name' => 'Weekend Signout', 'boarding_status' => 'Boarder', 'weekend_only' => 1, 'form_warning' => 'You need to have an approved weekend form for this weekend on file.'),
        array('name' => 'Vacation Signout', 'boarding_status' => 'Boarder', 'enabled' => 0, 'form_warning' => 'You need to have a vacation travel form on file.')
    );
    foreach ($types as $type) {
        $DB->insert_record('local_signout_type', (object) $type);
    }
}
