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
 * Database updgrade steps for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_signout_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2019070801) {

        // Add on-campus subpackage to the database.
        $subpackage = array('package' => 'signout', 'subpackage' => 'on_campus', 'pages' => json_encode(array(
            'preferences' => 'preferences.php', 'form' => 'on_campus_enter.php', 'report' => 'on_campus_report.php',
            'duty_report' => 'on_campus_duty_report.php'
        )));
        $DB->insert_record('local_mxschool_subpackage', (object) $subpackage);

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019070801, 'local', 'signout');
    }

    if ($oldversion < 2019070802) {

        // Define table local_signout_location to be created.
        $table = new xmldb_table('local_signout_location');

        // Adding fields to table local_signout_location.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('deleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('grade', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '11');
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('start_date', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('stop_date', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table local_signout_location.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for local_signout_location.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table local_signout_on_campus to be created.
        $table = new xmldb_table('local_signout_on_campus');

        // Adding fields to table local_signout_on_campus.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('locationid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('confirmerid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('deleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('confirmation_time', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('sign_in_time', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('time_created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('time_modified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_signout_on_campus.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('student', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->add_key('location', XMLDB_KEY_FOREIGN, array('locationid'), 'local_signout_location', array('id'));
        $table->add_key('confirmer', XMLDB_KEY_FOREIGN, array('confirmerid'), 'user', array('id'));

        // Conditionally launch create table for local_signout_on_campus.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019070802, 'local', 'signout');
    }

    if ($oldversion < 2019070805) {

        // Add new configs for on_campus signout.
        set_config('on_campus_form_enabled', '1', 'local_signout');
        set_config('on_campus_form_ipenabled', '1', 'local_signout');

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019070805, 'local', 'signout');
    }

    if ($oldversion < 2019070806) {

        // Add new configs for on_campus signout.
        set_config('on_campus_form_iperror', 'You must be on Middlesex\'s network to access this form.', 'local_signout');
        set_config('on_campus_report_iperror', 'You must be on Middlesex\'s network to sign in.', 'local_signout');

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019070806, 'local', 'signout');
    }

    if ($oldversion < 2019070810) {

        // Define field other to be added to local_signout_on_campus.
        $table = new xmldb_table('local_signout_on_campus');
        $field = new xmldb_field('other', XMLDB_TYPE_CHAR, '20', null, null, null, null, 'deleted');

        // Conditionally launch add field other.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019070810, 'local', 'signout');
    }

    if ($oldversion < 2019070811) {

        // Changing precision of field name on table local_signout_location to (100).
        $table = new xmldb_table('local_signout_location');
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'deleted');

        // Launch change of precision for field name.
        $dbman->change_field_precision($table, $field);

        // Changing precision of field other on table local_signout_on_campus to (100).
        $table = new xmldb_table('local_signout_on_campus');
        $field = new xmldb_field('other', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'deleted');

        // Launch change of precision for field other.
        $dbman->change_field_precision($table, $field);

        // Populate the local_signout_location table.
        $locations = array(
            array('name' => 'Library', 'grade' => 11),
            array('name' => 'Terry Room', 'grade' => 11),
            array('name' => 'Tech Center', 'grade' => 11),
            array('name' => 'Rachel Carson Center', 'grade' => 11),
            array('name' => 'Clay Centenial Center Lobby', 'grade' => 11)
        );
        foreach ($locations as $location) {
            $DB->insert_record('local_signout_location', (object) $location);
        }

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019070811, 'local', 'signout');
    }

    if ($oldversion < 2019071100) {

        // Rename field stop_date on table local_signout_location to end_date.
        $table = new xmldb_table('local_signout_location');
        $field = new xmldb_field('stop_date', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'start_date');

        // Launch rename field stop_date.
        $dbman->rename_field($table, $field, 'end_date');

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019071100, 'local', 'signout');
    }

    if ($oldversion < 2019071204) {

        // Add new configs for on_campus reports to refresh.
        set_config('on_campus_refresh_rate', '60', 'local_signout');

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019071204, 'local', 'signout');
    }

    if ($oldversion < 2019071208) {

        // Add new configs for on_campus warnings.
        set_config('on_campus_form_warning', 'You need special permission to go to a non-academic location.', 'local_signout');
        set_config('on_campus_form_confirmation', 'Have you received the required permissions?', 'local_signout');
        set_config('off_campus_form_confirmation', 'Have you received the required permissions?', 'local_signout');

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019071208, 'local', 'signout');
    }

    if ($oldversion < 2019071209) {

        $oncampus = $DB->get_record('local_mxschool_subpackage', array('subpackage' => 'on_campus'));
        $oncampus->pages = json_encode(array(
            'preferences' => 'preferences.php', 'form' => 'on_campus_enter.php', 'report' => 'on_campus_report.php',
            'duty_report' => 'duty_report.php'
        ));
        $DB->update_record('local_mxschool_subpackage', $oncampus);

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019071209, 'local', 'signout');
    }

    if ($oldversion < 2019071802) {

        // Unset old signin warnings.
        unset_config('off_campus_report_iperror', 'local_signout');
        unset_config('on_campus_report_iperror', 'local_signout');

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019071802, 'local', 'signout');
    }

    if ($oldversion < 2019071903) {

        // Set no permissions warning.
        set_config('off_campus_notification_warning_unsetpermissions', 'This student does NOT have passenger permissions on file.', 'local_signout');

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019071903, 'local', 'signout');
    }

    if ($oldversion < 2019071904) {

        // Set on_campus form warnings.
        unset_config('on_campus_form_warning', 'local_signout');
        set_config('on_campus_form_warning_underclassmen', 'You need special permission to go to any other location.', 'local_signout');
        set_config('on_campus_form_warning_juniors', 'You need special permission to go to a non-academic location.', 'local_signout');

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019071904, 'local', 'signout');
    }

    if ($oldversion < 2019071905) {

        // Add a couple more default locations.
        $locations = array(
            array('name' => 'Supervised Study Hall', 'grade' => 9),
            array('name' => 'Bass Arts Pavilion', 'grade' => 11)
        );
        foreach ($locations as $location) {
            $DB->insert_record('local_signout_location', (object) $location);
        }

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019071905, 'local', 'signout');
    }

    if ($oldversion < 2019072201) {

        // Add a few more default locations.
        $locations = array(
            array('name' => 'Health Center', 'grade' => 9),
            array('name' => 'StuFac', 'grade' => 12),
            array('name' => 'Gym', 'grade' => 12)
        );
        foreach ($locations as $location) {
            $DB->insert_record('local_signout_location', (object) $location);
        }

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019072201, 'local', 'signout');
    }

    if ($oldversion < 2019072210) {

        // Define key student (foreign) to be dropped form local_signout_off_campus.
        $table = new xmldb_table('local_signout_off_campus');
        $key = new xmldb_key('student', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Launch drop key student.
        $dbman->drop_key($table, $key);

        // Define key driver (foreign) to be dropped form local_signout_off_campus.
        $table = new xmldb_table('local_signout_off_campus');
        $key = new xmldb_key('driver', XMLDB_KEY_FOREIGN, array('driverid'), 'local_signout_offcampus', array('id'));

        // Launch drop key driver.
        $dbman->drop_key($table, $key);

        // Define key student (foreign) to be dropped form local_signout_on_campus.
        $table = new xmldb_table('local_signout_on_campus');
        $key = new xmldb_key('student', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Launch drop key student.
        $dbman->drop_key($table, $key);

        // Define key location (foreign) to be dropped form local_signout_on_campus.
        $table = new xmldb_table('local_signout_on_campus');
        $key = new xmldb_key('location', XMLDB_KEY_FOREIGN, array('locationid'), 'local_signout_location', array('id'));

        // Launch drop key location.
        $dbman->drop_key($table, $key);

        // Changing the default of field userid on table local_signout_off_campus to drop it.
        $table = new xmldb_table('local_signout_off_campus');
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of default for field userid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field driverid on table local_signout_off_campus to drop it.
        $table = new xmldb_table('local_signout_off_campus');
        $field = new xmldb_field('driverid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'userid');

        // Launch change of default for field driverid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field userid on table local_signout_on_campus to drop it.
        $table = new xmldb_table('local_signout_on_campus');
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of default for field userid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field locationid on table local_signout_on_campus to drop it.
        $table = new xmldb_table('local_signout_on_campus');
        $field = new xmldb_field('locationid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'userid');

        // Launch change of default for field locationid.
        $dbman->change_field_default($table, $field);

        // Define key student (foreign) to be added form local_signout_off_campus.
        $table = new xmldb_table('local_signout_off_campus');
        $key = new xmldb_key('student', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Launch add key student.
        $dbman->add_key($table, $key);

        // Define key driver (foreign) to be added form local_signout_off_campus.
        $table = new xmldb_table('local_signout_off_campus');
        $key = new xmldb_key('driver', XMLDB_KEY_FOREIGN, array('driverid'), 'local_signout_offcampus', array('id'));

        // Launch add key driver.
        $dbman->add_key($table, $key);

        // Define key student (foreign) to be added form local_signout_on_campus.
        $table = new xmldb_table('local_signout_on_campus');
        $key = new xmldb_key('student', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Launch add key student.
        $dbman->add_key($table, $key);

        // Define key location (foreign) to be added form local_signout_on_campus.
        $table = new xmldb_table('local_signout_on_campus');
        $key = new xmldb_key('location', XMLDB_KEY_FOREIGN, array('locationid'), 'local_signout_location', array('id'));

        // Launch add key location.
        $dbman->add_key($table, $key);

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019072210, 'local', 'signout');
    }

    if ($oldversion < 2019072213) {

        // Add new config for sign in ip validation error.
        set_config('on_campus_signin_iperror_boarder', 'You must be on Middlesex\'s network to sign back in to your dorm.', 'local_signout');
        set_config('on_campus_signin_iperror_day', 'You must be on Middlesex\'s network to be going home.', 'local_signout');
        set_config('off_campus_signin_iperror', 'You must be on Middlesex\'s network to sign in.', 'local_signout');

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019072213, 'local', 'signout');
    }

    if ($oldversion < 2019072300) {

        // Add root subpackage.
        $DB->insert_record('local_mxschool_subpackage', (object) array('package' => 'signout', 'pages' => json_encode(array('combined_report' => 'combined_report.php'))));

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019072300, 'local', 'signout');
    }

    if ($oldversion < 2019072400) {

        // Define field warning to be added to local_signout_location.
        $table = new xmldb_table('local_signout_location');
        $field = new xmldb_field('warning', XMLDB_TYPE_TEXT, null, null, null, null, null, 'end_date');

        // Conditionally launch add field warning.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $DB->insert_record('local_signout_location', (object) array('name' => 'On Campus', 'grade' => 12, 'warning' => 'You need face-to-face permission from the person on duty in your dorm to sign out \'On Campus.\''));

        set_config('on_campus_form_warning_underclassmen', 'You need special permission to go to any \'other\' location.', 'local_signout');

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019072400, 'local', 'signout');
    }

    if ($oldversion < 2019072502) {

        // Add all signout subpackages in the bew format.
        $subpackages = array(
            array('package' => 'signout', 'pages' => json_encode(array('combined_report'))),
            array('package' => 'signout', 'subpackage' => 'on_campus', 'pages' => json_encode(array(
                'preferences', 'form', 'report', 'duty_report'
            ))),
            array('package' => 'signout', 'subpackage' => 'off_campus', 'pages' => json_encode(array('preferences', 'form', 'report')))
        );
        foreach ($subpackages as $subpackage) {
            $DB->insert_record('local_mxschool_subpackage', (object) $subpackage);
        }

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019072502, 'local', 'signout');
    }

    if ($oldversion < 2019080400) {

        // Add new notification warning and rename existing ones.
        unset_config('off_campus_notification_warning_driver', 'local_signout');
        unset_config('off_campus_notification_warning_any', 'local_signout');
        unset_config('off_campus_notification_warning_parent', 'local_signout');
        unset_config('off_campus_notification_warning_specific', 'local_signout');
        unset_config('off_campus_notification_warning_over21', 'local_signout');
        unset_config('off_campus_notification_warning_unsetpermissions', 'local_signout');

        set_config('off_campus_notification_warning_driver_yespassengers', 'None.', 'local_signout');
        set_config('off_campus_notification_warning_driver_nopassengers', 'This student does NOT have permission to drive other students.', 'local_signout');
        set_config('off_campus_notification_warning_passenger_any', 'None.', 'local_signout');
        set_config('off_campus_notification_warning_passenger_parent', 'This student requires parent permission to be the passenger of another student.', 'local_signout');
        set_config('off_campus_notification_warning_passenger_specific', 'This student only has permission to the be the passenger of the following drivers: ', 'local_signout');
        set_config('off_campus_notification_warning_passenger_over21', 'This student does NOT have permission to be the passenger of anyone under 21.', 'local_signout');

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019080400, 'local', 'signout');
    }

    if ($oldversion < 2019080402) {

        // Add a few more notification warnings.
        set_config('off_campus_notification_warning_parent', 'None.', 'local_signout');
        set_config('off_campus_notification_warning_rideshare_yes', 'None.', 'local_signout');
        set_config('off_campus_notification_warning_rideshare_no', 'This student does NOT have permission to use rideshare.', 'local_signout');

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019080402, 'local', 'signout');
    }

    if ($oldversion < 2019080501) {

        // Add config for confirmation undo.
        set_config('on_campus_confirmation_undo_window', '5', 'local_signout');

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019080501, 'local', 'signout');
    }

    if ($oldversion < 2019080603) {

        // Add another default location.
        $DB->insert_record('local_signout_location', (object) array('name' => 'Play Rehearsal / Tech', 'grade' => 9));

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019080603, 'local', 'signout');
    }

    if ($oldversion < 2019080605) {

        // Add two more toggles.
        set_config('off_campus_form_permissions_active', '0', 'local_signout');
        set_config('on_campus_confirmation_enabled', '0', 'local_signout');

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019080605, 'local', 'signout');
    }

    if ($oldversion < 2019080701) {

        // Define table local_signout_type to be dropped.
        $table = new xmldb_table('local_signout_type');

        // Conditionally launch drop table for local_signout_type.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table local_signout_type to be created.
        $table = new xmldb_table('local_signout_type');

        // Adding fields to table local_signout_type.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('deleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('required_permissions', XMLDB_TYPE_CHAR, '10', null, null, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('grade', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '9');
        $table->add_field('boardering_status', XMLDB_TYPE_CHAR, '10', null, null, null, null);
        $table->add_field('weekend_only', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('start_date', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('end_date', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('form_warning', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('email_warning', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table local_signout_type.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for local_signout_type.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Changing the default of field grade on table local_signout_location to 9.
        $table = new xmldb_table('local_signout_location');
        $field = new xmldb_field('grade', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '9', 'name');

        // Launch change of default for field grade.
        $dbman->change_field_default($table, $field);

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019080701, 'local', 'signout');
    }

    if ($oldversion < 2019080705) {

        // Rename existing form warnings and add a couple new ones.
        unset_config('off_campus_form_warning_nopassengers', 'local_signout');
        unset_config('off_campus_form_warning_needparent', 'local_signout');
        unset_config('off_campus_form_warning_onlyspecific', 'local_signout');

        set_config('off_campus_form_warning_driver_nopassengers', 'Your permissions indicate that you may not drive passengers.', 'local_signout');
        set_config('off_campus_form_warning_passenger_parent', 'Your permissions indicate that you need a call from your parent to be the passenger of another student.', 'local_signout');
        set_config('off_campus_form_warning_passenger_specific', 'Your permissions indicate that you may only be the passenger of the following drivers: ', 'local_signout');
        set_config('off_campus_form_warning_passenger_over21', 'Your permissions indicate that you are not allowed to be the passenger of a driver who is under 21.', 'local_signout');
        set_config('off_campus_form_warning_rideshare_parent', 'Your permissions indicate that you need a call from your parent to use a car service.', 'local_signout');
        set_config('off_campus_form_warning_rideshare_notallowed', 'Your permissions indicate that you are not allowed to use a car service.', 'local_signout');

        // Remove and rename existing email warnings and add a new one.
        unset_config('off_campus_notification_warning_driver_passengers', 'local_signout');
        unset_config('off_campus_notification_warning_driver_yespassengers', 'local_signout');
        unset_config('off_campus_notification_warning_passenger_any', 'local_signout');
        unset_config('off_campus_notification_warning_parent', 'local_signout');
        unset_config('off_campus_notification_warning_rideshare_yes', 'local_signout');
        unset_config('off_campus_notification_warning_rideshare_no', 'local_signout');

        set_config('off_campus_notification_warning_driver_nopassengers', 'This student does NOT have permission to drive other students.', 'local_signout');
        set_config('off_campus_notification_warning_rideshare_parent', 'This student requires parent permission to use a car service.', 'local_signout');
        set_config('off_campus_notification_warning_rideshare_notallowed', 'This student does NOT have permission to use a car service.', 'local_signout');

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019080705, 'local', 'signout');
    }

    if ($oldversion < 2019080706) {

        // Rename field boarding_status on table local_signout_type to boarding_status.
        $table = new xmldb_table('local_signout_type');
        $field = new xmldb_field('boardering_status', XMLDB_TYPE_CHAR, '10', null, null, null, null, 'grade');

        // Launch rename field boardering_status.
        $dbman->rename_field($table, $field, 'boarding_status');

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019080706, 'local', 'signout');
    }

    if ($oldversion < 2019080709) {

        // Changing the default of field boarding_status on table local_signout_type to All.
        $table = new xmldb_table('local_signout_type');
        $field = new xmldb_field('boarding_status', XMLDB_TYPE_CHAR, '10', null, null, null, 'All', 'grade');

        // Launch change of default for field boarding_status.
        $dbman->change_field_default($table, $field);

        // Changing nullability of field boarding_status on table local_signout_type to not null.
        $table = new xmldb_table('local_signout_type');
        $field = new xmldb_field('boarding_status', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, 'All', 'grade');

        // Launch change of nullability for field boarding_status.
        $dbman->change_field_notnull($table, $field);

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019080709, 'local', 'signout');
    }

    if ($oldversion < 2019080715) {

        // Define table local_signout_off_campus to be dropped.
        $table = new xmldb_table('local_signout_off_campus');

        // Conditionally launch drop table for local_signout_off_campus.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table local_signout_off_campus to be created.
        $table = new xmldb_table('local_signout_off_campus');

        // Adding fields to table local_signout_off_campus.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('typeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('driverid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('approverid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('deleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('other', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('passengers', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('destination', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('departure_time', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sign_in_time', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('time_created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('time_modified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_signout_off_campus.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('student', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->add_key('type', XMLDB_KEY_FOREIGN, array('typeid'), 'local_signout_type', array('id'));
        $table->add_key('driver', XMLDB_KEY_FOREIGN, array('driverid'), 'local_signout_off_campus', array('id'));
        $table->add_key('approver', XMLDB_KEY_FOREIGN, array('approverid'), 'user', array('id'));

        // Conditionally launch create table for local_signout_off_campus.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019080715, 'local', 'signout');
    }

    if ($oldversion < 2019080807) {

        // Populate type table with defaults.
        $types = array(
            array('required_permissions' => 'driver', 'name' => 'Driving', 'grade' => 11, 'boarding_status' => 'Day'),
            array('required_permissions' => 'passenger', 'name' => 'Riding with Another Student', 'grade' => 11),
            array('required_permissions' => 'rideshare', 'name' => 'Car Service'),
            array('name' => 'Riding with Your Parent', 'form_warning' => 'You need face-to-face permission from one of your dorm faculty, or your parents need to have called the permissions line.'),
            array('name' => 'Town Shuttle'),
            array('name' => 'Weekend Activity', 'weekend_only' => 1),
            array('name' => 'Weekend Signout', 'boarding_status' => 'Boarder', 'weekend_only' => 1, 'form_warning' => 'You need to have an approved weekend form for this weekend on file.'),
            array('name' => 'Vacation Signout', 'boarding_status' => 'Boarder', 'enabled' => 0, 'form_warning' => 'You need to have a vacation travel form on file.')
        );
        foreach ($types as $type) {
            $DB->insert_record('local_signout_type', (object) $type);
        }

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019080807, 'local', 'signout');
    }

    if ($oldversion < 2019080809) {

        // Tweak config text.
        set_config('off_campus_form_instructions_passenger', 'Your driver must have submitted a form and selected you as a passenger to appear in the list below.', 'local_signout');
        set_config('off_campus_form_warning_driver_nopassengers', 'Your permissions indicate that you may not drive other students.', 'local_signout');
        set_config('off_campus_form_warning_passenger_parent', 'Your permissions indicate that you need a call from your parent to ride with another student.', 'local_signout');
        set_config('off_campus_form_warning_passenger_specific', 'Your permissions indicate that you may only ride with the following drivers:', 'local_signout');
        set_config('off_campus_form_warning_passenger_over21', 'Your permissions indicate that you are not allowed to be the ride with a driver who is under 21.', 'local_signout');
        set_config('off_campus_notification_warning_passenger_parent', 'This student requires parent permission to ride with another student.', 'local_signout');
        set_config('off_campus_notification_warning_passenger_specific', 'This student only has permission to ride with of the following drivers:', 'local_signout');
        set_config('off_campus_notification_warning_passenger_over21', 'This student does NOT have permission to ride with anyone under 21.', 'local_signout');


        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019080809, 'local', 'signout');
    }

    if ($oldversion < 2019080815) {

        // Define field all_day to be added to local_signout_location.
        $table = new xmldb_table('local_signout_location');
        $field = new xmldb_field('all_day', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'grade');

        // Conditionally launch add field all_day.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $DB->set_field('local_signout_location', 'all_day', 1, array('name' => 'Library'));

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019080815, 'local', 'signout');
    }

    if ($oldversion < 2019080822) {

        // Rename some of the configs.
        unset_config('on_campus_form_ipenabled', 'local_signout');
        unset_config('on_campus_form_iperror', 'local_signout');
        unset_config('on_campus_signin_iperror_boarder', 'local_signout');
        unset_config('on_campus_signin_iperror_day', 'local_signout');
        unset_config('off_campus_form_ipenabled', 'local_signout');
        unset_config('off_campus_form_iperror', 'local_signout');
        unset_config('off_campus_signin_iperror', 'local_signout');

        set_config('on_campus_ipvalidation_enabled', '1', 'local_signout');
        set_config('on_campus_form_ipvalidation_error', 'You must be on Middlesex\'s network to access this form.', 'local_signout');
        set_config('on_campus_signin_ipvalidation_error_boarder', 'You must be on Middlesex\'s network to sign back in to your dorm.', 'local_signout');
        set_config('on_campus_signin_ipvalidation_error_day', 'You must be on Middlesex\'s network to be going home.', 'local_signout');
        set_config('off_campus_ipvalidation_enabled', '1', 'local_signout');
        set_config('off_campus_form_ipvalidation_error', 'You must be on Middlesex\'s network to access this form.', 'local_signout');
        set_config('off_campus_signin_ipvalidation_error', 'You must be on Middlesex\'s network to sign in.', 'local_signout');

        // Signout savepoint reached.
        upgrade_plugin_savepoint(true, 2019080822, 'local', 'signout');
    }

    return true;
}
