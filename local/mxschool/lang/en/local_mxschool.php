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
 * English Language file for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Middlesex School';

/* General */
$string['print'] = 'Print';
$string['email_button_default'] = 'Send Email';
$string['email_button_sent'] = 'Email Sent';
$string['legend_header'] = 'Legend';
$string['report_header_actions'] = 'Actions';
$string['report_delete_warning'] = 'Are you sure want to delete this record?';
$string['report_select_dorm'] = 'All Houses';
$string['day_0'] = 'Sunday';
$string['day_1'] = 'Monday';
$string['day_2'] = 'Tuesday';
$string['day_3'] = 'Wednesday';
$string['day_4'] = 'Thursday';
$string['day_5'] = 'Friday';
$string['day_6'] = 'Saturday';
$string['first_semester'] = 'First Semester';
$string['second_semester'] = 'Second Semester';

// Capabilities.
$string['mxschool:manage_students'] = 'Middlesex School: View and manage student data';
$string['mxschool:manage_faculty'] = 'Middlesex School: View and manage faculty data';
$string['mxschool:manage_dorms'] = 'Middlesex School: View and manage dorm data';
$string['mxschool:view_checkin'] = 'Middlesex School: View checkin sheets';
$string['mxschool:manage_checkin'] = 'Middlesex School: Manage checkin preferences';
$string['mxschool:manage_weekend'] = 'Middlesex School: Manage weekend forms';

// Settings Pages.
$string['mxschool_category'] = 'Middlesex School';
$string['indexes'] = 'Index Pages';
$string['main_index'] = 'Middlesex School Index';
$string['user_management_index'] = 'User Management Index';
$string['checkin_index'] = 'Checkin Sheets Index';

// Events.
$string['event_page_visited'] = 'page visited event';

// Notifications.
$string['student_edit_success'] = 'Student Record Updated Successfully';
$string['parent_edit_success'] = 'Parent Record Updated Successfully';
$string['faculty_edit_success'] = 'Faculty Record Updated Successfully';
$string['dorm_edit_success'] = 'Dorm Record Updated Successfully';
$string['checkin_preferences_edit_success'] = 'Check-in Preferences Saved Successfully';
$string['weekend_form_success'] = 'Weekend Form Submitted Successfully';
$string['weekend_comment_form_success'] = 'Weekend Comment Updated Successfully';

$string['parent_delete_success'] = 'Parent Record Deleted Successfully';
$string['parent_delete_failure'] = 'Parent Record Not Found for Deletion';
$string['dorm_delete_success'] = 'Dorm Record Deleted Successfully';
$string['dorm_delete_failure'] = 'Dorm Record Not Found for Deletion';
$string['weekend_form_delete_success'] = 'Weekend Form Record Deleted Successfully';
$string['weekend_form_delete_failure'] = 'Weekend Form Record Not Found for Deletion';


/* User Management. */
$string['user_management'] = 'User Management';
$string['student_report'] = 'Student Report';
$string['faculty_report'] = 'Faculty Report';
$string['dorm_report'] = 'Dorm Report';
$string['student_edit'] = 'Edit Student Record';
$string['parent_edit'] = 'Edit Parent Record';
$string['faculty_edit'] = 'Edit Faculty Record';
$string['dorm_edit'] = 'Edit Dorm Record';

// Student Report.
$string['student_report_type_students'] = 'Student Report';
$string['student_report_type_permissions'] = 'Permissions Report';
$string['student_report_type_parents'] = 'Parent Report';

$string['student_report_students_header_student'] = 'Name';
$string['student_report_students_header_grade'] = 'Grade';
$string['student_report_students_header_advisor'] = 'Advisor';
$string['student_report_students_header_dorm'] = 'Dorm';
$string['student_report_students_header_room'] = 'Room';
$string['student_report_students_header_phone'] = 'Phone Number';
$string['student_report_students_header_birthday'] = 'Birthday';

$string['student_report_permissions_header_student'] = 'Name';
$string['student_report_permissions_header_overnight'] = 'Overnight';
$string['student_report_permissions_header_riding'] = 'May Ride With';
$string['student_report_permissions_header_comment'] = 'Riding Comment';
$string['student_report_permissions_header_rideshare'] = 'May Use Rideshare?';
$string['student_report_permissions_header_boston'] = 'May Drive to Boston?';
$string['student_report_permissions_header_town'] = 'May Drive to Town?';
$string['student_report_permissions_header_passengers'] = 'May Drive Passengers?';
$string['student_report_permissions_header_swimcompetent'] = 'Competent Swimmer?';
$string['student_report_permissions_header_swimallowed'] = 'Allowed to Swim?';
$string['student_report_permissions_header_boatallowed'] = 'Allowed in Boats?';

$string['student_report_parents_header_student'] = 'Student Name';
$string['student_report_parents_header_parent'] = 'Parent Name';
$string['student_report_parents_header_primaryparent'] = 'Primary';
$string['student_report_parents_header_relationship'] = 'Relationship';
$string['student_report_parents_header_homephone'] = 'Home Phone';
$string['student_report_parents_header_cellphone'] = 'Cell Phone';
$string['student_report_parents_header_workphone'] = 'Work Phone';
$string['student_report_parents_header_email'] = 'Email';

$string['parent_report_add'] = 'New Parent';

// Faculty Report.
$string['faculty_report_header_name'] = 'Name';
$string['faculty_report_header_dorm'] = 'Dorm';
$string['faculty_report_header_advisoryavailable'] = 'Advisory Available';
$string['faculty_report_header_advisoryclosing'] = 'Advisory Closing';

// Dorm Report.
$string['dorm_report_add'] = 'New Dorm';

$string['dorm_report_header_name'] = 'Name';
$string['dorm_report_header_abbreviation'] = 'Abbreviation';
$string['dorm_report_header_hoh'] = 'Head of House';
$string['dorm_report_header_permissionsline'] = 'Permissions Line';
$string['dorm_report_header_type'] = 'Type';
$string['dorm_report_header_gender'] = 'Gender';
$string['dorm_report_header_available'] = 'Available';

// Student Edit.
$string['student_edit_header_student'] = 'Student Information';
$string['student_edit_header_permissions'] = 'Student Permissions';

$string['student_edit_student_firstname'] = 'First Name';
$string['student_edit_student_middlename'] = 'Middle Name';
$string['student_edit_student_lastname'] = 'Last Name';
$string['student_edit_student_alternatename'] = 'Alternate Name';
$string['student_edit_student_email'] = 'Email';
$string['student_edit_student_admissionyear'] = 'Year of Admission';
$string['student_edit_student_grade'] = 'Grade';
$string['student_edit_student_gender'] = 'Gender';
$string['student_edit_student_advisor'] = 'Advisor';
$string['student_edit_student_isboarder'] = 'Boarder/Day Student';
$string['student_edit_student_isboardernextyear'] = 'Boarder/Day Student Next Year';
$string['student_edit_student_dorm'] = 'Dorm';
$string['student_edit_student_room'] = 'Room';
$string['student_edit_student_phonenumber'] = 'Phone Number';
$string['student_edit_student_birthday'] = 'Birthday';

$string['student_edit_permissions_overnight'] = 'Overnight';
$string['student_edit_permissions_riding'] = 'May Ride With';
$string['student_edit_permissions_comment'] = 'Riding Comment';
$string['student_edit_permissions_rideshare'] = 'May Use Rideshare?';
$string['student_edit_permissions_boston'] = 'May Drive to Boston?';
$string['student_edit_permissions_town'] = 'May Drive to Town?';
$string['student_edit_permissions_passengers'] = 'May Drive Passengers?';
$string['student_edit_permissions_swimcompetent'] = 'Competent Swimmer?';
$string['student_edit_permissions_swimallowed'] = 'Allowed to Swim?';
$string['student_edit_permissions_boatallowed'] = 'Allowed in Boats?';

// Parent Edit.
$string['parent_edit_header_parent'] = 'Parent Information';

$string['parent_edit_parent_student'] = 'Child';
$string['parent_edit_parent_name'] = 'Parent Name';
$string['parent_edit_parent_isprimary'] = 'Primary Parent?';
$string['parent_edit_parent_relationship'] = 'Relationship to Child';
$string['parent_edit_parent_homephone'] = 'Parent Home Phone';
$string['parent_edit_parent_cellphone'] = 'Parent Cell Phone';
$string['parent_edit_parent_workphone'] = 'Parent Work Phone';
$string['parent_edit_parent_email'] = 'Parent Email';

// Faculty Edit.
$string['faculty_edit_header_faculty'] = 'Faculty Information';

$string['faculty_edit_faculty_firstname'] = 'First Name';
$string['faculty_edit_faculty_middlename'] = 'Middle Name';
$string['faculty_edit_faculty_lastname'] = 'Last Name';
$string['faculty_edit_faculty_alternatename'] = 'Alternate Name';
$string['faculty_edit_faculty_email'] = 'Email';
$string['faculty_edit_faculty_facultycode'] = 'Faculty Code';
$string['faculty_edit_faculty_dorm'] = 'Dorm';
$string['faculty_edit_faculty_advisoryavailable'] = 'Advisory Available';
$string['faculty_edit_faculty_advisoryclosing'] = 'Advisory Closing';

// Dorm Edit.
$string['dorm_edit_header_dorm'] = 'Dorm Information';

$string['dorm_edit_dorm_name'] = 'Name';
$string['dorm_edit_dorm_abbreviation'] = 'Abbreviation';
$string['dorm_edit_dorm_hoh'] = 'Head of House';
$string['dorm_edit_dorm_permissionsline'] = 'Permissions Line';
$string['dorm_edit_dorm_type'] = 'Type';
$string['dorm_edit_dorm_gender'] = 'Gender';
$string['dorm_edit_dorm_available'] = 'Available';


/* Check-in Sheets. */
$string['checkin'] = 'Check-In Sheets and Weekend Forms';
$string['checkin_preferences'] = 'Check-In Sheets Preferences';
$string['generic_report'] = 'Check-In Sheet';
$string['weekday_report'] = 'Weekday Check-In Sheet';
$string['weekend_form'] = 'Weekend Form';
$string['weekend_report'] = 'Weekend Check-In Sheet';
$string['weekend_calculator'] = 'Weekend Calculator';

// Check-in Preferences.
$string['checkin_preferences_header_dates'] = 'Opening and Closing Dates';
$string['checkin_preferences_header_weekends'] = 'Weekend Types';
$string['checkin_preferences_header_notifications'] = 'Weekend Form Email Notifications';
$string['checkin_preferences_dates_dormsopen'] = 'Dorms Open On: ';
$string['checkin_preferences_dates_secondsemester'] = 'Second Semester Starts On: ';
$string['checkin_preferences_dates_dormsclose'] = 'Dorms Close On: ';
$string['checkin_preferences_weekends_label'] = 'Saturday {$a}';
$string['checkin_preferences_notifications_submittedsubject'] = 'Subject for Weekend Form Submitted Email';
$string['checkin_preferences_notifications_submittedbody'] = 'Body for Weekend Form Submitted Email';
$string['checkin_preferences_notifications_approvedsubject'] = 'Subject for Weekend Form Approved Email';
$string['checkin_preferences_notifications_approvedbody'] = 'Body for Weekend Form Approved Email';

// Generic Check-in Sheet.
$string['generic_report_title'] = '{$a}Check-In Sheet for __________';
$string['generic_report_header_student'] = 'Name';
$string['generic_report_header_room'] = 'Room #';
$string['generic_report_header_grade'] = 'Grade';
$string['generic_report_header_checkin'] = '&emsp;&emsp;';

// Weekday Check-in Sheet.
$string['weekday_report_title'] = '{$a}Check-In Sheet for the Week of __________';
$string['weekday_report_header_student'] = 'Name';
$string['weekday_report_header_room'] = 'Room #';
$string['weekday_report_header_grade'] = 'Grade';
$string['weekday_report_header_early'] = 'Early';
$string['weekday_report_header_late'] = 'Late';

// Weekend Form.
$string['weekend_form_topdescription'] = 'Please fill out the form entirely. Your form should be submitted to your Head of House no later than <b>10:30 PM on Friday</b>.<br>All relevant phone calls giving permission should also be received by Friday at 10:30 PM <i>(Voice mail messages are OK; Email messages are NOT)</i>.';
$string['weekend_form_bottomdescription'] = 'You may not leave for the weekend until you see your name on the \'OK\' list.<br>Permission phone calls should be addressed to <b>{$a->hoh}</b> @ <b>{$a->permissionsline}</b>.<br>If your plans change, you must get permission from <b>{$a->hoh}</b>. <b>Remember to sign out.</b>';

$string['weekend_form_dorm'] = 'Dorm';
$string['weekend_form_student'] = 'Student';
$string['weekend_form_departuretime'] = 'Departure Date and Time';
$string['weekend_form_returntime'] = 'Return Date and Time';
$string['weekend_form_destination'] = 'Your Destination';
$string['weekend_form_transportation'] = 'Transportation by';
$string['weekend_form_phone'] = 'Phone Number<br>(even if you are going home)';

$string['weekend_form_error_outoforder'] = 'You must select a return date and time after your departure date and time.';
$string['weekend_form_error_notinweekend'] = 'You must select a departure date within a valid weekend.';
$string['weekend_form_error_indifferentweekends'] = 'You must select a return date in the same weekend as your departure date.';

// Weekend Check-in Sheet.
$string['weekend_report_title'] = '{$a->dorm}Check-In Sheet for the Weekend of {$a->weekend}';
$string['weekend_report_select_submitted_all'] = 'All';
$string['weekend_report_select_submitted_true'] = 'Weekend Form';
$string['weekend_report_select_submitted_false'] = 'No Weekend Form';
$string['weekend_report_add'] = 'New Weekend Form';

$string['weekend_report_header_student'] = 'Name';
$string['weekend_report_header_room'] = 'Room #';
$string['weekend_report_header_grade'] = 'Grade';
$string['weekend_report_header_early'] = 'Early';
$string['weekend_report_header_late'] = 'Late';
$string['weekend_report_header_clean'] = 'Room Clean?';
$string['weekend_report_header_parent'] = 'Parent?';
$string['weekend_report_header_invite'] = 'Invite?';
$string['weekend_report_header_approved'] = 'Approved?';
$string['weekend_report_header_destination'] = 'Destination';
$string['weekend_report_header_transportation'] = 'Transportation';
$string['weekend_report_header_phone'] = 'Phone #';
$string['weekend_report_header_departurereturn'] = 'Departure Time<br>Return Time';

$string['weekend_comment_form_comment'] = 'Comments';

// Weekend Calculator.
$string['weekend_calculator_report_title'] = 'Weekend Calculator{$a}';
$string['weekend_calculator_report_header_student'] = 'Name';
$string['weekend_calculator_report_header_grade'] = 'Grade';
$string['weekend_calculator_report_header_total'] = 'Total';
$string['weekend_calculator_report_header_allowed'] = 'Allowed';
$string['weekend_report_abbreviation_offcampus'] = 'X';
$string['weekend_report_abbreviation_free'] = 'free';
$string['weekend_report_abbreviation_closed'] = 'camp';
$string['weekend_report_abbreviation_unlimited'] = 'ALL';

$string['weekend_report_legend_0_left'] = 'No weekends left';
$string['weekend_report_legend_1_left'] = '1 weekend left';
$string['weekend_report_legend_2_left'] = '2 weekends left';
$string['weekend_report_legend_3_left'] = '3+ weekends left';
$string['weekend_report_legend_offcampus'] = 'Student Off Campus';
$string['weekend_report_legend_free'] = 'Free weekend';
$string['weekend_report_legend_closed'] = 'Campus weekend';
