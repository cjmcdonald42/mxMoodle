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
 * English language strings for Middlesex's Peer Tutoring Subplugin.
 *
 * @package     local_peertutoring
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
$string['pluginname'] = 'Middlesex Peer Tutoring';

/* Capabilities. */
$string['peertutoring:manage_preferences'] = 'Middlesex School Peer Tutoring: View and manage peer tutoring preferences';
$string['peertutoring:manage_tutoring'] = 'Middlesex School Peer Tutoring: View and manage tutoring records';


/* Settings. */
$string['email_settings'] = 'Peer Tutoring Email Settings';
$string['email_settings:peertutoringmanager_email'] = 'Peer tutoring manager email';
$string['email_settings:peertutoringmanager_email:description'] = 'The email address to send summary emails for peer tutoring.';
$string['email_settings:peertutoringmanager_addressee'] = 'Peer tutoring manager addressee name';
$string['email_settings:peertutoringmanager_addressee:description'] = 'The name to use when addressing the peer tutoring manager in a email notification.';

// Index Pages.
$string['indexes:peertutoring'] = 'Peer Tutoring Index';


/* Tasks. */
$string['task:summary_email'] = 'Daily summary email to peer tutoring manager';



/*
 * ==============
 * Peer Tutoring.
 * ==============
 */
$string['peertutoring'] = 'Peer Tutoring';


/* Preferences. */
$string['preferences'] = 'Peer Tutoring Preferences';

// Email Notifications.
$string['preferences:notifications'] = 'Email Notifications';
$string['preferences:notifications:tags'] = 'Available Tags for Daily Summary Email';
$string['preferences:notifications:subject'] = 'Subject for Daily Summary Email';
$string['preferences:notifications:body'] = 'Body for Daily Summary Email';

// Notification.
$string['preferences:update:success'] = 'Peer Tutoring Preferences Saved Successfully';


/* Tutor Report. */
$string['tutor_report'] = 'Tutor Report';

// Filter.
$string['tutor_report:add'] = 'Add a Tutor';

// Headers.
$string['tutor_report:header:tutor'] = 'Name';


/* Tutor Edit. */
$string['tutor_edit'] = 'Edit Tutor Record';

// Tutor Information.
$string['tutor_edit:tutor'] = 'Tutor Information';
$string['tutor_edit:tutor:student'] = 'Student';
$string['tutor_edit:tutor:departments'] = 'Approved Departments';
$string['tutor_edit:tutor:departments:no_selection'] = 'No Departments Selected';
$string['tutor_edit:tutor:departments:placeholder'] = 'Search Departments';


/* Tutor notifications. */
$string['tutor:create:success'] = 'Tutor Record Created Successfully';
$string['tutor:update:success'] = 'Tutor Record Updated Successfully';
$string['tutor:delete:success'] = 'Tutor Record Deleted Successfully';
$string['tutor:delete:failure'] = 'Tutor Record Not Found for Deletion';


/* Department Report. */
$string['department_report'] = 'Department Report';

// Filter.
$string['department_report:add'] = 'Add a Department';

// Headers.
$string['department_report:header:name'] = 'Name';


/* Department Edit. */
$string['department_edit'] = 'Edit Department Record';

// Department Information.
$string['department_edit:department'] = 'Department Information';
$string['department_edit:department:name'] = 'Department Name';


/* Department Notifications. */
$string['department:create:success'] = 'Department Record Created Successfully';
$string['department:update:success'] = 'Department Record Updated Successfully';
$string['department:delete:success'] = 'Department Record Deleted Successfully';
$string['department:delete:failure'] = 'Department Record Not Found for Deletion';


/* Course Report. */
$string['course_report'] = 'Course Report';

// Filter.
$string['course_report:add'] = 'Add a Course';

// Headers.
$string['course_report:header:department'] = 'Department';
$string['course_report:header:name'] = 'Name';


/* Course Edit. */
$string['course_edit'] = 'Edit Course Record';

// Course Information.
$string['course_edit:course'] = 'Course Information';
$string['course_edit:course:department'] = 'Department';
$string['course_edit:course:name'] = 'Course Name';


/* Course Notifications. */
$string['course:create:success'] = 'Course Record Created Successfully';
$string['course:update:success'] = 'Course Record Updated Successfully';
$string['course:delete:success'] = 'Course Record Deleted Successfully';
$string['course:delete:failure'] = 'Course Record Not Found for Deletion';


/* Type Report. */
$string['type_report'] = 'Type Report';

// Filter.
$string['type_report:add'] = 'Add a Type';

// Headers.
$string['type_report:header:displaytext'] = 'Text';


/* Type Edit. */
$string['type_edit'] = 'Edit Type Record';

// Type Information.
$string['type_edit:type'] = 'Type Information';
$string['type_edit:type:displaytext'] = 'Text';


/* Type Notifications. */
$string['type:create:success'] = 'Type Record Created Successfully';
$string['type:update:success'] = 'Type Record Updated Successfully';
$string['type:delete:success'] = 'Type Record Deleted Successfully';
$string['type:delete:failure'] = 'Type Record Not Found for Deletion';


/* Rating Report. */
$string['rating_report'] = 'Effectiveness Rating Report';

// Filter.
$string['rating_report:add'] = 'Add an Effectiveness Rating';

// Headers.
$string['rating_report:header:displaytext'] = 'Text';


/* Rating Edit. */
$string['rating_edit'] = 'Edit Effectiveness Rating Record';
$string['rating_edit:rating'] = 'Effectiveness Rating Information';
$string['rating_edit:rating:displaytext'] = 'Text';


/* Rating Notifications. */
$string['rating:create:success'] = 'Effectiveness Rating Record Created Successfully';
$string['rating:update:success'] = 'Effectiveness Rating Record Updated Successfully';
$string['rating:delete:success'] = 'Effectiveness Rating Record Deleted Successfully';
$string['rating:delete:failure'] = 'Effectiveness Rating Record Not Found for Deletion';


/* Peer Tutoring Form. */
$string['form'] = 'Peer Tutoring Form';
$string['form_title'] = 'Peer Tutoring Form for {$a}';

// General Information.
$string['form:info'] = 'General Information';
$string['form:info:tutor'] = 'Tutor';
$string['form:info:tutoringdate'] = 'Date';
$string['form:info:student'] = 'Student Tutored';

// Details.
$string['form:details'] = 'Details';
$string['form:details:department'] = 'Subject Tutored';
$string['form:details:course'] = 'Course Tutored';
$string['form:details:topic'] = 'Topic of Tutoring Session';
$string['form:details:type'] = 'Type of Help Requested';
$string['form:details:type_select:other'] = 'Other (please specify)';
$string['form:details:rating'] = 'Effectiveness of Session';
$string['form:details:notes'] = 'Notes';

// Errors.
$string['form:error:nodepartment'] = 'You must specify a subject.';
$string['form:error:nocourse'] = 'You must specify a course.';
$string['form:error:notopic'] = 'You must specify a topic.';
$string['form:error:notype'] = 'You must specify a type.';
$string['form:error:norating'] = 'You must specify a rating.';

// Notifications.
$string['session:success'] = 'Peer Tutoring Session Submitted Successfully';
$string['session:delete:success'] = 'Peer Tutoring Session Record Deleted Successfully';
$string['session:delete:failure'] = 'Peer Tutoring Session Record Not Found for Deletion';


/* Peer Tutoring Report. */
$string['report'] = 'Peer Tutoring Report';

// Filter.
$string['report:select_tutor:all'] = 'All Tutors';
$string['report:select_department:all'] = 'All Departments';
$string['report:select_type:all'] = 'All Types';
$string['report:select_type:other'] = 'Other';
$string['report:select_date:all'] = 'All Dates';
$string['report:add'] = 'New Peer Tutoring Record';

// Headers.
$string['report:header:tutor'] = 'Tutor';
$string['report:header:tutoringdate'] = 'Date';
$string['report:header:student'] = 'Student';
$string['report:header:department'] = 'Department';
$string['report:header:course'] = 'Course';
$string['report:header:topic'] = 'Topic';
$string['report:header:type'] = 'Type';
$string['report:header:rating'] = 'Effectiveness Rating';
$string['report:header:notes'] = 'Notes';
