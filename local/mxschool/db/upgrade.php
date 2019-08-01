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
 * Database updgrade steps for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_mxschool_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2019031000) {

        // Define field default_departure_time to be added to local_mxschool_vt_site.
        $table = new xmldb_table('local_mxschool_vt_site');
        $field = new xmldb_field('default_departure_time', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'enabled_return');

        // Conditionally launch add field default_departure_time.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field default_return_time to be added to local_mxschool_vt_site.
        $table = new xmldb_table('local_mxschool_vt_site');
        $field = new xmldb_field('default_return_time', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'default_departure_time');

        // Conditionally launch add field default_return_time.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2019031000, 'local', 'mxschool');
    }

    if ($oldversion < 2019052201) {

        // Define key return (foreign-unique) to be dropped form local_mxschool_vt_trip.
        $table = new xmldb_table('local_mxschool_vt_trip');
        $key = new xmldb_key('return', XMLDB_KEY_FOREIGN_UNIQUE, array('returnid'), 'local_mxschool_vt_transport', array('id'));

        // Launch drop key return.
        $dbman->drop_key($table, $key);

        // Changing nullability of field returnid on table local_mxschool_vt_trip to null.
        $table = new xmldb_table('local_mxschool_vt_trip');
        $field = new xmldb_field('returnid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'departureid');

        // Launch change of nullability for field returnid.
        $dbman->change_field_notnull($table, $field);

        // Define key return (foreign-unique) to be added to local_mxschool_vt_trip.
        $table = new xmldb_table('local_mxschool_vt_trip');
        $key = new xmldb_key('return', XMLDB_KEY_FOREIGN_UNIQUE, array('returnid'), 'local_mxschool_vt_transport', array('id'));

        // Launch add key return.
        $dbman->add_key($table, $key);

        $DB->set_field('local_mxschool_vt_trip', 'returnid', null, array('returnid' => 0));

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2019052201, 'local', 'mxschool');
    }

    if ($oldversion < 2019070300) {

        // Define table local_mxschool_subpackage to be created.
        $table = new xmldb_table('local_mxschool_subpackage');

        // Adding fields to table local_mxschool_subpackage.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('package', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, 'mxschool');
        $table->add_field('subpackage', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('pages', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table local_mxschool_subpackage.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for local_mxschool_subpackage.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2019070300, 'local', 'mxschool');
    }

    if ($oldversion < 2019070301) {

        // Changing nullability of field subpackage on table local_mxschool_subpackage to null.
        $table = new xmldb_table('local_mxschool_subpackage');
        $field = new xmldb_field('subpackage', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'package');

        // Launch change of nullability for field subpackage.
        $dbman->change_field_notnull($table, $field);

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2019070301, 'local', 'mxschool');
    }

    if ($oldversion < 2019070302) {
        $subpackages = array(
            array('subpackage' => 'user_management', 'pages' => json_encode(array(
                'student_report' => 'student_report.php', 'faculty_report' => 'faculty_report.php',
                'dorm_report' => 'dorm_report.php'
            ))),
            array('subpackage' => 'checkin', 'pages' => json_encode(array(
                'preferences' => 'preferences.php', 'generic_report' => 'generic_report.php',
                'weekday_report' => 'weekday_report.php', 'weekend_form' => 'weekend_enter.php',
                'weekend_report' => 'weekend_report.php', 'weekend_calculator' => 'weekend_calculator.php'
            ))),
            array('subpackage' => 'esignout', 'pages' => json_encode(array(
                'preferences' => 'preferences.php', 'vehicle_report' => 'vehicle_report.php', 'form' => 'esignout_enter.php',
                'report' => 'esignout_report.php'
            ))),
            array('subpackage' => 'advisor_selection', 'pages' => json_encode(array(
                'preferences' => 'preferences.php', 'form' => 'advisor_enter.php', 'report' => 'advisor_report.php'
            ))),
            array('subpackage' => 'rooming', 'pages' => json_encode(array(
                'preferences' => 'preferences.php', 'form' => 'rooming_enter.php', 'report' => 'rooming_report.php'
            ))),
            array('subpackage' => 'vacation_travel', 'pages' => json_encode(array(
                'preferences' => 'preferences.php', 'form' => 'vacation_enter.php', 'report' => 'vacation_report.php',
                'transportation_report' => 'transportation_report.php'
            )))
        );
        foreach ($subpackages as $subpackage) {
            $DB->insert_record('local_mxschool_subpackage', (object) $subpackage);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2019070302, 'local', 'mxschool');
    }

    if ($oldversion < 2019070402) {
        // Add vehicle_report page to user_management subpackage.
        $usermanagement = $DB->get_record('local_mxschool_subpackage', array('subpackage' => 'user_management'));
        $usermanagement->pages = json_encode(array(
            'student_report' => 'student_report.php', 'faculty_report' => 'faculty_report.php', 'dorm_report' => 'dorm_report.php',
            'vehicle_report' => 'vehicle_report.php',
        ));
        $DB->update_record('local_mxschool_subpackage', $usermanagement);

        // Remove vehicle_report page from esignout subpackage.
        $esignout = $DB->get_record('local_mxschool_subpackage', array('subpackage' => 'esignout'));
        $esignout->pages = json_encode(array(
            'preferences' => 'preferences.php', 'form' => 'esignout_enter.php', 'report' => 'esignout_report.php'
        ));
        $DB->update_record('local_mxschool_subpackage', $esignout);

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2019070402, 'local', 'mxschool');
    }

    if ($oldversion < 2019070701) {
        // Define table local_mxschool_esignout to be dropped.
        $table = new xmldb_table('local_mxschool_esignout');

        // Conditionally launch drop table for local_mxschool_esignout.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Drop esignout from subpackage table.
        $DB->delete_records('local_mxschool_subpackage', array('subpackage' => 'esignout'));

        // Drop esignout notification email.
        $DB->delete_records('local_mxschool_notification', array('class' => 'esignout_submitted'));

        // Unset esignout configs.
        unset_config('esignout_edit_window', 'local_mxschool');
        unset_config('esignout_trip_window', 'local_mxschool');
        unset_config('esignout_form_enabled', 'local_mxschool');
        unset_config('esignout_form_ipenabled', 'local_mxschool');
        unset_config('esignout_form_iperror', 'local_mxschool');
        unset_config('esignout_report_iperror', 'local_mxschool');
        unset_config('esignout_form_instructions_passenger', 'local_mxschool');
        unset_config('esignout_form_instructions_bottom', 'local_mxschool');
        unset_config('esignout_form_warning_nopassengers', 'local_mxschool');
        unset_config('esignout_form_warning_needparent', 'local_mxschool');
        unset_config('esignout_form_warning_onlyspecific', 'local_mxschool');
        unset_config('esignout_form_confirmation', 'local_mxschool');

        unset_config('esignout_notification_warning_irregular', 'local_mxschool');
        unset_config('esignout_notification_warning_driver', 'local_mxschool');
        unset_config('esignout_notification_warning_any', 'local_mxschool');
        unset_config('esignout_notification_warning_parent', 'local_mxschool');
        unset_config('esignout_notification_warning_specific', 'local_mxschool');
        unset_config('esignout_notification_warning_over21', 'local_mxschool');

        unset_config('esignout_form_instructions', 'local_mxschool');
        unset_config('school_ip', 'local_mxschool');

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2019070701, 'local', 'mxschool');
    }

    if ($oldversion < 2019071700) {
        // Add picture_import page to user_management subpackage.
        $usermanagement = $DB->get_record('local_mxschool_subpackage', array('subpackage' => 'user_management'));
        $usermanagement->pages = json_encode(array(
            'student_report' => 'student_report.php', 'faculty_report' => 'faculty_report.php', 'dorm_report' => 'dorm_report.php',
            'vehicle_report' => 'vehicle_report.php', 'picture_import' => 'picture_import.php'
        ));
        $DB->update_record('local_mxschool_subpackage', $usermanagement);

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2019071700, 'local', 'mxschool');
    }

    if ($oldversion < 2019071703) {

        // Define field picture_filename to be added to local_mxschool_student.
        $table = new xmldb_table('local_mxschool_student');
        $field = new xmldb_field('picture_filename', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'birthday');

        // Conditionally launch add field picture_filename.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2019071703, 'local', 'mxschool');
    }

    if ($oldversion < 2019072201) {

        // Define key user (foreign_unique) to be dropped form local_mxschool_adv_selection.
        $table = new xmldb_table('local_mxschool_adv_selection');
        $key = new xmldb_key('user', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'user', array('id'));

        // Launch drop key user.
        $dbman->drop_key($table, $key);

        // Define key weekend (foreign) to be dropped form local_mxschool_comment.
        $table = new xmldb_table('local_mxschool_comment');
        $key = new xmldb_key('weekend', XMLDB_KEY_FOREIGN, array('weekendid'), 'local_mxschool_weekend', array('id'));

        // Launch drop key weekend.
        $dbman->drop_key($table, $key);

        // Define key dorm (foreign) to be dropped form local_mxschool_comment.
        $table = new xmldb_table('local_mxschool_comment');
        $key = new xmldb_key('dorm', XMLDB_KEY_FOREIGN, array('dormid'), 'local_mxschool_dorm', array('id'));

        // Launch drop key dorm.
        $dbman->drop_key($table, $key);

        // Define key hoh (foreign) to be dropped form local_mxschool_dorm.
        $table = new xmldb_table('local_mxschool_dorm');
        $key = new xmldb_key('hoh', XMLDB_KEY_FOREIGN, array('hohid'), 'user', array('id'));

        // Launch drop key hoh.
        $dbman->drop_key($table, $key);

        // Define key user (foreign_unique) to be dropped form local_mxschool_faculty.
        $table = new xmldb_table('local_mxschool_faculty');
        $key = new xmldb_key('user', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'user', array('id'));

        // Launch drop key user.
        $dbman->drop_key($table, $key);

        // Define key student (foreign) to be dropped form local_mxschool_parent.
        $table = new xmldb_table('local_mxschool_parent');
        $key = new xmldb_key('student', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Launch drop key student.
        $dbman->drop_key($table, $key);

        // Define key student (foreign_unique) to be dropped form local_mxschool_permissions.
        $table = new xmldb_table('local_mxschool_permissions');
        $key = new xmldb_key('student', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'user', array('id'));

        // Launch drop key student.
        $dbman->drop_key($table, $key);

        // Define key user (foreign_unique) to be dropped form local_mxschool_rooming.
        $table = new xmldb_table('local_mxschool_rooming');
        $key = new xmldb_key('user', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'user', array('id'));

        // Launch drop key user.
        $dbman->drop_key($table, $key);

        // Define key dormmate1 (foreign) to be dropped form local_mxschool_rooming.
        $table = new xmldb_table('local_mxschool_rooming');
        $key = new xmldb_key('dormmate1', XMLDB_KEY_FOREIGN, array('dormmate1id'), 'user', array('id'));

        // Launch drop key dormmate1.
        $dbman->drop_key($table, $key);

        // Define key dormmate2 (foreign) to be dropped form local_mxschool_rooming.
        $table = new xmldb_table('local_mxschool_rooming');
        $key = new xmldb_key('dormmate2', XMLDB_KEY_FOREIGN, array('dormmate2id'), 'user', array('id'));

        // Launch drop key dormmate2.
        $dbman->drop_key($table, $key);

        // Define key dormmate3 (foreign) to be dropped form local_mxschool_rooming.
        $table = new xmldb_table('local_mxschool_rooming');
        $key = new xmldb_key('dormmate3', XMLDB_KEY_FOREIGN, array('dormmate3id'), 'user', array('id'));

        // Launch drop key dormmate3.
        $dbman->drop_key($table, $key);

        // Define key dormmate4 (foreign) to be dropped form local_mxschool_rooming.
        $table = new xmldb_table('local_mxschool_rooming');
        $key = new xmldb_key('dormmate4', XMLDB_KEY_FOREIGN, array('dormmate4id'), 'user', array('id'));

        // Launch drop key dormmate4.
        $dbman->drop_key($table, $key);

        // Define key dormmate5 (foreign) to be dropped form local_mxschool_rooming.
        $table = new xmldb_table('local_mxschool_rooming');
        $key = new xmldb_key('dormmate5', XMLDB_KEY_FOREIGN, array('dormmate5id'), 'user', array('id'));

        // Launch drop key dormmate5.
        $dbman->drop_key($table, $key);

        // Define key dormmate6 (foreign) to be dropped form local_mxschool_rooming.
        $table = new xmldb_table('local_mxschool_rooming');
        $key = new xmldb_key('dormmate6', XMLDB_KEY_FOREIGN, array('dormmate6id'), 'user', array('id'));

        // Launch drop key dormmate6.
        $dbman->drop_key($table, $key);

        // Define key preferred_roommate (foreign) to be dropped form local_mxschool_rooming.
        $table = new xmldb_table('local_mxschool_rooming');
        $key = new xmldb_key('preferred_roommate', XMLDB_KEY_FOREIGN, array('preferred_roommateid'), 'user', array('id'));

        // Launch drop key preferred_roommate.
        $dbman->drop_key($table, $key);

        // Define key user (foreign_unique) to be dropped form local_mxschool_student.
        $table = new xmldb_table('local_mxschool_student');
        $key = new xmldb_key('user', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'user', array('id'));

        // Launch drop key user.
        $dbman->drop_key($table, $key);

        // Define key dorm (foreign) to be dropped form local_mxschool_student.
        $table = new xmldb_table('local_mxschool_student');
        $key = new xmldb_key('dorm', XMLDB_KEY_FOREIGN, array('dormid'), 'local_mxschool_dorm', array('id'));

        // Launch drop key dorm.
        $dbman->drop_key($table, $key);

        // Define key advisor (foreign) to be dropped form local_mxschool_student.
        $table = new xmldb_table('local_mxschool_student');
        $key = new xmldb_key('advisor', XMLDB_KEY_FOREIGN, array('advisorid'), 'user', array('id'));

        // Launch drop key advisor.
        $dbman->drop_key($table, $key);

        // Define key student (foreign) to be dropped form local_mxschool_vehicle.
        $table = new xmldb_table('local_mxschool_vehicle');
        $key = new xmldb_key('student', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Launch drop key student.
        $dbman->drop_key($table, $key);

        // Define key student (foreign_unique) to be dropped form local_mxschool_vt_trip.
        $table = new xmldb_table('local_mxschool_vt_trip');
        $key = new xmldb_key('student', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'id', array('id'));

        // Launch drop key student.
        $dbman->drop_key($table, $key);

        // Define key departure (foreign_unique) to be dropped form local_mxschool_vt_trip.
        $table = new xmldb_table('local_mxschool_vt_trip');
        $key = new xmldb_key('departure', XMLDB_KEY_FOREIGN_UNIQUE, array('departureid'), 'local_mxschool_vt_transport', array('id'));

        // Launch drop key departure.
        $dbman->drop_key($table, $key);

        // Define key return (foreign_unique) to be dropped form local_mxschool_vt_trip.
        $table = new xmldb_table('local_mxschool_vt_trip');
        $key = new xmldb_key('return', XMLDB_KEY_FOREIGN_UNIQUE, array('returnid'), 'local_mxschool_vt_transport', array('id'));

        // Launch drop key return.
        $dbman->drop_key($table, $key);

        // Define key user (foreign) to be dropped form local_mxschool_weekend_form.
        $table = new xmldb_table('local_mxschool_weekend_form');
        $key = new xmldb_key('user', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Launch drop key user.
        $dbman->drop_key($table, $key);

        // Define key weekend (foreign) to be dropped form local_mxschool_weekend_form.
        $table = new xmldb_table('local_mxschool_weekend_form');
        $key = new xmldb_key('weekend', XMLDB_KEY_FOREIGN, array('weekendid'), 'local_mxschool_weekend', array('id'));

        // Launch drop key weekend.
        $dbman->drop_key($table, $key);

        // Changing the default of field userid on table local_mxschool_adv_selection to drop it.
        $table = new xmldb_table('local_mxschool_adv_selection');
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of default for field userid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field weekendid on table local_mxschool_comment to drop it.
        $table = new xmldb_table('local_mxschool_comment');
        $field = new xmldb_field('weekendid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of default for field weekendid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field dormid on table local_mxschool_comment to drop it.
        $table = new xmldb_table('local_mxschool_comment');
        $field = new xmldb_field('dormid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'weekendid');

        // Launch change of default for field dormid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field hohid on table local_mxschool_dorm to drop it.
        $table = new xmldb_table('local_mxschool_dorm');
        $field = new xmldb_field('hohid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of default for field hohid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field userid on table local_mxschool_faculty to drop it.
        $table = new xmldb_table('local_mxschool_faculty');
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of default for field userid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field userid on table local_mxschool_parent to drop it.
        $table = new xmldb_table('local_mxschool_parent');
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of default for field userid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field userid on table local_mxschool_permissions to drop it.
        $table = new xmldb_table('local_mxschool_permissions');
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of default for field userid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field userid on table local_mxschool_rooming to drop it.
        $table = new xmldb_table('local_mxschool_rooming');
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of default for field userid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field dormmate1id on table local_mxschool_rooming to drop it.
        $table = new xmldb_table('local_mxschool_rooming');
        $field = new xmldb_field('dormmate1id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'room_type');

        // Launch change of default for field dormmate1id.
        $dbman->change_field_default($table, $field);

        // Changing the default of field dormmate2id on table local_mxschool_rooming to drop it.
        $table = new xmldb_table('local_mxschool_rooming');
        $field = new xmldb_field('dormmate2id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'dormmate1id');

        // Launch change of default for field dormmate2id.
        $dbman->change_field_default($table, $field);

        // Changing the default of field dormmate3id on table local_mxschool_rooming to drop it.
        $table = new xmldb_table('local_mxschool_rooming');
        $field = new xmldb_field('dormmate3id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'dormmate2id');

        // Launch change of default for field dormmate3id.
        $dbman->change_field_default($table, $field);

        // Changing the default of field dormmate4id on table local_mxschool_rooming to drop it.
        $table = new xmldb_table('local_mxschool_rooming');
        $field = new xmldb_field('dormmate4id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'dormmate3id');

        // Launch change of default for field dormmate4id.
        $dbman->change_field_default($table, $field);

        // Changing the default of field dormmate5id on table local_mxschool_rooming to drop it.
        $table = new xmldb_table('local_mxschool_rooming');
        $field = new xmldb_field('dormmate5id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'dormmate4id');

        // Launch change of default for field dormmate5id.
        $dbman->change_field_default($table, $field);

        // Changing the default of field dormmate6id on table local_mxschool_rooming to drop it.
        $table = new xmldb_table('local_mxschool_rooming');
        $field = new xmldb_field('dormmate6id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'dormmate5id');

        // Launch change of default for field dormmate6id.
        $dbman->change_field_default($table, $field);

        // Changing the default of field preferred_roommateid on table local_mxschool_rooming to drop it.
        $table = new xmldb_table('local_mxschool_rooming');
        $field = new xmldb_field('preferred_roommateid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'has_lived_in_double');

        // Launch change of default for field preferred_roommateid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field userid on table local_mxschool_student to drop it.
        $table = new xmldb_table('local_mxschool_student');
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of default for field userid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field dormid on table local_mxschool_student to drop it.
        $table = new xmldb_table('local_mxschool_student');
        $field = new xmldb_field('dormid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'userid');

        // Launch change of default for field dormid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field advisorid on table local_mxschool_student to drop it.
        $table = new xmldb_table('local_mxschool_student');
        $field = new xmldb_field('advisorid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'dormid');

        // Launch change of default for field advisorid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field userid on table local_mxschool_vehicle to drop it.
        $table = new xmldb_table('local_mxschool_vehicle');
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of default for field userid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field userid on table local_mxschool_vt_trip to drop it.
        $table = new xmldb_table('local_mxschool_vt_trip');
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of default for field userid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field departureid on table local_mxschool_vt_trip to drop it.
        $table = new xmldb_table('local_mxschool_vt_trip');
        $field = new xmldb_field('departureid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'userid');

        // Launch change of default for field departureid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field returnid on table local_mxschool_vt_trip to drop it.
        $table = new xmldb_table('local_mxschool_vt_trip');
        $field = new xmldb_field('returnid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'departureid');

        // Launch change of default for field returnid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field userid on table local_mxschool_weekend_form to drop it.
        $table = new xmldb_table('local_mxschool_weekend_form');
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of default for field userid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field weekendid on table local_mxschool_weekend_form to drop it.
        $table = new xmldb_table('local_mxschool_weekend_form');
        $field = new xmldb_field('weekendid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'userid');

        // Launch change of default for field weekendid.
        $dbman->change_field_default($table, $field);

        // Define key user (foreign_unique) to be added form local_mxschool_adv_selection.
        $table = new xmldb_table('local_mxschool_adv_selection');
        $key = new xmldb_key('user', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'user', array('id'));

        // Launch add key user.
        $dbman->add_key($table, $key);

        // Define key weekend (foreign) to be added form local_mxschool_comment.
        $table = new xmldb_table('local_mxschool_comment');
        $key = new xmldb_key('weekend', XMLDB_KEY_FOREIGN, array('weekendid'), 'local_mxschool_weekend', array('id'));

        // Launch add key weekend.
        $dbman->add_key($table, $key);

        // Define key dorm (foreign) to be added form local_mxschool_comment.
        $table = new xmldb_table('local_mxschool_comment');
        $key = new xmldb_key('dorm', XMLDB_KEY_FOREIGN, array('dormid'), 'local_mxschool_dorm', array('id'));

        // Launch add key dorm.
        $dbman->add_key($table, $key);

        // Define key hoh (foreign) to be added form local_mxschool_dorm.
        $table = new xmldb_table('local_mxschool_dorm');
        $key = new xmldb_key('hoh', XMLDB_KEY_FOREIGN, array('hohid'), 'user', array('id'));

        // Launch add key hoh.
        $dbman->add_key($table, $key);

        // Define key user (foreign_unique) to be added form local_mxschool_faculty.
        $table = new xmldb_table('local_mxschool_faculty');
        $key = new xmldb_key('user', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'user', array('id'));

        // Launch add key user.
        $dbman->add_key($table, $key);

        // Define key student (foreign) to be added form local_mxschool_parent.
        $table = new xmldb_table('local_mxschool_parent');
        $key = new xmldb_key('student', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Launch add key student.
        $dbman->add_key($table, $key);

        // Define key student (foreign_unique) to be added form local_mxschool_permissions.
        $table = new xmldb_table('local_mxschool_permissions');
        $key = new xmldb_key('student', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'user', array('id'));

        // Launch add key student.
        $dbman->add_key($table, $key);

        // Define key user (foreign_unique) to be added form local_mxschool_rooming.
        $table = new xmldb_table('local_mxschool_rooming');
        $key = new xmldb_key('user', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'user', array('id'));

        // Launch add key user.
        $dbman->add_key($table, $key);

        // Define key dormmate1 (foreign) to be added form local_mxschool_rooming.
        $table = new xmldb_table('local_mxschool_rooming');
        $key = new xmldb_key('dormmate1', XMLDB_KEY_FOREIGN, array('dormmate1id'), 'user', array('id'));

        // Launch add key dormmate1.
        $dbman->add_key($table, $key);

        // Define key dormmate2 (foreign) to be added form local_mxschool_rooming.
        $table = new xmldb_table('local_mxschool_rooming');
        $key = new xmldb_key('dormmate2', XMLDB_KEY_FOREIGN, array('dormmate2id'), 'user', array('id'));

        // Launch add key dormmate2.
        $dbman->add_key($table, $key);

        // Define key dormmate3 (foreign) to be added form local_mxschool_rooming.
        $table = new xmldb_table('local_mxschool_rooming');
        $key = new xmldb_key('dormmate3', XMLDB_KEY_FOREIGN, array('dormmate3id'), 'user', array('id'));

        // Launch add key dormmate3.
        $dbman->add_key($table, $key);

        // Define key dormmate4 (foreign) to be added form local_mxschool_rooming.
        $table = new xmldb_table('local_mxschool_rooming');
        $key = new xmldb_key('dormmate4', XMLDB_KEY_FOREIGN, array('dormmate4id'), 'user', array('id'));

        // Launch add key dormmate4.
        $dbman->add_key($table, $key);

        // Define key dormmate5 (foreign) to be added form local_mxschool_rooming.
        $table = new xmldb_table('local_mxschool_rooming');
        $key = new xmldb_key('dormmate5', XMLDB_KEY_FOREIGN, array('dormmate5id'), 'user', array('id'));

        // Launch add key dormmate5.
        $dbman->add_key($table, $key);

        // Define key dormmate6 (foreign) to be added form local_mxschool_rooming.
        $table = new xmldb_table('local_mxschool_rooming');
        $key = new xmldb_key('dormmate6', XMLDB_KEY_FOREIGN, array('dormmate6id'), 'user', array('id'));

        // Launch add key dormmate6.
        $dbman->add_key($table, $key);

        // Define key preferred_roommate (foreign) to be added form local_mxschool_rooming.
        $table = new xmldb_table('local_mxschool_rooming');
        $key = new xmldb_key('preferred_roommate', XMLDB_KEY_FOREIGN, array('preferred_roommateid'), 'user', array('id'));

        // Launch add key preferred_roommate.
        $dbman->add_key($table, $key);

        // Define key user (foreign_unique) to be added form local_mxschool_student.
        $table = new xmldb_table('local_mxschool_student');
        $key = new xmldb_key('user', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'user', array('id'));

        // Launch add key user.
        $dbman->add_key($table, $key);

        // Define key dorm (foreign) to be added form local_mxschool_student.
        $table = new xmldb_table('local_mxschool_student');
        $key = new xmldb_key('dorm', XMLDB_KEY_FOREIGN, array('dormid'), 'local_mxschool_dorm', array('id'));

        // Launch add key dorm.
        $dbman->add_key($table, $key);

        // Define key advisor (foreign) to be added form local_mxschool_student.
        $table = new xmldb_table('local_mxschool_student');
        $key = new xmldb_key('advisor', XMLDB_KEY_FOREIGN, array('advisorid'), 'user', array('id'));

        // Launch add key advisor.
        $dbman->add_key($table, $key);

        // Define key student (foreign) to be added form local_mxschool_vehicle.
        $table = new xmldb_table('local_mxschool_vehicle');
        $key = new xmldb_key('student', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Launch add key student.
        $dbman->add_key($table, $key);

        // Define key student (foreign_unique) to be added form local_mxschool_vt_trip.
        $table = new xmldb_table('local_mxschool_vt_trip');
        $key = new xmldb_key('student', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'id', array('id'));

        // Launch add key student.
        $dbman->add_key($table, $key);

        // Define key departure (foreign_unique) to be added form local_mxschool_vt_trip.
        $table = new xmldb_table('local_mxschool_vt_trip');
        $key = new xmldb_key('departure', XMLDB_KEY_FOREIGN_UNIQUE, array('departureid'), 'local_mxschool_vt_transport', array('id'));

        // Launch add key departure.
        $dbman->add_key($table, $key);

        // Define key return (foreign_unique) to be added form local_mxschool_vt_trip.
        $table = new xmldb_table('local_mxschool_vt_trip');
        $key = new xmldb_key('return', XMLDB_KEY_FOREIGN_UNIQUE, array('returnid'), 'local_mxschool_vt_transport', array('id'));

        // Launch add key return.
        $dbman->add_key($table, $key);

        // Define key user (foreign) to be added form local_mxschool_weekend_form.
        $table = new xmldb_table('local_mxschool_weekend_form');
        $key = new xmldb_key('user', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Launch add key user.
        $dbman->add_key($table, $key);

        // Define key weekend (foreign) to be added form local_mxschool_weekend_form.
        $table = new xmldb_table('local_mxschool_weekend_form');
        $key = new xmldb_key('weekend', XMLDB_KEY_FOREIGN, array('weekendid'), 'local_mxschool_weekend', array('id'));

        // Launch add key weekend.
        $dbman->add_key($table, $key);

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2019072201, 'local', 'mxschool');
    }

    if ($oldversion < 2019072512) {

        // Define table local_mxschool_subpackage to be dropped.
        $table = new xmldb_table('local_mxschool_subpackage');

        // Conditionally launch drop table for local_mxschool_subpackage.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table local_mxschool_subpackage to be created.
        $table = new xmldb_table('local_mxschool_subpackage');

        // Adding fields to table local_mxschool_subpackage.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('package', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, 'mxschool');
        $table->add_field('subpackage', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('pages', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table local_mxschool_subpackage.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for local_mxschool_subpackage.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Add all mxschool subpackages in the bew format.
        $subpackages = array(
            array('subpackage' => 'user_management', 'pages' => json_encode(array(
                'student_report', 'faculty_report', 'dorm_report', 'vehicle_report', 'picture_import'
            ))),
            array('subpackage' => 'checkin', 'pages' => json_encode(array(
                'preferences', 'generic_report', 'weekday_report', 'weekend_form', 'weekend_report', 'weekend_calculator'
            ))),
            array('subpackage' => 'advisor_selection', 'pages' => json_encode(array('preferences', 'form', 'report'))),
            array('subpackage' => 'rooming', 'pages' => json_encode(array('preferences', 'form', 'report'))),
            array('subpackage' => 'vacation_travel', 'pages' => json_encode(array(
                'preferences', 'form', 'report', 'transportation_report'
            )))
        );
        foreach ($subpackages as $subpackage) {
            $DB->insert_record('local_mxschool_subpackage', (object) $subpackage);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2019072512, 'local', 'mxschool');
    }

    if ($oldversion < 2019073100) {

        // Define field abbreviation to be dropped from local_mxschool_dorm.
        $table = new xmldb_table('local_mxschool_dorm');
        $field = new xmldb_field('abbreviation');

        // Conditionally launch drop field abbreviation.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field faculty_code to be dropped from local_mxschool_faculty.
        $table = new xmldb_table('local_mxschool_faculty');
        $field = new xmldb_field('faculty_code');

        // Conditionally launch drop field faculty_code.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2019073100, 'local', 'mxschool');
    }

    return true;
}
