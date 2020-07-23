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
 * English language strings for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/*
 * ========
 * General.
 * ========
 */
$string['pluginname'] = 'Middlesex';

/* Capabilities. */
$string['mxschool:manage_students'] = 'Middlesex School: View and manage student data';
$string['mxschool:manage_faculty'] = 'Middlesex School: View and manage faculty data';
$string['mxschool:manage_dorms'] = 'Middlesex School: View and manage dorm data';
$string['mxschool:manage_vehicles'] = 'Middlesex School: View and manage student vehicle registration';
$string['mxschool:manage_student_pictures'] = 'Middlesex School: Import directory pictures of students';
$string['mxschool:access_student_pictures'] = 'Middlesex School: Access directory pictures of students';
$string['mxschool:view_limited_checkin'] = 'Middlesex School: View checkin sheets only for the user\'s dorm (proctors)';
$string['mxschool:view_checkin'] = 'Middlesex School: View checkin sheets';
$string['mxschool:manage_checkin_preferences'] = 'Middlesex School: View and manage checkin preferences';
$string['mxschool:manage_weekend'] = 'Middlesex School: View and manage weekend forms';
$string['mxschool:manage_advisor_selection'] = 'Middlesex School: View and manage students\' advisor selection records';
$string['mxschool:manage_advisor_selection_preferences'] = 'Middlesex School: View and manage advisor selection preferences';
$string['mxschool:manage_rooming'] = 'Middlesex School: View and manage students\' rooming request records';
$string['mxschool:manage_rooming_preferences'] = 'Middlesex School: View and manage rooming preferences';
$string['mxschool:manage_vacation_travel'] = 'Middlesex School: View and manage students\' basic vacation travel records';
$string['mxschool:notify_vacation_travel'] = 'Middlesex School: Notify students who haven\'t submitted a vacation travel form';
$string['mxschool:manage_vacation_travel_transportation'] = 'Middlesex School: View and manage students\' detailed vacation travel records';
$string['mxschool:manage_vacation_travel_preferences'] = 'Middlesex School: View and manage vacation travel preferences';
$string['mxschool:manage_deans_permission'] = 'Middlesex School: View and manage deans\' permission forms';
$string['mxschool:manage_healthpass_preferences'] = 'Middlesex School: View and manage healthpass preferences';
$string['mxschool:manage_healthpass'] = 'Middlesex School: View and Manage healthpass data';


/* Events. */
$string['event:page_viewed'] = 'mxschool page viewed';
$string['event:record_created'] = 'mxschool record created';
$string['event:record_updated'] = 'mxschool record updated';
$string['event:record_deleted'] = 'mxschool record deleted';
$string['event:email_sent'] = 'mxschool email notification sent';


/* Privacy. */
$string['privacy:metadata'] = 'Please contact the IT Department for more details.';


/* Renderables. */

// Checkbox.
$string['checkbox:saved'] = 'saved';

// Dropdown.
$string['dropdown:default'] = 'All';
$string['dropdown:dorm'] = 'All Dorms';
$string['dropdown:house_all'] = 'All Houses';
$string['dropdown:house_day'] = 'All Day';
$string['dropdown:house_boarding'] = 'All Boarding';

// Email Button.
$string['email_button:confirmation'] = 'Are you sure you want to send this bulk notification?';
$string['email_button:sending'] = 'Emails Sending...';
$string['email_button:success'] = 'Emails Sent';
$string['email_button:failure'] = 'Emails Failed to Send';

// Form.
$string['form:select:default'] = 'Select';

// Report.
$string['report:print_button'] = 'Print';
$string['report:header:actions'] = 'Actions';
$string['report:delete_icon:confirmation'] = 'Are you sure want to delete this record?';


/* Settings. */
$string['mxschool_category'] = 'Middlesex';

// Email Settings.
$string['email_settings'] = 'Email Settings';
$string['email_settings:redirect'] = 'Redirect email';
$string['email_settings:redirect:description'] = 'The email address to redirect to for debugging - if empty emails will be sent to actual recipients.';
$string['email_settings:deans_email'] = 'Deans email';
$string['email_settings:deans_email:description'] = 'The email address to send notifications to the deans.';
$string['email_settings:deans_addressee'] = 'Deans addressee name';
$string['email_settings:deans_addressee:description'] = 'The name to use when addressing the deans in a email notification.';
$string['email_settings:transportationmanager_email'] = 'Transportation manager email';
$string['email_settings:transportationmanager_email:description'] = 'The email address to send notifications to the transportation manager.';
$string['email_settings:transportationmanager_addressee'] = 'Transportation manager addressee name';
$string['email_settings:transportationmanager_addressee:description'] = 'The name to use when addressing the transportation manager in a email notification.';

// Other Settings.
$string['other_settings'] = 'General Settings';
$string['other_settings:table_size'] = 'Default table length';
$string['other_settings:table_size:description'] = 'The number of rows to display when outputting tables.';

// Index Pages.
$string['indexes'] = 'Index Pages';
$string['indexes:mxschool'] = 'Middlesex Index';
$string['indexes:user_management'] = 'User Management Index';
$string['indexes:checkin'] = 'Checkin Sheets and Weekend Forms Index';
$string['indexes:advisor_selection'] = 'Advisor Selection Index';
$string['indexes:rooming'] = 'Rooming Index';
$string['indexes:vacation_travel'] = 'Vacation Travel Index';


/* Miscelaneous. */
$string['am'] = 'AM';
$string['pm'] = 'PM';
$string['semester:1'] = 'First Semester';
$string['semester:2'] = 'Second Semester';
$string['weekend_type:open'] = 'Open';
$string['weekend_type:closed'] = 'Closed';
$string['weekend_type:free'] = 'Free';
$string['weekend_type:vacation'] = 'Vacation';
$string['room_type:single'] = 'Single';
$string['room_type:double'] = 'Double';
$string['room_type:quad'] = 'Quad';



/*
 * ================
 * User Management.
 * ================
 */
$string['user_management'] = 'User Management';


/* Student Report. */
$string['user_management:student_report'] = 'Student Report';

// Filter.
$string['user_management:student_report:type:students'] = 'Student Report';
$string['user_management:student_report:type:permissions'] = 'Permissions Report';
$string['user_management:student_report:type:parents'] = 'Parent Report';
$string['user_management:student_report:add_parent'] = 'New Parent';

// Students headers.
$string['user_management:student_report:students:header:student'] = 'Name';
$string['user_management:student_report:students:header:grade'] = 'Grade';
$string['user_management:student_report:students:header:advisor'] = 'Advisor';
$string['user_management:student_report:students:header:dorm'] = 'Dorm';
$string['user_management:student_report:students:header:room'] = 'Room';
$string['user_management:student_report:students:header:phone'] = 'Phone Number';
$string['user_management:student_report:students:header:birthday'] = 'Birthday';

// Permissions headers.
$string['user_management:student_report:permissions:header:student'] = 'Name';
$string['user_management:student_report:permissions:header:overnight'] = 'May stay overnight with the permission of the';
$string['user_management:student_report:permissions:header:may_drive_over_21'] = 'May ride with drivers over 21?';
$string['user_management:student_report:permissions:header:may_drive_with_anyone'] = 'May ride with any driver?';
$string['user_management:student_report:permissions:header:travel_to_cities'] = 'May travel to regional cities?';
$string['user_management:student_report:permissions:header:passengers'] = 'May Drive Passengers?';
$string['user_management:student_report:permissions:header:rideshare'] = 'May Use Rideshare?';
$string['user_management:student_report:permissions:header:swimallowed'] = 'Allowed to Swim?';
$string['user_management:student_report:permissions:header:boatallowed'] = 'Allowed in Boats?';

// Parents headers.
$string['user_management:student_report:parents:header:student'] = 'Student Name';
$string['user_management:student_report:parents:header:parent'] = 'Parent Name';
$string['user_management:student_report:parents:header:primaryparent'] = 'Primary';
$string['user_management:student_report:parents:header:relationship'] = 'Relationship';
$string['user_management:student_report:parents:header:homephone'] = 'Home Phone';
$string['user_management:student_report:parents:header:cellphone'] = 'Cell Phone';
$string['user_management:student_report:parents:header:workphone'] = 'Work Phone';
$string['user_management:student_report:parents:header:email'] = 'Email';


/* Student Edit. */
$string['user_management:student_edit'] = 'Edit Student Record';

// Student Information.
$string['user_management:student_edit:student'] = 'Student Information';
$string['user_management:student_edit:student:firstname'] = 'First Name';
$string['user_management:student_edit:student:middlename'] = 'Middle Name';
$string['user_management:student_edit:student:lastname'] = 'Last Name';
$string['user_management:student_edit:student:alternatename'] = 'Alternate Name';
$string['user_management:student_edit:student:email'] = 'Email';
$string['user_management:student_edit:student:admission_year'] = 'Year of Admission';
$string['user_management:student_edit:student:grade'] = 'Grade';
$string['user_management:student_edit:student:grade:9'] = '9';
$string['user_management:student_edit:student:grade:10'] = '10';
$string['user_management:student_edit:student:grade:11'] = '11';
$string['user_management:student_edit:student:grade:12'] = '12';
$string['user_management:student_edit:student:gender'] = 'Gender';
$string['user_management:student_edit:student:gender:M'] = 'M';
$string['user_management:student_edit:student:gender:F'] = 'F';
$string['user_management:student_edit:student:advisor'] = 'Advisor';
$string['user_management:student_edit:student:is_boarder'] = 'Boarder/Day Student';
$string['user_management:student_edit:student:is_boarder:Boarder'] = 'Boarder';
$string['user_management:student_edit:student:is_boarder:Day'] = 'Day';
$string['user_management:student_edit:student:is_boarder_next_year'] = 'Boarder/Day Student Next Year';
$string['user_management:student_edit:student:is_boarder_next_year:Boarder'] = 'Boarder';
$string['user_management:student_edit:student:is_boarder_next_year:Day'] = 'Day';
$string['user_management:student_edit:student:dorm'] = 'Dorm';
$string['user_management:student_edit:student:room'] = 'Room';
$string['user_management:student_edit:student:picture'] = 'Student Picture Filename';
$string['user_management:student_edit:student:phone_number'] = 'Phone Number';
$string['user_management:student_edit:student:birthday'] = 'Birthday';

// Student Permissions.
$string['user_management:student_edit:permissions'] = 'Student Permissions';
$string['user_management:student_edit:permissions:overnight'] = 'Overnight';
$string['user_management:student_edit:permissions:overnight:Parent'] = 'Parent';
$string['user_management:student_edit:permissions:overnight:Host'] = 'Host';
$string['user_management:student_edit:permissions:may_drive_passengers'] = 'May Drive Passengers?';
$string['user_management:student_edit:permissions:may_drive_passengers:Parent'] = 'Parent';
$string['user_management:student_edit:permissions:may_use_rideshare'] = 'May Use Rideshare?';
$string['user_management:student_edit:permissions:may_use_rideshare:Parent'] = 'Parent';
$string['user_management:student_edit:permissions:may_travel_to_regional_cities'] = 'May Go to Regional Cities?';
$string['user_management:student_edit:permissions:may_travel_to_regional_cities:Parent'] = 'Parent';
$string['user_management:student_edit:permissions:may_drive_with_over_21'] = 'May ride with a driver over 21?';
$string['user_management:student_edit:permissions:may_drive_with_over_21:Parent'] = 'Parent';
$string['user_management:student_edit:permissions:may_drive_with_anyone'] = 'May ride with anyone?';
$string['user_management:student_edit:permissions:may_drive_with_anyone:Parent'] = 'Parent';
$string['user_management:student_edit:permissions:swim_competent'] = 'Competent Swimmer?';
$string['user_management:student_edit:permissions:swim_allowed'] = 'Allowed to Swim?';
$string['user_management:student_edit:permissions:boat_allowed'] = 'Allowed in Boats?';


/* Student Notifications. */
$string['user_management:student:update:success'] = 'Student Record Updated Successfully';


/* Parent Edit. */
$string['user_management:parent_edit'] = 'Edit Parent Record';

// Parent Information.
$string['user_management:parent_edit:parent'] = 'Parent Information';
$string['user_management:parent_edit:parent:student'] = 'Child';
$string['user_management:parent_edit:parent:name'] = 'Parent Name';
$string['user_management:parent_edit:parent:is_primary'] = 'Primary Parent?';
$string['user_management:parent_edit:parent:relationship'] = 'Relationship to Child';
$string['user_management:parent_edit:parent:home_phone'] = 'Parent Home Phone';
$string['user_management:parent_edit:parent:cell_phone'] = 'Parent Cell Phone';
$string['user_management:parent_edit:parent:work_phone'] = 'Parent Work Phone';
$string['user_management:parent_edit:parent:email'] = 'Parent Email';


/* Parent Notifications. */
$string['user_management:parent:create:success'] = 'Parent Record Created Successfully';
$string['user_management:parent:update:success'] = 'Parent Record Updated Successfully';
$string['user_management:parent:delete:success'] = 'Parent Record Deleted Successfully';
$string['user_management:parent:delete:failure'] = 'Parent Record Not Found for Deletion';


/* Faculty Report. */
$string['user_management:faculty_report'] = 'Faculty Report';

// Headers.
$string['user_management:faculty_report:header:name'] = 'Name';
$string['user_management:faculty_report:header:dorm'] = 'Dorm';
$string['user_management:faculty_report:header:approvesignout'] = 'May Approve Off-Campus Signout?';
$string['user_management:faculty_report:header:advisoryavailable'] = 'Advisory Available?';
$string['user_management:faculty_report:header:advisoryclosing'] = 'Advisory Closing?';


/* Faculty Edit. */
$string['user_management:faculty_edit'] = 'Edit Faculty Record';

// Faculty Information.
$string['user_management:faculty_edit:faculty'] = 'Faculty Information';
$string['user_management:faculty_edit:faculty:firstname'] = 'First Name';
$string['user_management:faculty_edit:faculty:middlename'] = 'Middle Name';
$string['user_management:faculty_edit:faculty:lastname'] = 'Last Name';
$string['user_management:faculty_edit:faculty:alternatename'] = 'Alternate Name';
$string['user_management:faculty_edit:faculty:email'] = 'Email';
$string['user_management:faculty_edit:faculty:dorm'] = 'Dorm';
$string['user_management:faculty_edit:faculty:approve_signout'] = 'May Approve Off-Campus Signout';
$string['user_management:faculty_edit:faculty:advisory_available'] = 'Advisory Available';
$string['user_management:faculty_edit:faculty:advisory_closing'] = 'Advisory Closing';


/* Faculty Notifications. */
$string['user_management:faculty:update:success'] = 'Faculty Record Updated Successfully';


/* Dorm Report. */
$string['user_management:dorm_report'] = 'Dorm Report';

// Filter.
$string['user_management:dorm_report:add'] = 'New Dorm';

// Headers.
$string['user_management:dorm_report:header:name'] = 'Name';
$string['user_management:dorm_report:header:hoh'] = 'Head of House';
$string['user_management:dorm_report:header:permissionsline'] = 'Permissions Line';
$string['user_management:dorm_report:header:type'] = 'Type';
$string['user_management:dorm_report:header:gender'] = 'Gender';
$string['user_management:dorm_report:header:available'] = 'Available';


/* Dorm Edit. */
$string['user_management:dorm_edit'] = 'Edit Dorm Record';

// Dorm Information.
$string['user_management:dorm_edit:dorm'] = 'Dorm Information';
$string['user_management:dorm_edit:dorm:name'] = 'Name';
$string['user_management:dorm_edit:dorm:hoh'] = 'Head of House';
$string['user_management:dorm_edit:dorm:permissions_line'] = 'Permissions Line';
$string['user_management:dorm_edit:dorm:type'] = 'Type';
$string['user_management:dorm_edit:dorm:type:Boarding'] = 'Boarding';
$string['user_management:dorm_edit:dorm:type:Day'] = 'Day';
$string['user_management:dorm_edit:dorm:type:All'] = 'All';
$string['user_management:dorm_edit:dorm:gender'] = 'Gender';
$string['user_management:dorm_edit:dorm:gender:Boys'] = 'Boys';
$string['user_management:dorm_edit:dorm:gender:Girls'] = 'Girls';
$string['user_management:dorm_edit:dorm:gender:All'] = 'All';
$string['user_management:dorm_edit:dorm:available'] = 'Available';


/* Dorm Notifications. */
$string['user_management:dorm:create:success'] = 'Dorm Record Created Successfully';
$string['user_management:dorm:update:success'] = 'Dorm Record Updated Successfully';
$string['user_management:dorm:delete:success'] = 'Dorm Record Deleted Successfully';
$string['user_management:dorm:delete:failure'] = 'Dorm Record Not Found for Deletion';


/* Vehicle Report. */
$string['user_management:vehicle_report'] = 'Registered Student Vehicles Report';

// Filter.
$string['user_management:vehicle_report:add'] = 'Register a Vehicle';

// Headers.
$string['user_management:vehicle_report:header:student'] = 'Student Name';
$string['user_management:vehicle_report:header:grade'] = 'Grade';
$string['user_management:vehicle_report:header:phone'] = 'Student Phone Number';
$string['user_management:vehicle_report:header:drive_passengers'] = 'May Drive Passengers';
$string['user_management:vehicle_report:header:make'] = 'Vehicle Make';
$string['user_management:vehicle_report:header:model'] = 'Vehicle Model';
$string['user_management:vehicle_report:header:color'] = 'Vehicle Color';
$string['user_management:vehicle_report:header:registration'] = 'Vehicle Registration';


/* Vehicle Edit. */
$string['user_management:vehicle_edit'] = 'Edit Student Vehicle Record';

// Student Information.
$string['user_management:vehicle_edit:info'] = 'Student Information';
$string['user_management:vehicle_edit:info:student'] = 'Student';

// Vehicle Information.
$string['user_management:vehicle_edit:vehicle'] = 'Vehicle Information';
$string['user_management:vehicle_edit:vehicle:make'] = 'Make';
$string['user_management:vehicle_edit:vehicle:model'] = 'Model';
$string['user_management:vehicle_edit:vehicle:color'] = 'Color';
$string['user_management:vehicle_edit:vehicle:registration'] = 'Registration';


/* Vehicle Notifications. */
$string['user_management:vehicle:create:success'] = 'Vehicle Record Created Successfully';
$string['user_management:vehicle:update:success'] = 'Vehicle Record Updated Successfully';
$string['user_management:vehicle:delete:success'] = 'Vehicle Record Deleted Successfully';
$string['user_management:vehicle:delete:failure'] = 'Vehicle Record Not Found for Deletion';


/* Picture Import. */
$string['user_management:picture_import'] = 'Import Student Pictures';
$string['user_management:picture_import:clear:text'] = 'Select to Delete all Student Picture Records from the Database';
$string['user_management:picture_import:pictures'] = 'Select Pictures to Import';


/* Picture Notifications. */
$string['user_management:picture_import:success'] = 'Student Pictures Imported Successfully';
$string['user_management:picture_import:delete:success'] = 'Student Pictures Deleted Successfully';



/*
 * ==================================
 * Check-In Sheets and Weekend Forms.
 * ==================================
 */
$string['checkin'] = 'Check-In Sheets and Weekend Forms';


/* Check-in Preferences. */
$string['checkin:preferences'] = 'Check-In Sheets Preferences';

// Opening and Closing Dates.
$string['checkin:preferences:dates'] = 'Opening and Closing Dates';
$string['checkin:preferences:dates:dorms_open'] = 'Dorms Open On: ';
$string['checkin:preferences:dates:second_semester'] = 'Second Semester Starts On: ';
$string['checkin:preferences:dates:dorms_close'] = 'Dorms Close On: ';

// Weekend Types.
$string['checkin:preferences:weekends'] = 'Weekend Types';
$string['checkin:preferences:weekends:label'] = 'Saturday {$a}';

$string['checkin:preferences:attendance'] = 'Attendance Report';
$string['checkin:preferences:attendance:reset_attendance_data'] = 'At what time should attendance data be reset?';

// Weekend Form Email Notifications.
$string['checkin:preferences:notifications'] = 'Weekend Form Email Notifications';
$string['checkin:preferences:notifications:submitted_tags'] = 'Available Tags for Weekend Form Submitted Email';
$string['checkin:preferences:notifications:submitted_subject'] = 'Subject for Weekend Form Submitted Email';
$string['checkin:preferences:notifications:submitted_body'] = 'Body for Weekend Form Submitted Email';
$string['checkin:preferences:notifications:approved_tags'] = 'Available Tags for Weekend Form Approved Email';
$string['checkin:preferences:notifications:approved_subject'] = 'Subject for Weekend Form Approved Email';
$string['checkin:preferences:notifications:approved_body'] = 'Body for Weekend Form Approved Email';

// Weekend Form Instructions.
$string['checkin:preferences:text'] = 'Weekend Form Instructions';
$string['checkin:preferences:text:top_instructions'] = 'Top Instructions';
$string['checkin:preferences:text:bottom_instructions'] = 'Bottom Instructions';
$string['checkin:preferences:text:closed_warning'] = 'Warning for a Closed Weekend';

// Notification.
$string['checkin:preferences:update:success'] = 'Check-in Preferences Saved Successfully';


/* Generic Check-in Sheet. */
$string['checkin:generic_report'] = 'Check-In Sheet';
$string['checkin:generic_report:title'] = '{$a}Check-In Sheet for __________';

// Headers.
$string['checkin:generic_report:header:student'] = 'Name';
$string['checkin:generic_report:header:dorm'] = 'Dorm';
$string['checkin:generic_report:header:room'] = 'Room';
$string['checkin:generic_report:header:grade'] = 'Grade';
$string['checkin:generic_report:header:checkin'] = '';


/* Weekday Check-in Sheet. */
$string['checkin:weekday_report'] = 'Weekday Check-In Sheet';
$string['checkin:weekday_report:title'] = '{$a}Check-In Sheet for the Week of __________';

// Headers.
$string['checkin:weekday_report:header:student'] = 'Name';
$string['checkin:weekday_report:header:dorm'] = 'Dorm';
$string['checkin:weekday_report:header:room'] = 'Room';
$string['checkin:weekday_report:header:grade'] = 'Grade';
$string['checkin:weekday_report:header:early'] = 'Early';
$string['checkin:weekday_report:header:late'] = 'Late';


/* Weekend Form. */
$string['checkin:weekend_form'] = 'Weekend Form';
$string['checkin:weekend_form:title'] = 'Weekend Form for {$a}';
$string['checkin:weekend_form:dorm'] = 'Dorm';
$string['checkin:weekend_form:dorm:default'] = 'All Dorms';
$string['checkin:weekend_form:student'] = 'Student';
$string['checkin:weekend_form:departure'] = 'Departure Date and Time';
$string['checkin:weekend_form:return'] = 'Return Date and Time';
$string['checkin:weekend_form:destination'] = 'Your Destination';
$string['checkin:weekend_form:transportation'] = 'Transportation by';
$string['checkin:weekend_form:phone'] = 'Phone Number<br>(even if you are going home)';

// Errors.
$string['checkin:weekend_form:error:out_of_order'] = 'Your return date and time must be after your departure date and time.';
$string['checkin:weekend_form:error:not_in_weekend'] = 'Your departure date must be within a valid weekend.';
$string['checkin:weekend_form:error:in_different_weekends'] = 'Your return date must be in the same weekend as your departure date.';
$string['checkin:weekend_form:error:no_destination'] = 'You must specify a destination.';
$string['checkin:weekend_form:error:no_transportation'] = 'You must specify who is driving you.';
$string['checkin:weekend_form:error:no_phone'] = 'You must specify a phone number.';

// Notifications.
$string['checkin:weekend_form:success'] = 'Weekend Form Submitted Successfully';
$string['checkin:weekend_form:delete:success'] = 'Weekend Form Record Deleted Successfully';
$string['checkin:weekend_form:delete:failure'] = 'Weekend Form Record Not Found for Deletion';


/* Weekend Check-in Sheet. */
$string['checkin:weekend_report'] = 'Weekend Check-In Sheet';
$string['checkin:weekend_report:title'] = '{$a->dorm}Check-In Sheet for the Weekend of {$a->weekend} ({$a->type})';

// Filter.
$string['checkin:weekend_report:select_start_day:default'] = 'Default Start Day';
$string['checkin:weekend_report:select_end_day:default'] = 'Default End Day';
$string['checkin:weekend_report:select_submitted:true'] = 'Weekend Form';
$string['checkin:weekend_report:select_submitted:false'] = 'No Weekend Form';
$string['checkin:weekend_report:add'] = 'New Weekend Form';

// Headers.
$string['checkin:weekend_report:header:student'] = 'Name';
$string['checkin:weekend_report:header:dorm'] = 'Dorm';
$string['checkin:weekend_report:header:room'] = 'Room';
$string['checkin:weekend_report:header:grade'] = 'Grade';
$string['checkin:weekend_report:header:early'] = 'Early';
$string['checkin:weekend_report:header:late'] = 'Late';
$string['checkin:weekend_report:header:clean'] = 'Room Clean?';
$string['checkin:weekend_report:header:parent'] = 'Parent?';
$string['checkin:weekend_report:header:invite'] = 'Invite?';
$string['checkin:weekend_report:header:approved'] = 'Approved?';
$string['checkin:weekend_report:header:destinationtransportation'] = 'Destination<br>Transportation';
$string['checkin:weekend_report:header:phone'] = 'Phone Number';
$string['checkin:weekend_report:header:departurereturn'] = 'Departure<br>Return';

// Cells.
$string['checkin:weekend_report:cell:approve_button'] = 'Send Email';


/* Weekend Comment Form. */
$string['checkin:weekend_comment_form:comment'] = 'Comments';

// Notifications.
$string['checkin:weekend_comment_form:create:success'] = 'Weekend Comment Created Successfully';
$string['checkin:weekend_comment_form:update:success'] = 'Weekend Comment Updated Successfully';


/* Weekend Calculator. */
$string['checkin:weekend_calculator'] = 'Weekend Calculator';
$string['checkin:weekend_calculator:title'] = '{$a}Weekend Calculator';

// Headers.
$string['checkin:weekend_calculator:header:student'] = 'Name';
$string['checkin:weekend_calculator:header:dorm'] = 'Dorm';
$string['checkin:weekend_calculator:header:room'] = 'Room';
$string['checkin:weekend_calculator:header:grade'] = 'Grade';
$string['checkin:weekend_calculator:header:total'] = 'Total';
$string['checkin:weekend_calculator:header:allowed'] = 'Allowed';

// Cells.
$string['checkin:weekend_calculator:cell:off_campus'] = 'X';
$string['checkin:weekend_calculator:cell:free'] = 'free';
$string['checkin:weekend_calculator:cell:closed'] = 'camp';
$string['checkin:weekend_calculator:cell:unlimited'] = 'ALL';

// Legend.
$string['checkin:weekend_calculator:legend:header'] = 'Legend';
$string['checkin:weekend_calculator:legend:0_left'] = 'No weekends left';
$string['checkin:weekend_calculator:legend:1_left'] = '1 weekend left';
$string['checkin:weekend_calculator:legend:2_left'] = '2 weekends left';
$string['checkin:weekend_calculator:legend:3_left'] = '3+ weekends left';
$string['checkin:weekend_calculator:legend:off_campus'] = 'Student Off Campus';
$string['checkin:weekend_calculator:legend:free'] = 'Free weekend';
$string['checkin:weekend_calculator:legend:closed'] = 'Campus weekend';

/* Attendance Report */
$string['checkin:attendance_report'] = 'Attendance Report';
$string['checkin:attendance_report:title'] = 'Attendance Report';

// Headers
$string['checkin:attendance_report:header:student'] = 'Name';
$string['checkin:attendance_report:header:dorm'] = 'Dorm';
$string['checkin:attendance_report:header:room'] = 'Room';
$string['checkin:attendance_report:header:grade'] = 'Grade';
$string['checkin:attendance_report:header:attended'] = 'Attended';
$string['checkin:attendance_report:reset_data'] = 'Reset Attendance Data';

// Filters
$string['checkin:attendance_report:attendance:all'] = 'All';
$string['checkin:attendance_report:attendance:1'] = 'Attended';
$string['checkin:attendance_report:attendance:0'] = 'Absent';

/*
 * ==================
 * Advisor Selection.
 * ==================
 */
$string['advisor_selection'] = 'Advisor Selection';


/* Advisor Selection Preferences. */
$string['advisor_selection:preferences'] = 'Advisor Selection Preferences';

// Availability.
$string['advisor_selection:preferences:availability'] = 'Availability';
$string['advisor_selection:preferences:availability:start'] = 'Start Date';
$string['advisor_selection:preferences:availability:stop'] = 'Stop Date';
$string['advisor_selection:preferences:availability:who'] = 'Enable for Whom';
$string['advisor_selection:preferences:availability:who:new'] = 'Only New Students';
$string['advisor_selection:preferences:availability:who:all'] = 'All Underclassmen';

// Advisor Selection Email Notifications.
$string['advisor_selection:preferences:notifications'] = 'Advisor Selection Email Notifications';
$string['advisor_selection:preferences:notifications:submitted_tags'] = 'Available Tags for Advisor Selection Form Submitted Email';
$string['advisor_selection:preferences:notifications:submitted_subject'] = 'Subject for Advisor Selection Form Submitted Email';
$string['advisor_selection:preferences:notifications:submitted_body'] = 'Body for Advisor Selection Form Submitted Email';
$string['advisor_selection:preferences:notifications:unsubmitted_tags'] = 'Available Tags for Unsubmitted Reminder Email';
$string['advisor_selection:preferences:notifications:unsubmitted_subject'] = 'Subject for Unsubmitted Reminder Email';
$string['advisor_selection:preferences:notifications:unsubmitted_body'] = 'Body for Unsubmitted Reminder Email';
$string['advisor_selection:preferences:notifications:results_tags'] = 'Available Tags for Results Email';
$string['advisor_selection:preferences:notifications:results_subject'] = 'Subject for Results Email';
$string['advisor_selection:preferences:notifications:results_body'] = 'Body for Results Email';

// Advisor Selection Form Instructions.
$string['advisor_selection:preferences:text'] = 'Advisor Selection Form Instructions';
$string['advisor_selection:preferences:text:closing_warning'] = 'Warning for Closing Advisory';
$string['advisor_selection:preferences:text:instructions'] = 'Changing Advisor Instructions';

// Notification.
$string['advisor_selection:preferences:update:success'] = 'Advisor Selection Preferences Saved Successfully';


/* Faculty Report. */
$string['advisor_selection:faculty_report'] = 'Faculty Report';

// Headers.
$string['advisor_selection:faculty_report:header:name'] = 'Name';
$string['advisor_selection:faculty_report:header:advisoryavailable'] = 'Advisory Available?';
$string['advisor_selection:faculty_report:header:advisoryclosing'] = 'Advisory Closing?';


/* Advisor Selection Form. */
$string['advisor_selection:form'] = 'Advisor Selection Form';
$string['advisor_selection:form:title'] = 'Advisor Selection Form for {$a}';

// General Information.
$string['advisor_selection:form:info'] = 'General Information';
$string['advisor_selection:form:info:student'] = 'Student';
$string['advisor_selection:form:info:current'] = 'Current Advisor';
$string['advisor_selection:form:info:keep_current'] = 'Keep Current Advisor';

// Choices.
$string['advisor_selection:form:options'] = 'Choices';
$string['advisor_selection:form:options:option1'] = 'First Choice';
$string['advisor_selection:form:options:option2'] = 'Second Choice';
$string['advisor_selection:form:options:option3'] = 'Third Choice';
$string['advisor_selection:form:options:option4'] = 'Fourth Choice';
$string['advisor_selection:form:options:option5'] = 'Fifth Choice';

// Deans.
$string['advisor_selection:form:deans'] = 'Deans\' Selection';
$string['advisor_selection:form:deans:selected'] = 'Chosen Advisor';

// Errors.
$string['advisor_selection:form:error:no_keep_current'] = 'You must specify whether or not you wish to keep your current advisor.';
$string['advisor_selection:form:error:incomplete'] = 'You must either select five choices, or you current advisor must be your final choice.';

// Notifications.
$string['advisor_selection:form:success'] = 'Advisor Selection Form Submitted Successfully';


/* Advisor Selection Report. */
$string['advisor_selection:report'] = 'Advisor Selection Report';

// Filter.
$string['advisor_selection:report:select_submitted:true'] = 'Submitted';
$string['advisor_selection:report:select_submitted:false'] = 'Not Submitted';
$string['advisor_selection:report:select_keepcurrent:true'] = 'Keeping Current Advisor';
$string['advisor_selection:report:select_keepcurrent:false'] = 'Changing Advisor';
$string['advisor_selection:report:add'] = 'New Advisor Selection Form';
$string['advisor_selection:report:remind'] = 'Notify Unsubmitted';
$string['advisor_selection:report:results'] = 'Notify Students and New Advisors';

// Headers.
$string['advisor_selection:report:header:student'] = 'Student';
$string['advisor_selection:report:header:current'] = 'Current Advisor';
$string['advisor_selection:report:header:keepcurrent'] = 'Keep Current Advisor?';
$string['advisor_selection:report:header:option1'] = 'Choice 1';
$string['advisor_selection:report:header:option2'] = 'Choice 2';
$string['advisor_selection:report:header:option3'] = 'Choice 3';
$string['advisor_selection:report:header:option4'] = 'Choice 4';
$string['advisor_selection:report:header:option5'] = 'Choice 5';
$string['advisor_selection:report:header:selected'] = 'Chosen Advisor';



/*
 * ========
 * Rooming.
 * ========
 */
$string['rooming'] = 'Rooming';


/* Rooming Preferecnes. */
$string['rooming:preferences'] = 'Rooming Preferences';

// Availability.
$string['rooming:preferences:availability'] = 'Availability';
$string['rooming:preferences:availability:start'] = 'Start Date';
$string['rooming:preferences:availability:stop'] = 'Stop Date';

// Rooming Email Notifications.
$string['rooming:preferences:notifications'] = 'Rooming Email Notifications';
$string['rooming:preferences:notifications:submitted_tags'] = 'Available Tags for Rooming Form Submitted Email';
$string['rooming:preferences:notifications:submitted_subject'] = 'Subject for Rooming Form Submitted Email';
$string['rooming:preferences:notifications:submitted_body'] = 'Body for Rooming Form Submitted Email';
$string['rooming:preferences:notifications:unsubmitted_tags'] = 'Available Tags for Unsubmitted Reminder Email';
$string['rooming:preferences:notifications:unsubmitted_subject'] = 'Subject for Unsubmitted Reminder Email';
$string['rooming:preferences:notifications:unsubmitted_body'] = 'Body for Unsubmitted Reminder Email';

// Rooming Requests Form Instructions.
$string['rooming:preferences:text'] = 'Rooming Requests Form Instructions';
$string['rooming:preferences:text:roommate_instructions'] = 'Instructions Regarding Doubles';

// Notification.
$string['rooming:preferences:update:success'] = 'Rooming Preferences Saved Successfully';


/* Rooming Form. */
$string['rooming:form'] = 'Rooming Requests Form';
$string['rooming:form:title'] = 'Rooming Requests Form for {$a}';

// General Information.
$string['rooming:form:info'] = 'General Information';
$string['rooming:form:info:student'] = 'Student';
$string['rooming:form:info:dorm'] = 'Current Dorm';
$string['rooming:form:info:liveddouble'] = 'Have you previously lived in a one-room double?';

// Requests.
$string['rooming:form:requests'] = 'Requests';
$string['rooming:form:requests:roomtype'] = 'Request a Room Type';
$string['rooming:form:requests:dormmate1'] = 'Request 3 Dormmates from Your Grade';
$string['rooming:form:requests:dormmate4'] = 'Request 3 Dormmates from Any Grade';
$string['rooming:form:requests:roommate'] = 'Preferred Roommate';

// Errors.
$string['rooming:form:error:no_lived_double'] = 'You must specify whether you have lived in a double.';
$string['rooming:form:error:no_room_type'] = 'You must specify a room type.';
$string['rooming:form:error:grade_dormmates'] = 'You must request three dormmates from your grade.';
$string['rooming:form:error:dormmates'] = 'You must request three dormmates from any grade.';
$string['rooming:form:error:roommate'] = 'You must select a preferred roommate.';

// Notifications.
$string['rooming:form:success'] = 'Rooming Requests Form Submitted Successfully';


/* Rooming Report. */
$string['rooming:report'] = 'Rooming Requests Report';

// Filter.
$string['rooming:report:select_submitted:true'] = 'Submitted';
$string['rooming:report:select_submitted:false'] = 'Not Submitted';
$string['rooming:report:select_gender:all'] = 'All Genders';
$string['rooming:report:select_gender:M'] = 'Boys';
$string['rooming:report:select_gender:F'] = 'Girls';
$string['rooming:report:select_roomtype:all'] = 'All Room Types';
$string['rooming:report:select_double:true'] = 'Has Lived in Double';
$string['rooming:report:select_double:false'] = 'Has Not Lived in Double';
$string['rooming:report:add'] = 'New Rooming Requests Form';
$string['rooming:report:remind'] = 'Notify Unsubmitted';

// Headers.
$string['rooming:report:header:student'] = 'Student';
$string['rooming:report:header:grade'] = 'Grade';
$string['rooming:report:header:gender'] = 'Gender';
$string['rooming:report:header:dorm'] = 'Current Dorm';
$string['rooming:report:header:roomtype'] = 'Requested Room Type';
$string['rooming:report:header:dormmates'] = 'Requested Dormmates';
$string['rooming:report:header:liveddouble'] = 'Has Lived in a Double';
$string['rooming:report:header:roommate'] = 'Preferred Roommate';



/*
 * ================
 * Vacation Travel.
 * ================
 */
$string['vacation_travel'] = 'Vacation Travel';


/* Vacation Travel Preferences. */
$string['vacation_travel:preferences'] = 'Vacation Travel Preferences';

// Availability.
$string['vacation_travel:preferences:availability'] = 'Availability';
$string['vacation_travel:preferences:availability:start'] = 'Start Date';
$string['vacation_travel:preferences:availability:stop'] = 'Stop Date';
$string['vacation_travel:preferences:availability:return_enabled:text'] = 'Check to Enable the Return Portion of the Form and Reports.';

// Vacation Travel Email Notifications.
$string['vacation_travel:preferences:notifications'] = 'Vacation Travel Email Notifications';
$string['vacation_travel:preferences:notifications:submitted_tags'] = 'Available Tags for Vacation Travel Form Submitted Email';
$string['vacation_travel:preferences:notifications:submitted_subject'] = 'Subject for Vacation Travel Form Submitted Email';
$string['vacation_travel:preferences:notifications:submitted_body'] = 'Body for Vacation Travel Form Submitted Email';
$string['vacation_travel:preferences:notifications:unsubmitted_tags'] = 'Available Tags for Unsubmitted Reminder Email';
$string['vacation_travel:preferences:notifications:unsubmitted_subject'] = 'Subject for Unsubmitted Reminder Email';
$string['vacation_travel:preferences:notifications:unsubmitted_body'] = 'Body for Unsubmitted Reminder Email';

// Notifcation.
$string['vacation_travel:preferences:update:success'] = 'Vacation Travel Preferences Saved Successfully';


/* Vacation Travel Site Report. */
$string['vacation_travel:site_report'] = 'Site Report';

// Filter.
$string['vacation_travel:site_report:add'] = 'New Site';

// Headers.
$string['vacation_travel:site_report:header:name'] = 'Name';
$string['vacation_travel:site_report:header:type'] = 'Type';
$string['vacation_travel:site_report:header:departureenabled'] = 'Available for Departure';
$string['vacation_travel:site_report:header:defaultdeparturetime'] = 'Default Departure Time';
$string['vacation_travel:site_report:header:returnenabled'] = 'Available for Return';
$string['vacation_travel:site_report:header:defaultreturntime'] = 'Default Return Time';


/* Vacation Travel Site Edit. */
$string['vacation_travel:site_edit'] = 'Edit Site Record';

// Site Information.
$string['vacation_travel:site_edit:site'] = 'Site Information';
$string['vacation_travel:site_edit:site:name'] = 'Name';
$string['vacation_travel:site_edit:site:type'] = 'Type';
$string['vacation_travel:site_edit:site:departure_enabled'] = 'Available for Departure';
$string['vacation_travel:site_edit:site:default_departure_time'] = 'Default Departure Time';
$string['vacation_travel:site_edit:site:return_enabled'] = 'Available for Return';
$string['vacation_travel:site_edit:site:default_return_time'] = 'Default Return Time';


/* Vacation Travel Site Notifications. */
$string['vacation_travel:site:create:success'] = 'Vacation Travel Site Record Created Successfully';
$string['vacation_travel:site:update:success'] = 'Vacation Travel Site Record Updated Successfully';
$string['vacation_travel:site:delete:success'] = 'Vacation Travel Site Record Deleted Successfully';
$string['vacation_travel:site:delete:failure'] = 'Vacation Travel Site Record Not Found for Deletion';


/* Vacation Travel Form. */
$string['vacation_travel:form'] = 'Vacation Travel Form';
$string['vacation_travel:form:title'] = 'Vacation Travel Form for {$a}';

// General Information.
$string['vacation_travel:form:info'] = 'General Information';
$string['vacation_travel:form:info:student'] = 'Student';
$string['vacation_travel:form:info:destination'] = 'Destination';
$string['vacation_travel:form:info:phone'] = 'Phone Number';

// Depature Information.
$string['vacation_travel:form:departure'] = 'Departure Information';
$string['vacation_travel:form:departure:dep_mxtransportation'] = 'Do You Need School Transportation?';
$string['vacation_travel:form:departure:dep_type'] = 'Transportation type';
$string['vacation_travel:form:departure:dep_type:Car'] = 'Car';
$string['vacation_travel:form:departure:dep_type:Plane'] = 'Plane';
$string['vacation_travel:form:departure:dep_type:Bus'] = 'Bus';
$string['vacation_travel:form:departure:dep_type:Train'] = 'Train';
$string['vacation_travel:form:departure:dep_type:NYCDirect'] = 'NYC Direct';
$string['vacation_travel:form:departure:dep_type:Non-MXBus'] = 'Non-MX Bus';
$string['vacation_travel:form:departure:dep_site'] = 'Stop';
$string['vacation_travel:form:departure:dep_site:Plane'] = 'Airport';
$string['vacation_travel:form:departure:dep_site:Train'] = 'Station';
$string['vacation_travel:form:departure:dep_site:other'] = 'Other (Please Specify Below)';
$string['vacation_travel:form:departure:dep_details'] = 'Details';
$string['vacation_travel:form:departure:dep_details:Car'] = 'Driver';
$string['vacation_travel:form:departure:dep_carrier'] = 'Carrier';
$string['vacation_travel:form:departure:dep_carrier:Plane'] = 'Airline';
$string['vacation_travel:form:departure:dep_carrier:Train'] = 'Train Company';
$string['vacation_travel:form:departure:dep_carrier:Bus'] = 'Bus Company';
$string['vacation_travel:form:departure:dep_number'] = 'Transportation Number';
$string['vacation_travel:form:departure:dep_number:Plane'] = 'Flight Number';
$string['vacation_travel:form:departure:dep_number:Train'] = 'Train Number';
$string['vacation_travel:form:departure:dep_number:Bus'] = 'Bus Number';
$string['vacation_travel:form:departure:dep_variable'] = 'Date and Time Leaving Campus';
$string['vacation_travel:form:departure:dep_variable:Plane'] = 'Flight Date and Time';
$string['vacation_travel:form:departure:dep_variable:Train'] = 'Train Date and Time';
$string['vacation_travel:form:departure:dep_variable:Bus'] = 'Bus Date and Time';
$string['vacation_travel:form:departure:dep_international'] = 'Is this an International Flight?';

// Return Information.
$string['vacation_travel:form:return'] = 'Return Information';
$string['vacation_travel:form:return:ret_mxtransportation'] = 'Do You Need School Transportation?';
$string['vacation_travel:form:return:ret_type'] = 'Transportation type';
$string['vacation_travel:form:return:ret_type:Car'] = 'Car';
$string['vacation_travel:form:return:ret_type:Plane'] = 'Plane';
$string['vacation_travel:form:return:ret_type:Bus'] = 'Bus';
$string['vacation_travel:form:return:ret_type:Train'] = 'Train';
$string['vacation_travel:form:return:ret_type:NYCDirect'] = 'NYC Direct';
$string['vacation_travel:form:return:ret_type:Non-MXBus'] = 'Non-MX Bus';
$string['vacation_travel:form:return:ret_site'] = 'Stop';
$string['vacation_travel:form:return:ret_site:Plane'] = 'Arrival Airport';
$string['vacation_travel:form:return:ret_site:Train'] = 'Arrival Station';
$string['vacation_travel:form:return:ret_site:other'] = 'Other (Please Specify Below)';
$string['vacation_travel:form:return:ret_details'] = 'Details';
$string['vacation_travel:form:return:ret_details:Car'] = 'Driver';
$string['vacation_travel:form:return:ret_carrier'] = 'Carrier';
$string['vacation_travel:form:return:ret_carrier:Plane'] = 'Airline';
$string['vacation_travel:form:return:ret_carrier:Train'] = 'Train Company';
$string['vacation_travel:form:return:ret_carrier:Bus'] = 'Bus Company';
$string['vacation_travel:form:return:ret_number'] = 'Transportation Number';
$string['vacation_travel:form:return:ret_number:Plane'] = 'Flight Number';
$string['vacation_travel:form:return:ret_number:Train'] = 'Train Number';
$string['vacation_travel:form:return:ret_number:Bus'] = 'Bus Number';
$string['vacation_travel:form:return:ret_variable'] = 'Date and Time Arriving at Campus';
$string['vacation_travel:form:return:ret_variable:Plane'] = 'Flight Date and Time';
$string['vacation_travel:form:return:ret_variable:Train'] = 'Train Date and Time';
$string['vacation_travel:form:return:ret_variable:Bus'] = 'Bus Date and Time';
$string['vacation_travel:form:return:ret_international'] = 'Will You Be Clearing Customs in Boston?';

// Errors.
$string['vacation_travel:form:error:no_destination'] = 'You must specify a destination.';
$string['vacation_travel:form:error:no_phone'] = 'You must specify a phone number.';
$string['vacation_travel:form:error:no_mxtransportation'] = 'You must specify whether you will require school transportation.';
$string['vacation_travel:form:error:no_type'] = 'You must specify a transportation type.';
$string['vacation_travel:form:error:no_site'] = 'You must specify a stop.';
$string['vacation_travel:form:error:no_airport'] = 'You must specify an airport.';
$string['vacation_travel:form:error:no_station'] = 'You must specify a station.';
$string['vacation_travel:form:error:no_driver'] = 'You must specify a driver.';
$string['vacation_travel:form:error:no_details'] = 'You must specify bus details.';
$string['vacation_travel:form:error:no_other'] = 'You must specify your other information.';
$string['vacation_travel:form:error:no_carrier:Plane'] = 'You must specify a carrier.';
$string['vacation_travel:form:error:no_carrier:Bus'] = 'You must specify a bus company.';
$string['vacation_travel:form:error:no_carrier:Train'] = 'You must specify a train company.';
$string['vacation_travel:form:error:no_number:Plane'] = 'You must specify a flight number.';
$string['vacation_travel:form:error:no_number:Bus'] = 'You must specify a bus number.';
$string['vacation_travel:form:error:no_number:Train'] = 'You must specify a train number.';
$string['vacation_travel:form:error:no_international:dep'] = 'You must specify whether your flight is an international flight.';
$string['vacation_travel:form:error:no_international:ret'] = 'You must specify whether you will be clearing customs in Boston.';
$string['vacation_travel:form:error:out_of_order'] = 'Your return date and time must be after your departure date and time.';

// Notifications.
$string['vacation_travel:form:success'] = 'Vacation Travel Form Submitted Successfully';


/* Vacation Travel Report. */
$string['vacation_travel:report'] = 'Vacation Travel Report';
$string['vacation_travel:report:title'] = '{$a}Vacation Travel Report';

// Filter.
$string['vacation_travel:report:select_submitted:true'] = 'Submitted';
$string['vacation_travel:report:select_submitted:false'] = 'Not Submitted';
$string['vacation_travel:report:add'] = 'New Vacation Travel Form';
$string['vacation_travel:report:remind'] = 'Notify Unsubmitted';

// Headers.
$string['vacation_travel:report:header:student'] = 'Student';
$string['vacation_travel:report:header:dorm'] = 'Dorm';
$string['vacation_travel:report:header:destination'] = 'Destination';
$string['vacation_travel:report:header:phone'] = 'Phone Number';
$string['vacation_travel:report:header:depdatetime'] = 'Departure Date and Time';
$string['vacation_travel:report:header:deptype'] = 'Departure Type';
$string['vacation_travel:report:header:retdatetime'] = 'Return Date and Time';
$string['vacation_travel:report:header:rettype'] = 'Return Type';
$string['vacation_travel:report:header:retinfo'] = 'Return Details';


/* Vacation Travel Transportation Report. */
$string['vacation_travel:transportation_report'] = 'Transportation Report';
$string['vacation_travel:transportation_report:title:departure'] = 'Departure Transportation Report';
$string['vacation_travel:transportation_report:title:return'] = 'Return Transportation Report';

// Filter.
$string['vacation_travel:transportation_report:select_portion:departure'] = 'Departure';
$string['vacation_travel:transportation_report:select_portion:return'] = 'Return';
$string['vacation_travel:transportation_report:select_mxtransportation:true'] = 'School Transportation';
$string['vacation_travel:transportation_report:select_mxtransportation:false'] = 'Not School Transportation';
$string['vacation_travel:transportation_report:select_type:all'] = 'All Types';
$string['vacation_travel:transportation_report:select_type:Car'] = 'Car';
$string['vacation_travel:transportation_report:select_type:Plane'] = 'Plane';
$string['vacation_travel:transportation_report:select_type:Bus'] = 'Bus';
$string['vacation_travel:transportation_report:select_type:Train'] = 'Train';
$string['vacation_travel:transportation_report:select_type:NYCDirect'] = 'NYC Direct';
$string['vacation_travel:transportation_report:select_type:Non-MXBus'] = 'Non-MX Bus';
$string['vacation_travel:transportation_report:add'] = 'New Vacation Travel Form';

// Departure headers.
$string['vacation_travel:transportation_report:departure:header:student'] = 'Student';
$string['vacation_travel:transportation_report:departure:header:dorm'] = 'Dorm';
$string['vacation_travel:transportation_report:departure:header:destination'] = 'Destination';
$string['vacation_travel:transportation_report:departure:header:phone'] = 'Phone Number';
$string['vacation_travel:transportation_report:departure:header:mxtransportation'] = 'School Transportation';
$string['vacation_travel:transportation_report:departure:header:type'] = 'Type';
$string['vacation_travel:transportation_report:departure:header:site'] = 'Airport / Station / Stop';
$string['vacation_travel:transportation_report:departure:header:details'] = 'Details / Driver';
$string['vacation_travel:transportation_report:departure:header:carrier'] = 'Airline / Company';
$string['vacation_travel:transportation_report:departure:header:number'] = 'Flight / Bus / Train Number';
$string['vacation_travel:transportation_report:departure:header:datetime'] = 'Date and Time';
$string['vacation_travel:transportation_report:departure:header:international'] = 'International Flight';
$string['vacation_travel:transportation_report:departure:header:timemodified'] = 'Last Modified';
$string['vacation_travel:transportation_report:departure:header:email'] = 'Email';

// Return headers.
$string['vacation_travel:transportation_report:return:header:student'] = 'Student';
$string['vacation_travel:transportation_report:return:header:dorm'] = 'Dorm';
$string['vacation_travel:transportation_report:return:header:destination'] = 'Destination';
$string['vacation_travel:transportation_report:return:header:phone'] = 'Phone Number';
$string['vacation_travel:transportation_report:return:header:mxtransportation'] = 'School Transportation';
$string['vacation_travel:transportation_report:return:header:type'] = 'Type';
$string['vacation_travel:transportation_report:return:header:site'] = 'Airport / Station / Stop';
$string['vacation_travel:transportation_report:return:header:details'] = 'Details / Driver';
$string['vacation_travel:transportation_report:return:header:carrier'] = 'Airline / Company';
$string['vacation_travel:transportation_report:return:header:number'] = 'Transportation Number';
$string['vacation_travel:transportation_report:return:header:datetime'] = 'Date and Time';
$string['vacation_travel:transportation_report:return:header:international'] = 'Clearing Customs in Boston';
$string['vacation_travel:transportation_report:return:header:timemodified'] = 'Last Modified';
$string['vacation_travel:transportation_report:return:header:email'] = 'Email';

// Cells.
$string['vacation_travel:transportation_report:cell:site_other'] = 'Other';

/*
 * ================
 * Deans' Permission
 * ================
 */
$string['deans_permission'] = 'Deans\' Permission';

/* Deans permission Form */
$string['deans_permission:form'] = 'Deans\' Permission Form';
$string['deans_permission:form:title'] = 'Deans\' Permission Form for {$a}';

// Information.
$string['deans_permission:form:info'] = 'Information';
$string['deans_permission:form:info:student'] = 'Student';
$string['deans_permission:form:info:event'] = 'Event type';
$string['deans_permission:form:info:event_info'] = 'Event information';
$string['deans_permission:form:info:sport'] = 'What sport do you play at MX?';
$string['deans_permission:form:info:missing_sports'] = 'Will you be missing a practice or a game?';
$string['deans_permission:form:info:missing_studyhours'] = 'Will you be missing study hours?';
$string['deans_permission:form:info:missing_class'] = 'Will you be missing classes?';
$string['deans_permission:form:info:times_away'] = 'When will you be away?';
// Notifications.
$string['deans_permission:form:success'] = 'Deans\' Permission Form Submitted Successfully';


/* Deans permission report */
$string['deans_permission:report'] = 'Deans\' Permission Report';
$string['deans_permission:report:title'] = 'Deans\' Permission Report';

// Headers.
$string['deans_permission:report:header:student'] = 'Student';
$string['deans_permission:report:header:event'] = 'Event Type';
$string['deans_permission:report:header:event_info'] = 'Event Info';
$string['deans_permission:report:header:sport'] = 'Sport Played';
$string['deans_permission:report:header:missing'] = 'Missing';
$string['deans_permission:report:header:times_away'] = 'Dates';
$string['deans_permission:report:header:parent_perm'] = 'Parent Permission?';
$string['deans_permission:report:header:sports_perm'] = 'Miss Sports';
$string['deans_permission:report:header:studyhours_perm'] = 'Miss Study Hours';
$string['deans_permission:report:header:class_perm'] = 'Miss Class';
$string['deans_permission:report:header:comment'] = 'Comment';
$string['deans_permission:report:header:dean_perm'] = 'Approved?';
$string['deans_permission:report:header:form_submitted'] = 'Submitted';

// Filters.
$string['deans_permission:report:approved:true'] = 'Approved';
$string['deans_permission:report:approved:false'] = 'Under Review';
$string['deans_permission:report:event:all'] = 'All Event Types';

$string['deans_permission:report:delete:success'] = 'Record Deleted Succesfully';
$string['deans_permission:report:delete:failure'] = 'Failed to Delete Record';

// Buttons.
$string['deans_permission:report:add'] = 'New Deans\' Permission Form';

/* Deans Permissions Preferences */
$string['deans_permission:preferences'] = 'Deans\' Permission Preferences';
$string['deans_permission:preferences:title'] = 'Deans\' Permission Preferences';
$string['deans_permission:preferences:deans_email'] = 'Deans\' Permission Form Submitted Notification';
$string['deans_permission:preferences:deans_email:deans_email_address'] = 'Deans\' Email Address';
$string['deans_permission:preferences:deans_email:submitted_tags'] = 'Available tags for deans\' permission form submission email';
$string['deans_permission:preferences:deans_email:submitted_subject'] = 'Deans\' permission submission form email subject';
$string['deans_permission:preferences:deans_email:submitted_body'] = 'Deans\' permission submission form email body';
$string['deans_permission:preferences:sports_email'] = 'Sports Review Permission Request Email';
$string['deans_permission:preferences:sports_email:sports_email_address'] = 'Athletic Director Email Address';
$string['deans_permission:preferences:sports_email:sports_tags'] = 'Available tags for deans\' permission form sports review request email';
$string['deans_permission:preferences:sports_email:sports_subject'] = 'Sports review request subject';
$string['deans_permission:preferences:sports_email:sports_body'] = 'Sports review request body';
$string['deans_permission:preferences:class_email'] = 'Class Review Permission Request Email';
$string['deans_permission:preferences:class_email:class_email_address'] = 'Academic Director Email Address';
$string['deans_permission:preferences:class_email:class_tags'] = 'Available tags for deans\' permission form class review request email';
$string['deans_permission:preferences:class_email:class_subject'] = 'Class review request subject';
$string['deans_permission:preferences:class_email:class_body'] = 'Class review request body';
$string['deans_permission:preferences:approved_email'] = 'Deans\' Permission Form Approved Email Notification';
$string['deans_permission:preferences:approved_email:info'] = 'Note:';
$string['deans_permission:preferences:approved_email:note'] = 'If the form is approved, this email will go to the student, the advisor
												   of the student, and, if the student is missing study hours, that student\'s head of house.';
$string['deans_permission:preferences:approved_email:approved_tags'] = 'Available tags for deans\' permission form approved email';
$string['deans_permission:preferences:approved_email:approved_subject'] = 'Deans\' permission form approved form email subject';
$string['deans_permission:preferences:approved_email:approved_body'] = 'Deans\' permission form approved email body';
$string['deans_permission:preferences:update:success'] = 'Deans\' Permission Preferences Updated Succesfully';

$string['deans_permission:preferences:add_event'] = 'New Event Type Option';

$string['deans_permission:event_report'] = 'Deans\' Permission Event Types';
$string['deans_permission:event_report:title'] = 'Deans\' Permission Event Types';
$string['deans_permission:event_report:header:event_name'] = 'Event Type';

$string['deans_permission:event_edit'] = 'Create or Edit a Deans\' Permission Form Event Type';
$string['deans_permission:event_edit:title'] = 'Create or Edit a Deans\' Permission Form Event Type';
$string['deans_permission:event_edit:success'] = 'Event Type Added';
$string['deans_permission:event_edit:event_name'] = 'Event Type Name';
$string['deans_permission:event_edit:info'] = 'Info';
$string['deans_permission:event_edit:info:event_name'] = 'Event Type Name';

/*
 * ================
 * Healthpass.
 * ================
 */

 /* Healthpass Intake Form.  */

 // Form Headers.
 $string['healthpass'] = "COVIDPass";
 $string['healthpass:form'] = 'COVIDPass Form';
 $string['healthpass:form:health_info'] = 'COVIDPass Information';

 // Fields Labels.
 $string['healthpass:form:health_info:name'] = 'Name';
 $string['healthpass:form:health_info:body_temperature'] = 'What is your body temperature?';
 $string['healthpass:form:health_info:health_info0'] = "Have you been in contact with anyone who has or is suspected to have COVID19?";
 $string['healthpass:form:health_info:health_info1'] = "Have you or has anyone you live with recently been tested for COVID19?";
 $string['healthpass:form:health_info:health_info2'] = "Have you taken any fever reducing medications today? (i.e., Tylenol, Ibuprofen)";
 $string['healthpass:form:symptoms'] = "Do you have:";
 $string['healthpass:form:symptoms:symptom0'] = "a fever?";
 $string['healthpass:form:symptoms:symptom1'] = "a sore throat?";
 $string['healthpass:form:symptoms:symptom2'] = "a new cough (not related to a chronic condition)?";
 $string['healthpass:form:symptoms:symptom3'] = "diarrhea (new or worsening)?";
 $string['healthpass:form:symptoms:symptom4'] = "body aches?";
 $string['healthpass:form:symptoms:symptom5'] = "a sudden loss of sense of taste or smell?";
 $string['healthpass:form:symptoms:symptom6'] = "shortness of breath?";
 $string['healthpass:form:symptoms:symptom7'] = "a headache?";

 // Form Reponses.
 $string['healthpass:form:success:approved'] = "COVIDPass Approved";
 $string['healthpass:form:success:denied'] = "COVIDPass Denied";
 $string['healthpass:form:error:no_symptoms_logic'] = "You cannot click 'I have no symptoms' if you have this symptom";
 $string['healthpass:form:error:unset_symptom'] = "You must answer here or click 'I have no symptoms'";
 $string['healthpass:form:no_symptoms_button'] = "I have no symptoms";

 /* Healthpass Report.  */

 // Report Headers.
 $string['healthpass:report'] = "Student COVIDPass Report";
 $string['healthpass:report:header:userid'] = "Name";
 $string['healthpass:report:header:status'] = "Form Status";
 $string['healthpass:report:header:body_temperature'] = "Body Temperature";
 $string['healthpass:report:header:symptoms'] = "Symptoms";
 $string['healthpass:report:header:override_status'] = "Override Status";
 $string['healthpass:report:header:comment'] = "Comment";
 $string['healthpass:report:header:time_submitted'] = "Most Recent Submitted";

 // Filters.
 $string['healthpass:report:status:all'] = 'All';
 $string['healthpass:report:status:approved'] = 'Approved';
 $string['healthpass:report:status:denied'] = 'Denied';
 $string['healthpass:report:status:submitted'] = 'Submitted';
 $string['healthpass:report:status:unsubmitted'] = 'Unsubmitted';
 $string['healthpass:report:user_type:students'] = 'Students';
 $string['healthpass:report:user_type:faculty'] = 'Faculty';
 $string['healthpass:report:user_type:staff'] = 'Staff';


 // Report Buttons.
 $string['healthpass:report:add'] = 'New COVIDPass Form';
 $string['healthpass:report:review_button'] = 'Review';
 $string['healthpass:report:override_button'] = 'Override';
 $string['healthpass:report:undo_override_button'] = 'Undo Override';
 $string['healthpass:report:under_review'] = 'Under Review';
 $string['healthpass:report:overridden'] = 'Overridden';
 $string['healthpass:report:remind'] = 'Notify Unsubmitted';
 $string['healthpass:report:override_email_button'] = 'Notify';


 /* Healthpass Preferences.  */

 // Form Headers.
 $string['healthpass:preferences'] = 'COVIDPass Preferences';
 $string['healthpass:preferences:preferences'] = 'Preferences';
 $string['healthpass:preferences:healthcenter_notification'] = 'Healthcenter Email Notifications';
 $string['healthpass:preferences:submitted_notifications'] = 'COVIDpass Email Notifications';
 $string['healthpass:preferences:unsubmitted_notifications'] = 'COVIDpass Reminder Notifications';

 // Field Labels.
 $string['healthpass:preferences:preferences:healthpass_enabled'] = 'Enable COVIDPass';
 $string['healthpass:preferences:preferences:reset_time'] = 'At what time should COVIDPass reset? (default midnight)';
 $string['healthpass:preferences:preferences:max_body_temp'] = 'Highest allowable body temperature';

 $string['healthpass:preferences:healthcenter_notification:healthcenter_notification_enabled'] = 'Enable email notifications to the healthcenter when a COVIDpass is denied';
 $string['healthpass:preferences:healthcenter_notification:healthcenter_email_address'] = 'Healthcenter email';
 $string['healthpass:preferences:healthcenter_notification:healthcenter_tags'] = 'Available tags for COVIDpass healthcenter notification email';
 $string['healthpass:preferences:healthcenter_notification:healthcenter_subject'] = 'Healthcenter notification email subject';
 $string['healthpass:preferences:healthcenter_notification:healthcenter_body'] = 'Healthcenter notification email body';

 $string['healthpass:preferences:submitted_notifications:approved_tags'] = 'Available tags for COVIDpass approved email';
 $string['healthpass:preferences:submitted_notifications:approved_subject'] = 'COVIDpass approved email subject';
 $string['healthpass:preferences:submitted_notifications:approved_body'] = 'COVIDpass approved email body';

 $string['healthpass:preferences:submitted_notifications:denied_tags'] = 'Available tags for COVIDpass denied email';
 $string['healthpass:preferences:submitted_notifications:denied_subject'] = 'COVIDpass denied email subject';
 $string['healthpass:preferences:submitted_notifications:denied_body'] = 'COVIDpass denied email body';

 $string['healthpass:preferences:submitted_notifications:overridden_tags'] = 'Available tags for COVIDpass overridden email';
 $string['healthpass:preferences:submitted_notifications:overridden_subject'] = 'COVIDpass overridden email subject';
 $string['healthpass:preferences:submitted_notifications:overridden_body'] = 'COVIDpass overridden email body';

 $string['healthpass:preferences:unsubmitted_notifications:days_before_reminder'] = 'Number of days until the user can be sent a reminder email';
 $string['healthpass:preferences:unsubmitted_notifications:unsubmitted_tags'] = 'Available tags for COVIDpass reminder email';
 $string['healthpass:preferences:unsubmitted_notifications:unsubmitted_subject'] = 'COVIDpass reminder email subject';
 $string['healthpass:preferences:unsubmitted_notifications:unsubmitted_body'] = 'COVIDpass reminder email body';

 // Form Responses.
 $string['healthpass:preferences:success'] = 'COVIDPass Preferences Updated';
 $string['healthpass:preferences:error:not_numeric'] = 'You must enter a number here';

 /* Healthpass Symptoms and Health_info. */
 $string['healthpass:health_info0'] = 'In contact with suspected COVID19';
 $string['healthpass:health_info1'] = 'Recently tested';
 $string['healthpass:health_info2'] = 'Took fever reducing medications';

 $string['healthpass:symptom0'] = 'Fever';
 $string['healthpass:symptom1'] = 'Sore Throat';
 $string['healthpass:symptom2'] = 'Cough';
 $string['healthpass:symptom3'] = 'Diarrhea';
 $string['healthpass:symptom4'] = 'Body Aches';
 $string['healthpass:symptom5'] = 'Loss of Sense';
 $string['healthpass:symptom6'] = 'Shortness of Breath';
 $string['healthpass:symptom7'] = 'Headache';
