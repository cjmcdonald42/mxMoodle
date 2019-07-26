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
 * Default config values for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/* On-Campus Signout. */
$defaults['local_signout']['on_campus_form_enabled'] = '1';
$defaults['local_signout']['on_campus_form_ipenabled'] = '1';
$defaults['local_signout']['on_campus_form_iperror'] = 'You must be on Middlesex\'s network to access this form.';
$defaults['local_signout']['on_campus_signin_iperror_boarder'] = 'You must be on Middlesex\'s network to sign back in to your dorm.';
$defaults['local_signout']['on_campus_signin_iperror_day'] = 'You must be on Middlesex\'s network to be going home.';
$defaults['local_signout']['on_campus_form_warning_underclassmen'] = 'You need special permission to go to any \'other\' location.';
$defaults['local_signout']['on_campus_form_warning_juniors'] = 'You need special permission to go to a non-academic location.';
$defaults['local_signout']['on_campus_form_confirmation'] = 'Have you received the required permissions?';
$defaults['local_signout']['on_campus_refresh_rate'] = '60';

/* Off-Campus Signout. */
$defaults['local_signout']['off_campus_edit_window'] = '30';
$defaults['local_signout']['off_campus_trip_window'] = '30';
$defaults['local_signout']['off_campus_form_enabled'] = '1';
$defaults['local_signout']['off_campus_form_ipenabled'] = '1';
$defaults['local_signout']['off_campus_form_iperror'] = 'You must be on Middlesex\'s network to access this form.';
$defaults['local_signout']['off_campus_signin_iperror'] = 'You must be on Middlesex\'s network to sign in.';
$defaults['local_signout']['off_campus_form_instructions_passenger'] = 'Your driver must have submitted a form to be in the list below.';
$defaults['local_signout']['off_campus_form_instructions_bottom'] = 'You will have {minutes} minutes to edit your form once you have submitted it.';
$defaults['local_signout']['off_campus_form_warning_nopassengers'] = 'Your permissions indicate that you may not drive passengers.';
$defaults['local_signout']['off_campus_form_warning_needparent'] = 'Your permissions indicate that you need a call from your parent.';
$defaults['local_signout']['off_campus_form_warning_onlyspecific'] = 'Your permissions indicate that you may only be the passenger of the following drivers: ';
$defaults['local_signout']['off_campus_form_confirmation'] = 'Have you received the required permissions?';
$defaults['local_signout']['off_campus_notification_warning_irregular'] = '[Irregular] ';
$defaults['local_signout']['off_campus_notification_warning_driver'] = 'None.';
$defaults['local_signout']['off_campus_notification_warning_any'] = 'None.';
$defaults['local_signout']['off_campus_notification_warning_parent'] = 'This student requires parent permission to be the passenger of another student.';
$defaults['local_signout']['off_campus_notification_warning_specific'] = 'This student only has permission to the be the passenger of the following drivers: ';
$defaults['local_signout']['off_campus_notification_warning_over21'] = 'This student does NOT have permission to be the passenger of anyone under 21.';
$defaults['local_signout']['off_campus_notification_warning_unsetpermissions'] = 'This student does NOT have passenger permissions on file.';
