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

/*
 * ========
 * General.
 * ========
 */
$string['pluginname'] = 'Middlesex Electronic Signout';

/* Capabilities. */
$string['signout:view_limited_signout_summary'] = 'Middlesex School Electronic Signout: View limited version of combined report without personal information or actions only for the user\'s dorm (proctors)';
$string['signout:manage_on_campus'] = 'Middlesex School Electronic Signout: View and manage student on-campus signout records';
$string['signout:manage_on_campus_preferences'] = 'Middlesex School Electronic Signout: View and manage on-campus signout preferences';
$string['signout:confirm_on_campus'] = 'Middlesex School Electronic Signout: View duty report and confirm on-campus signout records';
$string['signout:manage_off_campus'] = 'Middlesex School Electronic Signout: View and manage student off-campus signout records';
$string['signout:manage_off_campus_preferences'] = 'Middlesex School Electronic Signout: View and manage off-campus signout preferences';


/* Renderables. */

// Confirmation button.
$string['confirmation_button:confirm'] = 'Confirm';
$string['confirmation_button:undo'] = 'Undo';

// Sign-in button.
$string['sign_in_button:error:norecord'] = 'No active signout record exists for the current user.';
$string['sign_in_button:error:invalidrecord'] = 'The active record is invalid.';
$string['sign_in_button:error:invalidtype'] = 'The active record type is invalid.';


/* Settings. */
$string['signout_settings'] = 'eSignout Settings';
$string['signout_settings:school_ip'] = 'Middlesex network IP address';
$string['signout_settings:school_ip:description'] = 'The IP to check against to confirm that someone is on campus.';

// Index Pages.
$string['indexes:signout'] = 'Signout Index';
$string['indexes:on_campus'] = 'On-Campus Signout Index';
$string['indexes:off_campus'] = 'Off-Campus Signout Index';



/*
 * =================
 * Combined Signout.
 * =================
 */
$string['signout'] = 'Combined Signout';


/* Combined Report. */
$string['combined_report'] = 'Dorm Signout Report';
$string['combined_report:title'] = '{$a}Live Signout Report';

// Headers.
$string['combined_report:header:student'] = 'Student';
$string['combined_report:header:grade'] = 'Grade';
$string['combined_report:header:dorm'] = 'Dorm';
$string['combined_report:header:status'] = 'Status';
$string['combined_report:header:location'] = 'Location';
$string['combined_report:header:signouttime'] = 'Sign Out Time';

// Cells.
$string['combined_report:cell:status:on_campus'] = 'On Campus';
$string['combined_report:cell:status:off_campus'] = 'Off Campus';



/*
 * ==================
 * On-Campus Signout.
 * ==================
 */
$string['on_campus'] = 'On-Campus Signout';


/* On-Campus Signout Preferences. */
$string['on_campus:preferences'] = 'On-Campus Signout Preferences';

// Config.
$string['on_campus:preferences:config'] = 'Config';
$string['on_campus:preferences:config:enabled:text'] = 'Check to enable the on-campus signout form';
$string['on_campus:preferences:config:ip_enabled:text'] = 'Check to enable IP validation against {$a->school} for the on-campus subsystem — your current IP is {$a->current}';
$string['on_campus:preferences:config:confirmation_enabled:text'] = 'Check to enable confirmation functionality';
$string['on_campus:preferences:config:refresh'] = 'How Often the On-Campus Reports Should Refresh (seconds)<br>Leave Blank to Disable Auto-refresh';
$string['on_campus:preferences:config:confirmation_undo'] = 'How Long a Confirmer Should Have to Undo a Confirmation (seconds)<br>Leave Blank to Disable Undo Functionality';

// Permissions Warnings.
$string['on_campus:preferences:text'] = 'Permissions Warnings';
$string['on_campus:preferences:text:ip_form_error'] = 'Text to Display in On-Campus Signout Form When on the Wrong Network';
$string['on_campus:preferences:text:ip_sign_in_error_boarder'] = 'Text to Display When a Boarder Tries to Sign in When on the Wrong Network';
$string['on_campus:preferences:text:ip_sign_in_error_day'] = 'Text to Display When a Day Student Tries to Sign in When on the Wrong Network';
$string['on_campus:preferences:text:underclassman_warning'] = 'Warning in On-Campus Signout Form for a 9th or 10th Grade Student Who Selects an \'Other\' Location';
$string['on_campus:preferences:text:junior_warning'] = 'Warning in On-Campus Signout Form for an 11th Grade Student Who Selects an \'Other\' Location';
$string['on_campus:preferences:text:confirmation'] = 'Confirmation for a Student with a Warning in On-Campus Signout Form';

// Notification.
$string['on_campus:preferences:update:success'] = 'On-Campus Signout Preferences Saved Successfully';


/* On-Campus Location Report. */
$string['on_campus:location_report'] = 'On-Campus Location Report';

// Filter.
$string['on_campus:location_report:add'] = 'New Location';

// Headers.
$string['on_campus:location_report:header:name'] = 'Name';
$string['on_campus:location_report:header:grade'] = 'Minimum Grade';
$string['on_campus:location_report:header:allday'] = 'Available for All Day Students';
$string['on_campus:location_report:header:enabled'] = 'Enabled';
$string['on_campus:location_report:header:start'] = 'Start Date';
$string['on_campus:location_report:header:end'] = 'End Date';
$string['on_campus:location_report:header:warning'] = 'Warning';


/* On-Campus Location Edit. */
$string['on_campus:location_edit'] = 'Edit On-Campus Location Record';

// Location Information.
$string['on_campus:location_edit:location'] = 'Location Information';
$string['on_campus:location_edit:location:name'] = 'Name';
$string['on_campus:location_edit:location:grade'] = 'Minimum Grade';
$string['on_campus:location_edit:location:grade:9'] = '9';
$string['on_campus:location_edit:location:grade:10'] = '10';
$string['on_campus:location_edit:location:grade:11'] = '11';
$string['on_campus:location_edit:location:grade:12'] = '12';
$string['on_campus:location_edit:location:all_day'] = 'Available for All Day Students';
$string['on_campus:location_edit:location:enabled'] = 'Enabled';
$string['on_campus:location_edit:location:start'] = 'Start Date';
$string['on_campus:location_edit:location:end'] = 'End Date';
$string['on_campus:location_edit:location:warning'] = 'Warning';


/* On-Campus Location Notifications. */
$string['on_campus:location:create:success'] = 'On-Campus Location Record Created Successfully';
$string['on_campus:location:update:success'] = 'On-Campus Location Record Updated Successfully';
$string['on_campus:location:delete:success'] = 'On-Campus Location Record Deleted Successfully';
$string['on_campus:location:delete:failure'] = 'On-Campus Location Record Not Found for Deletion';


/* On-Campus Signout Form. */
$string['on_campus:form'] = 'On-Campus Signout Form';
$string['on_campus:form:title'] = 'On-Campus Signout Form for {$a}';

$string['on_campus:form:info'] = 'General Information';
$string['on_campus:form:info:student'] = 'Student';
$string['on_campus:form:info:location'] = 'Sign-Out Location';
$string['on_campus:form:info:location_select:other'] = 'Other (please specify)';

// Permissions Check.
$string['on_campus:form:permissions'] = 'Permissions Check';

// Errors.
$string['on_campus:form:error:no_location'] = 'You must specify a location.';

// Notifications.
$string['on_campus:form:success'] = 'On-Campus Signout Form Submitted Successfully';
$string['on_campus:form:delete:success'] = 'On-Campus Signout Record Deleted Successfully';
$string['on_campus:form:delete:failure'] = 'On-Campus Signout Record Not Found for Deletion';


/* On-Campus Signout Report. */
$string['on_campus:report'] = 'On-Campus Signout Report';
$string['on_campus:report:title'] = '{$a}On-Campus Signout Report';

// Filter.
$string['on_campus:report:select_location:all'] = 'All Locations';
$string['on_campus:report:select_location:other'] = 'Other';
$string['on_campus:report:select_date:all'] = 'All Dates';
$string['on_campus:report:add'] = 'New On-Campus Signout Record';

// Headers.
$string['on_campus:report:header:student'] = 'Student';
$string['on_campus:report:header:grade'] = 'Grade';
$string['on_campus:report:header:dorm'] = 'Dorm';
$string['on_campus:report:header:location'] = 'Location';
$string['on_campus:report:header:signoutdate'] = 'Date';
$string['on_campus:report:header:signouttime'] = 'Sign Out Time';
$string['on_campus:report:header:confirmation'] = 'Confirmation';
$string['on_campus:report:header:signin'] = 'Sign In Time';

// Cells.
$string['on_campus:report:cell:confirmation'] = 'Confirmed by {$a->confirmer} at {$a->confirmationtime}';


/* On-Campus Signout Duty Report. */
$string['on_campus:duty_report'] = 'On-Campus Duty Report';
$string['on_campus:duty_report:title'] = 'On-Campus Duty Report for {$a}';

// Filter.
$string['on_campus:duty_report:select_active:true'] = 'Currently Signed Out';
$string['on_campus:duty_report:select_active:false'] = 'All Students';
$string['on_campus:duty_report:select_pictures:on'] = 'Show Pictures';
$string['on_campus:duty_report:select_pictures:off'] = 'Hide Pictures';
$string['on_campus:duty_report:select_location:all'] = 'All Locations';
$string['on_campus:duty_report:select_location:other'] = 'Other';

// Headers.
$string['on_campus:duty_report:header:student'] = 'Student';
$string['on_campus:duty_report:header:picture'] = 'Picture';
$string['on_campus:duty_report:header:grade'] = 'Grade';
$string['on_campus:duty_report:header:dorm'] = 'Dorm';
$string['on_campus:duty_report:header:advisor'] = 'Advisor';
$string['on_campus:duty_report:header:location'] = 'Location';
$string['on_campus:duty_report:header:signouttime'] = 'Sign Out Time';
$string['on_campus:duty_report:header:confirmation'] = 'Confirmation';
$string['on_campus:duty_report:header:signin'] = 'Sign In Time';

// Cells.
$string['on_campus:duty_report:cell:picture:not_found'] = '<i>&mdash;Picture Not Found&mdash;</i>';
$string['on_campus:duty_report:cell:confirmation'] = 'Confirmed by {$a->confirmer} at {$a->confirmationtime}';



/*
 * ===================
 * Off-Campus Signout.
 * ===================
 */
$string['off_campus'] = 'Off-Campus Signout';


/* Off-Campus Signout Email Notification. */
$string['off_campus:notification:warning:default'] = 'None.';
$string['off_campus:notification:warning:other'] = '(passenger) {$a->passengerwarning} (car service) {$a->ridesharewarning}';


/* Off-Campus Signout Preferences. */
$string['off_campus:preferences'] = 'Off-Campus Signout Preferences';

// Config.
$string['off_campus:preferences:config'] = 'Config';
$string['off_campus:preferences:config:edit_window'] = 'Window for Students to Edit Off-Campus Signout Forms (minutes)';
$string['off_campus:preferences:config:trip_window'] = 'Window for a Driver to be Available for Selection (minutes)';
$string['off_campus:preferences:config:enabled:text'] = 'Check to enable the off-campus signout form';
$string['off_campus:preferences:config:permissions_active:text'] = 'Check to enable all off-campus signout types which require student permissions';
$string['off_campus:preferences:config:ip_enabled:text'] = 'Check to enable IP validation against {$a->school} for the off-campus subsystem — your current IP is {$a->current}';

// Off-Campus Signout Email Notifications.
$string['off_campus:preferences:notifications'] = 'Off-Campus Signout Email Notifications';
$string['off_campus:preferences:notifications:tags'] = 'Available Tags';
$string['off_campus:preferences:notifications:subject'] = 'Subject for Off-Campus Signout Form Submitted Email';
$string['off_campus:preferences:notifications:body'] = 'Body for Off-Campus Signout Form Submitted Email';

// Off-Campus Signout Form Instructions and Permissions Warnings.
$string['off_campus:preferences:formtext'] = 'Off-Campus Signout Form Instructions and Permissions Warnings';
$string['off_campus:preferences:formtext:ip_form_error'] = 'Text to Display in Off-Campus Signout Form When on the Wrong Network';
$string['off_campus:preferences:formtext:ip_sign_in_error'] = 'Text to Display When a Student Tries to Sign in When on the Wrong Network';
$string['off_campus:preferences:formtext:passenger_instructions'] = 'Instructions Regarding Selecting a Driver';
$string['off_campus:preferences:formtext:bottom_instructions'] = 'Instructions at the Bottom of the Off-Campus Signout Form';
$string['off_campus:preferences:formtext:confirmation'] = 'Confirmation for a Passenger a Warning';
$string['off_campus:preferences:formtext:form_driver_no_passengers'] = 'Warning for a Student Who May Not Drive Passengers';
$string['off_campus:preferences:formtext:form_passenger_parent'] = 'Warning for a Passenger with Permissions to Ride Only with Parent Permission';
$string['off_campus:preferences:formtext:form_passenger_specific'] = 'Warning for a Passenger with Permissions to Ride Only with Specific Drivers';
$string['off_campus:preferences:formtext:form_passenger_over_21'] = 'Warning for a Passenger with Permissions to Ride Only with Drivers Over 21';
$string['off_campus:preferences:formtext:form_rideshare_parent'] = 'Warning for a Student with Permissions to Use a Car Service Only with Parent Permission';
$string['off_campus:preferences:formtext:form_rideshare_not_allowed'] = 'Warning for a Student without Permissions to Use a Car Service';

// Off-Campus Signout Email Permissions Warnings.
$string['off_campus:preferences:emailtext'] = 'Off-Campus Signout Email Permissions Warnings';
$string['off_campus:preferences:emailtext:email_driver_no_passengers'] = 'Warning for a Student Who May Not Drive Passengers';
$string['off_campus:preferences:emailtext:email_passenger_parent'] = 'Warning for a Passenger with Permissions to Ride Only with Parent Permission';
$string['off_campus:preferences:emailtext:email_passenger_specific'] = 'Warning for a Passenger with Permissions to Ride Only with Specific Drivers';
$string['off_campus:preferences:emailtext:email_passenger_over_21'] = 'Warning for a Passenger with Permissions to Ride Only with Drivers Over 21';
$string['off_campus:preferences:emailtext:email_rideshare_parent'] = 'Warning for a Student with Permissions to Use a Car Service Only with Parent Permission';
$string['off_campus:preferences:emailtext:email_rideshare_not_allowed'] = 'Warning for a Student without Permissions to Use a Car Service';
$string['off_campus:preferences:emailtext:irregular'] = 'Indicator for an Irregular Signout';
$string['off_campus:preferences:update:success'] = 'Off-Campus Signout Preferences Saved Successfully';


/* Off-Campus Signout Type Report. */
$string['off_campus:type_report'] = 'Off-Campus Signout Type Report';

// Filter.
$string['off_campus:type_report:add'] = 'New Type';

// Headers.
$string['off_campus:type_report:header:name'] = 'Name';
$string['off_campus:type_report:header:permissions'] = 'Permissions';
$string['off_campus:type_report:header:grade'] = 'Minimum Grade';
$string['off_campus:type_report:header:boardingstatus'] = 'Boarding Status';
$string['off_campus:type_report:header:weekend'] = 'Weekend Only';
$string['off_campus:type_report:header:enabled'] = 'Enabled';
$string['off_campus:type_report:header:start'] = 'Start Date';
$string['off_campus:type_report:header:end'] = 'End Date';
$string['off_campus:type_report:header:formwarning'] = 'Form Warning';
$string['off_campus:type_report:header:emailwarning'] = 'Email Warning';


/* Off-Campus Signout Type Edit. */
$string['off_campus:type_edit'] = 'Edit Off-Campus Signout Type Record';

// Type information.
$string['off_campus:type_edit:type'] = 'Type Information';
$string['off_campus:type_edit:type:permissions'] = 'Permissions';
$string['off_campus:type_edit:type:name'] = 'Name';
$string['off_campus:type_edit:type:grade'] = 'Minimum Grade';
$string['off_campus:type_edit:type:grade:9'] = '9';
$string['off_campus:type_edit:type:grade:10'] = '10';
$string['off_campus:type_edit:type:grade:11'] = '11';
$string['off_campus:type_edit:type:grade:12'] = '12';
$string['off_campus:type_edit:type:boarding_status'] = 'Boarding Status';
$string['off_campus:type_edit:type:boarding_status:Boarder'] = 'Boarder';
$string['off_campus:type_edit:type:boarding_status:Day'] = 'Day';
$string['off_campus:type_edit:type:boarding_status:All'] = 'All';
$string['off_campus:type_edit:type:weekend'] = 'Weekend Only';
$string['off_campus:type_edit:type:enabled'] = 'Enabled';
$string['off_campus:type_edit:type:start'] = 'Start Date';
$string['off_campus:type_edit:type:end'] = 'End Date';
$string['off_campus:type_edit:type:form_warning'] = 'Form Warning';
$string['off_campus:type_edit:type:email_warning'] = 'Email Warning';


/* Off-Campus Signout Type Notifications. */
$string['off_campus:type:create:success'] = 'Off-Campus Signout Type Record Created Successfully';
$string['off_campus:type:update:success'] = 'Off-Campus Signout Type Record Updated Successfully';
$string['off_campus:type:delete:success'] = 'Off-Campus Signout Type Record Deleted Successfully';
$string['off_campus:type:delete:failure'] = 'Off-Campus Signout Type Record Not Found for Deletion';


/* Off-Campus Signout Form. */
$string['off_campus:form'] = 'Off-Campus Signout Form';
$string['off_campus:form:title'] = 'Off-Campus Signout Form for {$a}';

// General Information.
$string['off_campus:form:info'] = 'General Information';
$string['off_campus:form:info:student'] = 'Student';
$string['off_campus:form:info:type'] = 'Signout Type';
$string['off_campus:form:info:type_select:other'] = 'Other Adult (Please Specify)';
$string['off_campus:form:info:passengers'] = 'Your Passengers';
$string['off_campus:form:info:passengers:no_selection'] = 'No Passengers Selected';
$string['off_campus:form:info:passengers:placeholder'] = 'Search Passengers';
$string['off_campus:form:info:driver'] = 'Your Driver';

// Details.
$string['off_campus:form:details'] = 'Details';
$string['off_campus:form:details:destination'] = 'Your Destination';
$string['off_campus:form:details:departure_time'] = 'Departure Time';
$string['off_campus:form:details:approver'] = 'Face-to-Face Permission Granted by';

// Permissions Check.
$string['off_campus:form:permissions'] = 'Permissions Check';

// Errors.
$string['off_campus:form:error:no_type'] = 'You must specify a driver type.';
$string['off_campus:form:error:no_driver'] = 'You must specify a driver.';
$string['off_campus:form:error:no_destination'] = 'You must specify a destination.';
$string['off_campus:form:error:no_approver'] = 'You must specify who approved your signout.';

// Notifications.
$string['off_campus:form:success'] = 'Off-Campus Signout Form Submitted Successfully';
$string['off_campus:form:delete:success'] = 'Off-Campus Signout Record Deleted Successfully';
$string['off_campus:form:delete:failure'] = 'Off-Campus Signout Record Not Found for Deletion';


/* Off-Campus Signout Report. */
$string['off_campus:report'] = 'Off-Campus Signout Report';

// Filter.
$string['off_campus:report:select_type:all'] = 'All Types';
$string['off_campus:report:select_type:other'] = 'Other';
$string['off_campus:report:select_date:all'] = 'All Dates';
$string['off_campus:report:add'] = 'New Off-Campus Signout Record';

// Headers.
$string['off_campus:report:header:student'] = 'Student';
$string['off_campus:report:header:grade'] = 'Grade';
$string['off_campus:report:header:dorm'] = 'Dorm';
$string['off_campus:report:header:type'] = 'Type';
$string['off_campus:report:header:driver'] = 'Driver';
$string['off_campus:report:header:passengers'] = 'Passengers';
$string['off_campus:report:header:passengercount'] = 'Passengers Submitted';
$string['off_campus:report:header:destination'] = 'Destination';
$string['off_campus:report:header:departuredate'] = 'Date';
$string['off_campus:report:header:departuretime'] = 'Departure Time';
$string['off_campus:report:header:approver'] = 'Permission From';
$string['off_campus:report:header:signin'] = 'Sign In Time';

// Cells.
$string['off_campus:report:cell:passengers:none'] = 'None';
