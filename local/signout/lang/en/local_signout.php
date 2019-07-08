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
 * English language strings for Middlesex School's eSignout Subplugin.
 *
 * @package    local_signout
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Middlesex School Electronic Signout';

/* General */
$string['sign_in_button'] = 'Sign In';

// Capabilities.
$string['signout:manage_off_campus'] = 'Middlesex School Electronic Signout: View and manage student off-campus signout records';
$string['signout:manage_off_campus_preferences'] = 'Middlesex School Electronic Signout: View and manage off-campus signout preferences';
$string['signout:manage_on_campus'] = 'Middlesex School Electronic Signout: View and manage student on-campus signout records';
$string['signout:manage_on_campus_preferences'] = 'Middlesex School Electronic Signout: View and manage on-campus signout preferences';

// Settings Pages.
$string['settings'] = 'eSignout Settings';
$string['school_ip'] = 'Middlesex Network IP';
$string['school_ip_description'] = 'The IP to check against to confirm that someone is on campus.';

$string['signout_index'] = 'Signout Index';
$string['off_campus_index'] = 'Off-Campus Signout Index';
$string['on_campus_index'] = 'On-Campus Signout Index';

// Notifications.
$string['off_campus_preferences_edit_success'] = 'Off-Campus Signout Preferences Saved Successfully';
$string['off_campus_success'] = 'Off-Campus Signout Form Submitted Successfully';
$string['on_campus_preferences_edit_success'] = 'On-Campus Signout Preferences Saved Successfully';

$string['on_campus_location_create_success'] = 'On-Campus Location Record Created Successfully';
$string['on_campus_location_edit_success'] = 'On-Campus Location Record Updated Successfully';

$string['on_campus_success'] = 'On-Campus Signout Form Submitted Successfully';

$string['off_campus_delete_success'] = 'Off-Campus Signout Record Deleted Successfully';
$string['off_campus_delete_failure'] = 'Off-Campus Signout Record Not Found for Deletion';
$string['on_campus_location_delete_success'] = 'On-Campus Location Record Deleted Successfully';
$string['on_campus_location_delete_failure'] = 'On-Campus Location Record Not Found for Deletion';
$string['on_campus_delete_success'] = 'On-Campus Signout Record Deleted Successfully';
$string['on_campus_delete_failure'] = 'On-Campus Signout Record Not Found for Deletion';

/* Off-Campus Signout */
$string['off_campus'] = 'Off-Campus Signout';
$string['off_campus_preferences'] = 'Off-Campus Signout Preferences';
$string['off_campus_form'] = 'Off-Campus Signout Form';
$string['off_campus_report'] = 'Off-Campus Signout Report';

// Preferences for Off-Campus Signout.
$string['off_campus_preferences_header_config'] = 'Config';
$string['off_campus_preferences_header_notifications'] = 'Off-Campus Signout Email Notifications';
$string['off_campus_preferences_header_text'] = 'Off-Campus Signout Form Permissions Warnings';
$string['off_campus_preferences_header_emailtext'] = 'Off-Campus Signout Email Permissions Warnings';
$string['off_campus_preferences_config_editwindow'] = 'Window for Students to Edit Off-Campus Signout Forms (minutes)';
$string['off_campus_preferences_config_tripwindow'] = 'Window for a Driver to be Available for Selection (minutes)';
$string['off_campus_preferences_config_offcampusenabled_text'] = 'Check to Enable the Off-Campus Signout Form';
$string['off_campus_preferences_config_ipenabled_text'] = 'Check to Enable IP Validation Against {$a->school} - Your Current IP is {$a->current}';
$string['off_campus_preferences_notifications_tags'] = 'Available Tags';
$string['off_campus_preferences_notifications_subject'] = 'Subject for Off-Campus Signout Form Submitted Email';
$string['off_campus_preferences_notifications_body'] = 'Body for Off-Campus Signout Form Submitted Email';
$string['off_campus_preferences_text_ipformerror'] = 'Text to Display in Off-Campus Signout Form When on the Wrong Network';
$string['off_campus_preferences_text_ipreporterror'] = 'Text to Display in Off-Campus Signout Report When on the Wrong Network';
$string['off_campus_preferences_text_passengerinstructions'] = 'Instructions Regarding Selecting a Driver';
$string['off_campus_preferences_text_bottominstructions'] = 'Instructions at the Bottom of the Off-Campus Signout Form';
$string['off_campus_preferences_text_nopassengers'] = 'Warning for a Student Who May Not Drive Passengers';
$string['off_campus_preferences_text_needparent'] = 'Warning for a Student Who May Only be a Passenger with Parent Permission';
$string['off_campus_preferences_text_onlyspecific'] = 'Warning for a Student Who May Only be the Passenger of Specific Drivers';
$string['off_campus_preferences_text_confirmation'] = 'Confirmation for a Passenger with Warnings';
$string['off_campus_preferences_emailtext_irregular'] = 'Indicator for an Irregular Signout';
$string['off_campus_preferences_emailtext_driver'] = 'Warning for a Driver';
$string['off_campus_preferences_emailtext_any'] = 'Warning for a Passenger with Permissions to Ride with Any Driver';
$string['off_campus_preferences_emailtext_parent'] = 'Warning for a Passenger with Permissions to Ride Only with Parent Permission';
$string['off_campus_preferences_emailtext_specific'] = 'Warning for a Passenger with Permissions to Ride Only with Specific Drivers';
$string['off_campus_preferences_emailtext_over21'] = 'Warning for a Passenger with Permissions to Ride Only with Drivers Over 21';

// Form for Off-Campus Signout.
$string['off_campus_form_header_info'] = 'General Information';
$string['off_campus_form_header_details'] = 'Details';
$string['off_campus_form_header_permissions'] = 'Permissions Check';
$string['off_campus_form_info_student'] = 'Student';
$string['off_campus_form_info_type'] = 'Driver Type';
$string['off_campus_form_info_type_select_Driver'] = 'Yourself';
$string['off_campus_form_info_type_select_Passenger'] = 'Another Student';
$string['off_campus_form_info_type_select_Parent'] = 'Your Parent';
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
$string['off_campus_report_select_type_other'] = 'Other';
$string['off_campus_report_select_date_all'] = 'All Dates';
$string['off_campus_report_add'] = 'New Off-Campus Signout Record';
$string['off_campus_report_header_student'] = 'Student';
$string['off_campus_report_header_type'] = 'Type';
$string['off_campus_report_header_driver'] = 'Driver';
$string['off_campus_report_header_passengers'] = 'Passengers';
$string['off_campus_report_header_passengercount'] = 'Passengers Submitted';
$string['off_campus_report_header_destination'] = 'Destination';
$string['off_campus_report_header_date'] = 'Date';
$string['off_campus_report_header_departure'] = 'Departure Time';
$string['off_campus_report_header_approver'] = 'Permission From';
$string['off_campus_report_header_signin'] = 'Sign In Time';
$string['off_campus_report_nopassengers'] = 'None';


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
$string['on_campus_preferences_config_oncampusenabled_text'] = 'Check to Enable the On-Campus Signout Form';
$string['on_campus_preferences_config_ipenabled_text'] = 'Check to Enable IP Validation Against {$a->school} - Your Current IP is {$a->current}';
$string['on_campus_preferences_text_ipformerror'] = 'Text to Display in On-Campus Signout Form When on the Wrong Network';
$string['on_campus_preferences_text_ipreporterror'] = 'Text to Display in On-Campus Signout Report When on the Wrong Network';

// On-Campus Location Report.
$string['on_campus_location_report_add'] = 'New Location';
$string['on_campus_location_report_header_name'] = 'Name';
$string['on_campus_location_report_header_grade'] = 'Minimum Grade';
$string['on_campus_location_report_header_enabled'] = 'Enabled';
$string['on_campus_location_report_header_start'] = 'Start Date';
$string['on_campus_location_report_header_stop'] = 'Stop Date';

// On-Campus Location Edit.
$string['on_campus_location_edit_header_location'] = 'Location Information';
$string['on_campus_location_edit_location_name'] = 'Name';
$string['on_campus_location_edit_location_grade'] = 'Minimum Grade';
$string['on_campus_location_edit_location_grade_11'] = '11';
$string['on_campus_location_edit_location_grade_12'] = '12';
$string['on_campus_location_edit_location_enabled'] = 'Enabled';
$string['on_campus_location_edit_location_start'] = 'Start Date';
$string['on_campus_location_edit_location_stop'] = 'Stop Date';

// Form for On-Campus Signout.
$string['on_campus_form_header_info'] = 'General Information';
$string['on_campus_form_info_student'] = 'Student';
$string['on_campus_form_info_location'] = 'Sign-Out Location';
$string['on_campus_form_location_select_other'] = 'Other (please specify)';
$string['on_campus_form_error_nolocation'] = 'You must specify a location.';

// Report for On-Campus Signout.


// Duty Report for On-Campus Signout.
