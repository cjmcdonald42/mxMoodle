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
 * English language strings for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Middlesex School Electronic Signout';

/* General */
$string['confirmation_button_confirm'] = 'Confirm';
$string['confirmation_button_undo'] = 'Undo';
$string['sign_in_error_norecord'] = 'No active signout record exists for the current user.';
$string['sign_in_error_invalidrecord'] = 'The active record is invalid.';
$string['sign_in_error_invalidtype'] = 'The active record type is invalid.';

// Capabilities.
$string['signout:view_limited_signout_summary'] = 'Middlesex School Electronic Signout: View limited version of combined report without personal information or actions only for the user\'s dorm (proctors)';
$string['signout:manage_on_campus'] = 'Middlesex School Electronic Signout: View and manage student on-campus signout records';
$string['signout:manage_on_campus_preferences'] = 'Middlesex School Electronic Signout: View and manage on-campus signout preferences';
$string['signout:confirm_on_campus'] = 'Middlesex School Electronic Signout: View duty report and confirm on-campus signout records';
$string['signout:manage_off_campus'] = 'Middlesex School Electronic Signout: View and manage student off-campus signout records';
$string['signout:manage_off_campus_preferences'] = 'Middlesex School Electronic Signout: View and manage off-campus signout preferences';

// Settings Pages.
$string['settings'] = 'eSignout Settings';
$string['school_ip'] = 'Middlesex Network IP';
$string['school_ip_description'] = 'The IP to check against to confirm that someone is on campus.';

$string['signout_index'] = 'Signout Index';
$string['on_campus_index'] = 'On-Campus Signout Index';
$string['off_campus_index'] = 'Off-Campus Signout Index';

// Notifications.
$string['on_campus_preferences_edit_success'] = 'On-Campus Signout Preferences Saved Successfully';
$string['off_campus_preferences_edit_success'] = 'Off-Campus Signout Preferences Saved Successfully';
$string['off_campus_success'] = 'Off-Campus Signout Form Submitted Successfully';

$string['on_campus_location_create_success'] = 'On-Campus Location Record Created Successfully';
$string['on_campus_location_edit_success'] = 'On-Campus Location Record Updated Successfully';

$string['on_campus_success'] = 'On-Campus Signout Form Submitted Successfully';

$string['table_delete_failure'] = 'Table Not Found for Record Deletion';
$string['on_campus_location_delete_success'] = 'On-Campus Location Record Deleted Successfully';
$string['on_campus_location_delete_failure'] = 'On-Campus Location Record Not Found for Deletion';
$string['on_campus_delete_success'] = 'On-Campus Signout Record Deleted Successfully';
$string['on_campus_delete_failure'] = 'On-Campus Signout Record Not Found for Deletion';
$string['off_campus_delete_success'] = 'Off-Campus Signout Record Deleted Successfully';
$string['off_campus_delete_failure'] = 'Off-Campus Signout Record Not Found for Deletion';

/* Combined */
$string['signout'] = 'Combined Signout';
$string['combined_report'] = 'Dorm Signout Report';

$string['combined_report_title'] = '{$a}Live Signout Report';
$string['combined_report_select_date_all'] = 'All Dates';
$string['combined_report_header_student'] = 'Student';
$string['combined_report_header_grade'] = 'Grade';
$string['combined_report_header_dorm'] = 'Dorm';
$string['combined_report_header_status'] = 'Status';
$string['combined_report_header_location'] = 'Location';
$string['combined_report_header_signouttime'] = 'Sign Out Time';
$string['combined_report_status_on_campus'] = 'On Campus';
$string['combined_report_status_off_campus'] = 'Off Campus';

/* On Campus Signout */
$string['on_campus'] = 'On-Campus Signout';
$string['on_campus_preferences'] = 'On-Campus Signout Preferences';
$string['on_campus_location_report'] = 'Location Report';
$string['on_campus_location_edit'] = 'Edit Location Record';
$string['on_campus_form'] = 'On-Campus Signout Form';
$string['on_campus_report'] = 'On-Campus Signout Report';
$string['on_campus_duty_report'] = 'On-Campus Duty Report';

// Preferences for On-Campus Signout.
$string['on_campus_preferences_header_config'] = 'Config';
$string['on_campus_preferences_header_text'] = 'On-Campus Signout Form Permissions Warnings';
$string['on_campus_preferences_config_enabled_text'] = 'Check to Enable the On-Campus Signout Form';
$string['on_campus_preferences_config_ipenabled_text'] = 'Check to Enable IP Validation Against {$a->school} - Your Current IP is {$a->current}';
$string['on_campus_preferences_config_confirmationenabled_text'] = 'Check to Enable Confirmation';
$string['on_campus_preferences_config_refresh'] = 'How Often the On-Campus Reports Should Refresh (seconds)<br>Leave Blank to Disable Auto-refresh';
$string['on_campus_preferences_config_confirmationundo'] = 'How Long a Confirmer Should Have to Undo a Confirmation (seconds)<br>Leave Blank to Disable Undo Functionality';
$string['on_campus_preferences_text_ipformerror'] = 'Text to Display in On-Campus Signout Form When on the Wrong Network';
$string['on_campus_preferences_text_ipsigninerrorboarder'] = 'Text to Display When a Boarder Tries to Sign in When on the Wrong Network';
$string['on_campus_preferences_text_ipsigninerrorday'] = 'Text to Display When a Day Student Tries to Sign in When on the Wrong Network';
$string['on_campus_preferences_text_underclassmanwarning'] = 'Warning for a 9th or 10th Grade Student Who Selects an \'Other\' Location';
$string['on_campus_preferences_text_juniorwarning'] = 'Warning for an 11th Grade Student Who Selects an \'Other\' Location';
$string['on_campus_preferences_text_confirmation'] = 'Confirmation for a Student with a Warning';

// On-Campus Location Report.
$string['on_campus_location_report_add'] = 'New Location';
$string['on_campus_location_report_header_name'] = 'Name';
$string['on_campus_location_report_header_grade'] = 'Minimum Grade';
$string['on_campus_location_report_header_enabled'] = 'Enabled';
$string['on_campus_location_report_header_start'] = 'Start Date';
$string['on_campus_location_report_header_end'] = 'End Date';
$string['on_campus_location_report_header_warning'] = 'Warning';

// On-Campus Location Edit.
$string['on_campus_location_edit_header_location'] = 'Location Information';
$string['on_campus_location_edit_location_name'] = 'Name';
$string['on_campus_location_edit_location_grade'] = 'Minimum Grade';
$string['on_campus_location_edit_location_grade_9'] = '9';
$string['on_campus_location_edit_location_grade_10'] = '10';
$string['on_campus_location_edit_location_grade_11'] = '11';
$string['on_campus_location_edit_location_grade_12'] = '12';
$string['on_campus_location_edit_location_enabled'] = 'Enabled';
$string['on_campus_location_edit_location_start'] = 'Start Date';
$string['on_campus_location_edit_location_end'] = 'End Date';
$string['on_campus_location_edit_location_warning'] = 'Warning';

// Form for On-Campus Signout.
$string['on_campus_form_title'] = 'On-Campus Signout Form for {$a}';
$string['on_campus_form_header_info'] = 'General Information';
$string['on_campus_form_header_permissions'] = 'Permissions Check';
$string['on_campus_form_info_student'] = 'Student';
$string['on_campus_form_info_location'] = 'Sign-Out Location';
$string['on_campus_form_location_select_other'] = 'Other (please specify)';
$string['on_campus_form_error_nolocation'] = 'You must specify a location.';

// Report for On-Campus Signout.
$string['on_campus_report_title'] = '{$a}On-Campus Signout Report';
$string['on_campus_report_select_location_all'] = 'All Locations';
$string['on_campus_report_select_location_other'] = 'Other';
$string['on_campus_report_select_date_all'] = 'All Dates';
$string['on_campus_report_add'] = 'New On-Campus Signout Record';
$string['on_campus_report_header_student'] = 'Student';
$string['on_campus_report_header_grade'] = 'Grade';
$string['on_campus_report_header_dorm'] = 'Dorm';
$string['on_campus_report_header_location'] = 'Location';
$string['on_campus_report_header_signoutdate'] = 'Date';
$string['on_campus_report_header_signouttime'] = 'Sign Out Time';
$string['on_campus_report_header_confirmation'] = 'Confirmation';
$string['on_campus_report_header_signin'] = 'Sign In Time';
$string['on_campus_report_column_confirmation_text'] = 'Confirmed by {$a->confirmer} at {$a->confirmationtime}';

// Duty Report for On-Campus Signout.
$string['duty_report_title'] = 'On-Campus Duty Report for {$a}';
$string['duty_report_select_active_true'] = 'Currently Signed Out';
$string['duty_report_select_active_false'] = 'All Students';
$string['duty_report_select_pictures_on'] = 'Show Pictures';
$string['duty_report_select_pictures_off'] = 'Hide Pictures';
$string['duty_report_select_location_all'] = 'All Locations';
$string['duty_report_select_location_other'] = 'Other';
$string['duty_report_header_student'] = 'Student';
$string['duty_report_header_picture'] = 'Picture';
$string['duty_report_header_grade'] = 'Grade';
$string['duty_report_header_dorm'] = 'Dorm';
$string['duty_report_header_advisor'] = 'Advisor';
$string['duty_report_header_location'] = 'Location';
$string['duty_report_header_signouttime'] = 'Sign Out Time';
$string['duty_report_header_confirmation'] = 'Confirmation';
$string['duty_report_header_signin'] = 'Sign In Time';
$string['duty_report_column_picture_notfound'] = '<i>&mdash;Picture Not Found&mdash;</i>';
$string['duty_report_column_confirmation_text'] = 'Confirmed by {$a->confirmer} at {$a->confirmationtime}';


/* Off-Campus Signout */
$string['off_campus'] = 'Off-Campus Signout';
$string['off_campus_preferences'] = 'Off-Campus Signout Preferences';
$string['off_campus_form'] = 'Off-Campus Signout Form';
$string['off_campus_report'] = 'Off-Campus Signout Report';

// Off-Campus Submitted Notification.
$string['off_campus_notification_warning_other'] = '(passenger) {$a->passengerwarning} (rideshare) {$a->ridesharewarning}';

// Preferences for Off-Campus Signout.
$string['off_campus_preferences_header_config'] = 'Config';
$string['off_campus_preferences_header_notifications'] = 'Off-Campus Signout Email Notifications';
$string['off_campus_preferences_header_text'] = 'Off-Campus Signout Form Permissions Warnings';
$string['off_campus_preferences_header_emailtext'] = 'Off-Campus Signout Email Permissions Warnings';
$string['off_campus_preferences_config_editwindow'] = 'Window for Students to Edit Off-Campus Signout Forms (minutes)';
$string['off_campus_preferences_config_tripwindow'] = 'Window for a Driver to be Available for Selection (minutes)';
$string['off_campus_preferences_config_enabled_text'] = 'Check to Enable the Off-Campus Signout Form';
$string['off_campus_preferences_config_permissions_active_text'] = 'Check to Enable the Portions of the Off-Campus Signout Form Which Require Student Permissions';
$string['off_campus_preferences_config_ipenabled_text'] = 'Check to Enable IP Validation Against {$a->school} - Your Current IP is {$a->current}';
$string['off_campus_preferences_notifications_tags'] = 'Available Tags';
$string['off_campus_preferences_notifications_subject'] = 'Subject for Off-Campus Signout Form Submitted Email';
$string['off_campus_preferences_notifications_body'] = 'Body for Off-Campus Signout Form Submitted Email';
$string['off_campus_preferences_text_ipformerror'] = 'Text to Display in Off-Campus Signout Form When on the Wrong Network';
$string['off_campus_preferences_text_ipsigninerror'] = 'Text to Display When a Student Tries to Sign in When on the Wrong Network';
$string['off_campus_preferences_text_passengerinstructions'] = 'Instructions Regarding Selecting a Driver';
$string['off_campus_preferences_text_bottominstructions'] = 'Instructions at the Bottom of the Off-Campus Signout Form';
$string['off_campus_preferences_text_nopassengers'] = 'Warning for a Student Who May Not Drive Passengers';
$string['off_campus_preferences_text_needparent'] = 'Warning for a Student Who May Only be a Passenger with Parent Permission';
$string['off_campus_preferences_text_onlyspecific'] = 'Warning for a Student Who May Only be the Passenger of Specific Drivers';
$string['off_campus_preferences_text_confirmation'] = 'Confirmation for a Passenger a Warning';
$string['off_campus_preferences_emailtext_irregular'] = 'Indicator for an Irregular Signout';
$string['off_campus_preferences_emailtext_driveryespassengers'] = 'Warning for a Driver Who is Allowed to Drive Other Students';
$string['off_campus_preferences_emailtext_drivernopassengers'] = 'Warning for a Driver Who is not Allowed to Drive Other Students';
$string['off_campus_preferences_emailtext_passengerany'] = 'Warning for a Passenger with Permissions to Ride with Any Driver';
$string['off_campus_preferences_emailtext_passengerparent'] = 'Warning for a Passenger with Permissions to Ride Only with Parent Permission';
$string['off_campus_preferences_emailtext_passengerspecific'] = 'Warning for a Passenger with Permissions to Ride Only with Specific Drivers';
$string['off_campus_preferences_emailtext_passengerover21'] = 'Warning for a Passenger with Permissions to Ride Only with Drivers Over 21';
$string['off_campus_preferences_emailtext_parent'] = 'Warning for a Student Riding with their Parent';
$string['off_campus_preferences_emailtext_rideshareyes'] = 'Warning for a Student Who is Allowed to Use Rideshare';
$string['off_campus_preferences_emailtext_rideshareno'] = 'Warning for a Student Who is not Allowed to Use Rideshare';


// Form for Off-Campus Signout.
$string['off_campus_form_title'] = 'Off-Campus Signout Form for {$a}';
$string['off_campus_form_header_info'] = 'General Information';
$string['off_campus_form_header_details'] = 'Details';
$string['off_campus_form_header_permissions'] = 'Permissions Check';
$string['off_campus_form_info_student'] = 'Student';
$string['off_campus_form_info_type'] = 'Driver Type';
$string['off_campus_form_info_type_select_Driver'] = 'Yourself';
$string['off_campus_form_info_type_select_Passenger'] = 'Another Student';
$string['off_campus_form_info_type_select_Parent'] = 'Your Parent';
$string['off_campus_form_info_type_select_Rideshare'] = 'Rideshare';
$string['off_campus_form_info_type_select_Other'] = 'Other Adult (Please Specify)';
$string['off_campus_form_info_passengers'] = 'Your Passengers';
$string['off_campus_form_info_driver'] = 'Your Driver';
$string['off_campus_form_details_destination'] = 'Your Destination';
$string['off_campus_form_details_departure_time'] = 'Departure Time';
$string['off_campus_form_details_approver'] = 'Face-to-Face Permission Granted by';
$string['off_campus_form_passengers_noselection'] = 'No Passengers Selected';
$string['off_campus_form_passengers_placeholder'] = 'Search Passengers';
$string['off_campus_form_error_notype'] = 'You must specify a driver type.';
$string['off_campus_form_error_nodriver'] = 'You must specify a driver.';
$string['off_campus_form_error_nodestination'] = 'You must specify a destination.';
$string['off_campus_form_error_noapprover'] = 'You must specify who approved your signout.';

// Report for Off-Campus Signout.
$string['off_campus_report_select_type_all'] = 'All Types';
$string['off_campus_report_select_type_driver'] = 'Driver';
$string['off_campus_report_select_type_passenger'] = 'Passenger';
$string['off_campus_report_select_type_parent'] = 'Parent';
$string['off_campus_report_select_type_rideshare'] = 'Rideshare';
$string['off_campus_report_select_type_other'] = 'Other';
$string['off_campus_report_select_date_all'] = 'All Dates';
$string['off_campus_report_add'] = 'New Off-Campus Signout Record';
$string['off_campus_report_header_student'] = 'Student';
$string['off_campus_report_header_grade'] = 'Grade';
$string['off_campus_report_header_type'] = 'Type';
$string['off_campus_report_header_driver'] = 'Driver';
$string['off_campus_report_header_passengers'] = 'Passengers';
$string['off_campus_report_header_passengercount'] = 'Passengers Submitted';
$string['off_campus_report_header_destination'] = 'Destination';
$string['off_campus_report_header_departuredate'] = 'Date';
$string['off_campus_report_header_departuretime'] = 'Departure Time';
$string['off_campus_report_header_approver'] = 'Permission From';
$string['off_campus_report_header_signin'] = 'Sign In Time';
$string['off_campus_report_nopassengers'] = 'None';
