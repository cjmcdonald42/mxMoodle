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
 * Database updgrade steps for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_mxschool_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2018061003) {

        // Define table local_mxschool_weekend to be dropped.
        $table = new xmldb_table('local_mxschool_weekend');

        // Conditionally launch drop table for local_mxschool_weekend.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table local_mxschool_weekend to be created.
        $table = new xmldb_table('local_mxschool_weekend');

        // Adding fields to table local_mxschool_weekend.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('sunday_time', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, 'Open');
        $table->add_field('start_time', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('end_time', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_mxschool_weekend.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for local_mxschool_weekend.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018061003, 'local', 'mxschool');
    }

    if ($oldversion < 2018061105) {

        // Define field permissions_line to be added to local_mxschool_dorm.
        $table = new xmldb_table('local_mxschool_dorm');
        $field = new xmldb_field('permissions_line', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, 'available');

        // Conditionally launch add field permissions_line.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018061105, 'local', 'mxschool');
    }

    if ($oldversion < 2018061202) {

        // Define table local_mxschool_weekend_form to be dropped.
        $table = new xmldb_table('local_mxschool_weekend_form');

        // Conditionally launch drop table for local_mxschool_weekend_form.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table local_mxschool_weekend_form to be created.
        $table = new xmldb_table('local_mxschool_weekend_form');

        // Adding fields to table local_mxschool_weekend_form.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('weekendid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('departure_date_time', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('return_date_time', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('destination', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('transportation', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('phone_number', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('active', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('parent', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('invite', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('approved', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('time_created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('time_modified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_mxschool_weekend_form.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('user', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->add_key('weekend', XMLDB_KEY_FOREIGN, array('weekendid'), 'local_mxschool_weekend', array('id'));

        // Conditionally launch create table for local_mxschool_weekend_form.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018061202, 'local', 'mxschool');
    }

    if ($oldversion < 2018061429) {

        // Define table local_mxschool_comment to be created.
        $table = new xmldb_table('local_mxschool_comment');

        // Adding fields to table local_mxschool_comment.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('weekendid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('dormid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('comment', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_mxschool_comment.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('weekend', XMLDB_KEY_FOREIGN, array('weekendid'), 'local_mxschool_weekend', array('id'));
        $table->add_key('dorm', XMLDB_KEY_FOREIGN, array('dormid'), 'local_mxschool_dorm', array('id'));

        // Conditionally launch create table for local_mxschool_comment.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018061429, 'local', 'mxschool');
    }

    if ($oldversion < 2018061504) {

        // Define table local_mxschool_notification to be created.
        $table = new xmldb_table('local_mxschool_notification');

        // Adding fields to table local_mxschool_notification.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('class', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('subject', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('body_html', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_mxschool_notification.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('class', XMLDB_KEY_UNIQUE, array('class'));

        // Conditionally launch create table for local_mxschool_notification.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018061504, 'local', 'mxschool');
    }

    if ($oldversion < 2018061806) {

        // Define table local_mxschool_vehicles to be created.
        $table = new xmldb_table('local_mxschool_vehicle');

        // Adding fields to table local_mxschool_vehicles.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('license_date', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('make', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('model', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('color', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('registration', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_mxschool_vehicles.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('student', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'user', array('id'));

        // Conditionally launch create table for local_mxschool_vehicles.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018061806, 'local', 'mxschool');
    }

    if ($oldversion < 2018061807) {

        // Define field deleted to be added to local_mxschool_vehicle.
        $table = new xmldb_table('local_mxschool_vehicle');
        $field = new xmldb_field('deleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'userid');

        // Conditionally launch add field deleted.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018061807, 'local', 'mxschool');
    }

    if ($oldversion < 2018061904) {

        // Define key student (foreign) to be dropped form local_mxschool_vehicle.
        $table = new xmldb_table('local_mxschool_vehicle');
        $key = new xmldb_key('student', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'user', array('id'));

        // Launch drop key student.
        $dbman->drop_key($table, $key);

        // Define key student (foreign) to be added to local_mxschool_vehicle.
        $table = new xmldb_table('local_mxschool_vehicle');
        $key = new xmldb_key('student', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Launch add key student.
        $dbman->add_key($table, $key);

        // Define field may_approve_signout to be added to local_mxschool_faculty.
        $table = new xmldb_table('local_mxschool_faculty');
        $field = new xmldb_field('may_approve_signout', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null, 'faculty_code');

        // Conditionally launch add field may_approve_signout.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018061904, 'local', 'mxschool');
    }

    if ($oldversion < 2018062101) {

        // Define table local_mxschool_passenger to be dropped.
        $table = new xmldb_table('local_mxschool_passenger');

        // Conditionally launch drop table for local_mxschool_passenger.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table local_mxschool_esignout to be dropped.
        $table = new xmldb_table('local_mxschool_esignout');

        // Conditionally launch drop table for local_mxschool_esignout.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table local_mxschool_esignout to be created.
        $table = new xmldb_table('local_mxschool_esignout');

        // Adding fields to table local_mxschool_esignout.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('driverid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('approverid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('deleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('destination', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('departure_time', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('sign_in_time', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('time_created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('time_modified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_mxschool_esignout.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('student', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->add_key('driver', XMLDB_KEY_FOREIGN, array('driverid'), 'local_mxschool_esignout', array('id'));
        $table->add_key('approver', XMLDB_KEY_FOREIGN, array('approverid'), 'user', array('id'));

        // Conditionally launch create table for local_mxschool_esignout.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018062101, 'local', 'mxschool');
    }

    if ($oldversion < 2018062506) {

        // Define field passengers to be added to local_mxschool_esignout.
        $table = new xmldb_table('local_mxschool_esignout');
        $field = new xmldb_field('passengers', XMLDB_TYPE_TEXT, null, null, null, null, null, 'deleted');

        // Conditionally launch add field passengers.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018062506, 'local', 'mxschool');
    }

    if ($oldversion < 2018062510) {

        // Define field type to be added to local_mxschool_esignout.
        $table = new xmldb_table('local_mxschool_esignout');
        $field = new xmldb_field('type', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, 'deleted');

        // Conditionally launch add field type.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018062510, 'local', 'mxschool');
    }

    if ($oldversion < 2018062600) {

        // Define field license_date to be added to local_mxschool_permissions.
        $table = new xmldb_table('local_mxschool_permissions');
        $field = new xmldb_field('license_date', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'overnight');

        // Conditionally launch add field license_date.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018062600, 'local', 'mxschool');
    }

    if ($oldversion < 2018062602) {

        // Define field license_date to be dropped from local_mxschool_vehicle.
        $table = new xmldb_table('local_mxschool_vehicle');
        $field = new xmldb_field('license_date');

        // Conditionally launch drop field license_date.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018062602, 'local', 'mxschool');
    }

    if ($oldversion < 2018062812) {

        // Define key advisor (foreign) to be dropped form local_mxschool_student.
        $table = new xmldb_table('local_mxschool_student');
        $key = new xmldb_key('advisor', XMLDB_KEY_FOREIGN, array('advisorid'), 'local_mxschool_faculty', array('id'));

        // Launch drop key advisor.
        $dbman->drop_key($table, $key);

        // Define key advisor (foreign) to be added to local_mxschool_student.
        $table = new xmldb_table('local_mxschool_student');
        $key = new xmldb_key('advisor', XMLDB_KEY_FOREIGN, array('advisorid'), 'user', array('id'));

        // Launch add key advisor.
        $dbman->add_key($table, $key);

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018062812, 'local', 'mxschool');
    }

    if ($oldversion < 2018071001) {

        // Define field may_approve_signout to be dropped from local_mxschool_faculty.
        $table = new xmldb_table('local_mxschool_faculty');
        $field = new xmldb_field('may_approve_signout');

        // Conditionally launch drop field may_approve_signout.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field advisory_available to be dropped from local_mxschool_faculty.
        $table = new xmldb_table('local_mxschool_faculty');
        $field = new xmldb_field('advisory_available');

        // Conditionally launch drop field advisory_available.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field advisory_closing to be dropped from local_mxschool_faculty.
        $table = new xmldb_table('local_mxschool_faculty');
        $field = new xmldb_field('advisory_closing');

        // Conditionally launch drop field advisory_closing.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field may_approve_signout to be added to local_mxschool_faculty.
        $table = new xmldb_table('local_mxschool_faculty');
        $field = new xmldb_field('may_approve_signout', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'faculty_code');

        // Conditionally launch add field may_approve_signout.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field advisory_available to be added to local_mxschool_faculty.
        $table = new xmldb_table('local_mxschool_faculty');
        $field = new xmldb_field('advisory_available', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'may_approve_signout');

        // Conditionally launch add field advisory_available.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field advisory_closing to be added to local_mxschool_faculty.
        $table = new xmldb_table('local_mxschool_faculty');
        $field = new xmldb_field('advisory_closing', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'advisory_available');

        // Conditionally launch add field advisory_closing.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018071001, 'local', 'mxschool');
    }

    if ($oldversion < 2018071010) {

        // Define table local_mxschool_adv_selection to be created.
        $table = new xmldb_table('local_mxschool_adv_selection');

        // Adding fields to table local_mxschool_adv_selection.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('keep_current', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('option1id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('option2id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('option3id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('option4id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('option5id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('selectedid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('time_created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('time_modified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_mxschool_adv_selection.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('user', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'user', array('id'));
        $table->add_key('option1', XMLDB_KEY_FOREIGN, array('option1id'), 'user', array('id'));
        $table->add_key('option2', XMLDB_KEY_FOREIGN, array('option2id'), 'user', array('id'));
        $table->add_key('option3', XMLDB_KEY_FOREIGN, array('option3id'), 'user', array('id'));
        $table->add_key('option4', XMLDB_KEY_FOREIGN, array('option4id'), 'user', array('id'));
        $table->add_key('option5', XMLDB_KEY_FOREIGN, array('option5id'), 'user', array('id'));
        $table->add_key('selected', XMLDB_KEY_FOREIGN, array('selectedid'), 'user', array('id'));

        // Conditionally launch create table for local_mxschool_adv_selection.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018071010, 'local', 'mxschool');
    }

    if ($oldversion < 2018071108) {
        set_config('weekend_form_instructions_top', 'Please fill out the form entirely. Your form should be submitted to your Head of House no later than <b>10:30 PM on Friday</b>.<br>All relevant phone calls giving permission should also be received by Friday at 10:30 PM <i>(Voice mail messages are OK; Email messages are NOT)</i>.', 'local_mxschool');
        set_config('weekend_form_instructions_bottom', 'You may not leave for the weekend until you see your name on the \'OK\' list.<br>Permission phone calls should be addressed to <b>{hoh}</b> @ <b>{permissionsline}</b>.<br>If your plans change, you must get permission from <b>{hoh}</b>. <b>Remember to sign out.</b>', 'local_mxschool');
        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018071108, 'local', 'mxschool');
    }

    if ($oldversion < 2018071110) {
        set_config('advisor_form_closing_warning', 'Your current advisor\'s advisory is closing, so you must provide choices for a new advisor.', 'local_mxschool');
        set_config('advisor_form_instructions', 'Please rank you top five advisor choices in descending order. You may rank less than five if your final choice is your current advisor.', 'local_mxschool');
        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018071110, 'local', 'mxschool');
    }

    if ($oldversion < 2018071301) {

        // Define key permissions (foreign-unique) to be dropped form local_mxschool_student.
        $table = new xmldb_table('local_mxschool_student');
        $key = new xmldb_key('permissions', XMLDB_KEY_FOREIGN_UNIQUE, array('permissionsid'), 'local_mxschool_permissions', array('id'));

        // Launch drop key permissions.
        $dbman->drop_key($table, $key);

        // Define field permissionsid to be dropped from local_mxschool_student.
        $table = new xmldb_table('local_mxschool_student');
        $field = new xmldb_field('permissionsid');

        // Conditionally launch drop field permissionsid.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018071301, 'local', 'mxschool');
    }

    if ($oldversion < 2018071304) {
        set_config('esignout_form_warning_nopassengers', 'Your permissions indicate that you may not drive passengers.', 'local_mxschool');
        set_config('esignout_form_warning_needparent', 'Your permissions indicate that you need a call from your parent.', 'local_mxschool');
        set_config('esignout_form_warning_onlyspecific', 'Your permissions indicate that you may only be the passenger of the following drivers: ', 'local_mxschool');
        set_config('esignout_form_confirmation', 'Have you recieved the required permissions?', 'local_mxschool');

        set_config('esignout_notification_warning_driver', 'None.', 'local_mxschool');
        set_config('esignout_notification_warning_any', 'None.', 'local_mxschool');
        set_config('esignout_notification_warning_parent', 'This student requires parent permission to be the passenger of another student.', 'local_mxschool');
        set_config('esignout_notification_warning_specific', 'This student only has permission to the be the passenger of the following drivers: ', 'local_mxschool');
        set_config('esignout_notification_warning_over21', 'This student does NOT have permission to be the passenger of anyone under 21.', 'local_mxschool');

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018071304, 'local', 'mxschool');
    }

    if ($oldversion < 2018071504) {

        // Define table local_mxschool_rooming to be created.
        $table = new xmldb_table('local_mxschool_rooming');

        // Adding fields to table local_mxschool_rooming.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('room_type', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('dormmate1id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('dormmate2id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('dormmate3id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('dormmate4id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('dormmate5id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('dormmate6id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('has_lived_in_double', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('preferred_roommateid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('time_created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('time_modified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_mxschool_rooming.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('user', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'user', array('id'));
        $table->add_key('dormmate1', XMLDB_KEY_FOREIGN, array('dormmate1id'), 'user', array('id'));
        $table->add_key('dormmate2', XMLDB_KEY_FOREIGN, array('dormmate2id'), 'user', array('id'));
        $table->add_key('dormmate3', XMLDB_KEY_FOREIGN, array('dormmate3id'), 'user', array('id'));
        $table->add_key('dormmate4', XMLDB_KEY_FOREIGN, array('dormmate4id'), 'user', array('id'));
        $table->add_key('dormmate5', XMLDB_KEY_FOREIGN, array('dormmate5id'), 'user', array('id'));
        $table->add_key('dormmate6', XMLDB_KEY_FOREIGN, array('dormmate6id'), 'user', array('id'));
        $table->add_key('preferred_roommate', XMLDB_KEY_FOREIGN, array('preferred_roommateid'), 'user', array('id'));

        // Conditionally launch create table for local_mxschool_rooming.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018071504, 'local', 'mxschool');
    }

    if ($oldversion < 2018071514) {
        set_config('rooming_form_checkbox_instructions', 'Check if you have lived in a one-room double in the past.', 'local_mxschool');
        set_config('rooming_form_roommate_instructions', 'Because there are several one-room doubles on campus, there are years when students who prefer to be in a single must live in a double. If you have not lived in a one-room double before, please indicate with whom you would want to live if placed in one.', 'local_mxschool');

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018071514, 'local', 'mxschool');
    }

    if ($oldversion < 2018071602) {
        unset_config('rooming_form_checkbox_instructions', 'local_mxschool');

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018071602, 'local', 'mxschool');
    }

    return true;
}
