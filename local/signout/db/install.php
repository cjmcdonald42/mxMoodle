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
    set_config('off_campus_form_ipenabled', '1', 'local_signout');
    set_config('off_campus_form_iperror', 'You must be on Middlesex\'s network to access this form.', 'local_signout');
    set_config('off_campus_signin_iperror', 'You must be on Middlesex\'s network to sign in.', 'local_signout');
    set_config('off_campus_form_instructions_passenger', 'Your driver must have submitted a form to be in the list below.', 'local_signout');
    set_config('off_campus_form_instructions_bottom', 'You will have {minutes} minutes to edit your form once you have submitted it.', 'local_signout');
    set_config('off_campus_form_warning_nopassengers', 'Your permissions indicate that you may not drive passengers.', 'local_signout');
    set_config('off_campus_form_warning_needparent', 'Your permissions indicate that you need a call from your parent.', 'local_signout');
    set_config('off_campus_form_warning_onlyspecific', 'Your permissions indicate that you may only be the passenger of the following drivers: ', 'local_signout');
    set_config('off_campus_form_confirmation', 'Have you received the required permissions?', 'local_signout');
    set_config('off_campus_notification_warning_irregular', '[Irregular] ', 'local_signout');
    set_config('off_campus_notification_warning_driver_passengers', 'None.', 'local_signout');
    set_config('off_campus_notification_warning_driver_yespassengers', 'This student does NOT have permission to drive other students.', 'local_signout');
    set_config('off_campus_notification_warning_passenger_any', 'None.', 'local_signout');
    set_config('off_campus_notification_warning_passenger_parent', 'This student requires parent permission to be the passenger of another student.', 'local_signout');
    set_config('off_campus_notification_warning_passenger_specific', 'This student only has permission to the be the passenger of the following drivers: ', 'local_signout');
    set_config('off_campus_notification_warning_passenger_over21', 'This student does NOT have permission to be the passenger of anyone under 21.', 'local_signout');
    set_config('off_campus_notification_warning_parent', 'None.', 'local_signout');
    set_config('off_campus_notification_warning_rideshare_yes', 'None.', 'local_signout');
    set_config('off_campus_notification_warning_rideshare_no', 'This student does NOT have permission to use rideshare.', 'local_signout');

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
        array('name' => 'Health Center', 'grade' => 9),
        array('name' => 'Supervised Study Hall', 'grade' => 9),
        array('name' => 'Play Rehearsal / Tech', 'grade' => 9),
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
}
