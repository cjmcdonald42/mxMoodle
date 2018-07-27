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
 * English language strings for Middlesex School's Peer Tutoring Subplugin.
 *
 * @package    local_peertutoring
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Middlesex School Peer Tutoring';

/* General */

// Capabilities.
$string['peertutoring:manage_preferences'] = 'Middlesex School Peer Tutoring: View and manage peer tutoring preferences';
$string['peertutoring:manage_tutoring'] = 'Middlesex School Peer Tutoring: View and manage tutoring records';
$string['peertutoring:enter_tutoring'] = 'Middlesex School Peer Tutoring: Create tutoring records';

// Settings Pages.
$string['settings'] = 'Peer Tutoring Settings';
$string['peertutoradmin_email'] = 'Peer Tutor Admin Email';
$string['peertutoradmin_email_description'] = 'The email address to send summary emails for peer tutoring.';

$string['peertutoring_index'] = 'Peer Tutoring Index';

// Tasks.
$string['task_summary_email'] = 'Daily summary email to peer tutor admin';

// Notifications.
$string['preferences_edit_success'] = 'Peer Tutoring Preferences Saved Successfully';
$string['tutor_create_success'] = 'Tutor Record Created Successfully';
$string['tutor_edit_success'] = 'Tutor Record Updated Successfully';
$string['department_create_success'] = 'Department Record Created Successfully';
$string['department_edit_success'] = 'Department Record Updated Successfully';
$string['course_create_success'] = 'Course Record Created Successfully';
$string['course_edit_success'] = 'Course Record Updated Successfully';
$string['type_create_success'] = 'Type Record Created Successfully';
$string['type_edit_success'] = 'Type Record Updated Successfully';
$string['rating_create_success'] = 'Effectiveness Rating Record Created Successfully';
$string['rating_edit_success'] = 'Effectiveness Rating Record Updated Successfully';
$string['tutoring_form_success'] = 'Tutoring Form Updated Successfully';

$string['table_delete_failure'] = 'Table Not Found for Record Deletion';
$string['department_delete_success'] = 'Department Record Deleted Successfully';
$string['department_delete_failure'] = 'Department Record Not Found for Deletion';
$string['course_delete_success'] = 'Course Record Deleted Successfully';
$string['course_delete_failure'] = 'Course Record Not Found for Deletion';
$string['type_delete_success'] = 'Type Record Deleted Successfully';
$string['type_delete_failure'] = 'Type Record Not Found for Deletion';
$string['rating_delete_success'] = 'Effectiveness Rating Record Deleted Successfully';
$string['rating_delete_failure'] = 'Effectiveness Rating Record Not Found for Deletion';
$string['session_delete_success'] = 'Peer Tutoring Session Record Deleted Successfully';
$string['session_delete_failure'] = 'Peer Tutoring Session Record Not Found for Deletion';

/* Peer Tutoring */
$string['peertutoring'] = 'Peer Tutoring';
$string['preferences'] = 'Peer Tutoring Preferences';
$string['tutor_report'] = 'Tutor Report';
$string['department_report'] = 'Department Report';
$string['course_report'] = 'Course Report';
$string['type_report'] = 'Type Report';
$string['rating_report'] = 'Effectiveness Rating Report';
$string['tutor_edit'] = 'Edit Tutor Record';
$string['department_edit'] = 'Edit Department Record';
$string['course_edit'] = 'Edit Course Record';
$string['type_edit'] = 'Edit Type Record';
$string['rating_edit'] = 'Edit Effectiveness Rating Record';
$string['tutoring_form'] = 'Peer Tutoring Form';
$string['tutoring_report'] = 'Peer Tutoring Report';

// Preferences.
$string['preferences_header_notifications'] = 'Email Notifications';
$string['preferences_notifications_available'] = 'Available Tags for Daily Summary Email';
$string['preferences_notifications_subject'] = 'Subject for Daily Summary Email';
$string['preferences_notifications_body'] = 'Body for Daily Summary Email';

// Tutor Report.
$string['tutor_report_add'] = 'Add a Tutor';

$string['tutor_report_header_tutor'] = 'Name';

// Department Report.
$string['department_report_add'] = 'Add a Department';

$string['department_report_header_name'] = 'Name';

// Course Report.
$string['course_report_add'] = 'Add a Course';

$string['course_report_header_department'] = 'Department';
$string['course_report_header_name'] = 'Name';

// Type Report.
$string['type_report_add'] = 'Add a Type';

$string['type_report_header_displaytext'] = 'Text';

// Rating Report.
$string['rating_report_add'] = 'Add an Effectiveness Rating';

$string['rating_report_header_displaytext'] = 'Text';

// Tutor Edit.
$string['tutor_edit_header_tutor'] = 'Tutor Information';
$string['tutor_edit_tutor_student'] = 'Student';
$string['tutor_edit_tutor_departments'] = 'Approved Departments';

$string['tutor_edit_form_departments_noselection'] = 'No Departments Selected';
$string['tutor_edit_form_departments_placeholder'] = 'Search Departments';

// Department Edit.
$string['department_edit_header_department'] = 'Department Information';
$string['department_edit_department_name'] = 'Department Name';

// Course Edit.
$string['course_edit_header_course'] = 'Course Information';
$string['course_edit_course_department'] = 'Department';
$string['course_edit_course_name'] = 'Course Name';

// Type Edit.
$string['type_edit_header_type'] = 'Type Information';
$string['type_edit_type_displaytext'] = 'Text';

// Rating Edit.
$string['rating_edit_header_rating'] = 'Effectiveness Rating Information';
$string['rating_edit_rating_displaytext'] = 'Text';

// Tutoring Form.
$string['tutoring_form_header_info'] = 'General Information';
$string['tutoring_form_header_details'] = 'Details';
$string['tutoring_form_info_tutor'] = 'Tutor';
$string['tutoring_form_info_tutoringdate'] = 'Date';
$string['tutoring_form_info_student'] = 'Student Tutored';
$string['tutoring_form_details_department'] = 'Subject Tutored';
$string['tutoring_form_details_course'] = 'Course Tutored';
$string['tutoring_form_details_topic'] = 'Topic of Tutoring Session';
$string['tutoring_form_details_type'] = 'Type of Help Requested';
$string['tutoring_form_details_rating'] = 'Effectiveness of Session';
$string['tutoring_form_details_notes'] = 'Notes';

$string['tutoring_form_department_default'] = 'Select';
$string['tutoring_form_course_default'] = 'Select';
$string['tutoring_form_type_default'] = 'Select';
$string['tutoring_form_rating_default'] = 'Select';

$string['tutoring_form_error_nodepartment'] = 'You must specify a subject.';
$string['tutoring_form_error_nocourse'] = 'You must specify a course.';
$string['tutoring_form_error_notopic'] = 'You must specify a topic.';
$string['tutoring_form_error_notype'] = 'You must specify a type.';
$string['tutoring_form_error_norating'] = 'You must specify a rating.';

// Tutoring Report.
$string['tutoring_report_select_tutor_all'] = 'All Tutors';
$string['tutoring_report_select_department_all'] = 'All Departments';
$string['tutoring_report_select_type_all'] = 'All Types';
$string['tutoring_report_select_date_all'] = 'All Dates';
$string['tutoring_report_add'] = 'New Peer Tutoring Record';

$string['tutoring_report_header_tutor'] = 'Tutor';
$string['tutoring_report_header_tutoringdate'] = 'Date';
$string['tutoring_report_header_student'] = 'Student';
$string['tutoring_report_header_department'] = 'Department';
$string['tutoring_report_header_course'] = 'Course';
$string['tutoring_report_header_topic'] = 'Topic';
$string['tutoring_report_header_type'] = 'Type';
$string['tutoring_report_header_rating'] = 'Effectiveness Rating';
$string['tutoring_report_header_notes'] = 'Notes';
