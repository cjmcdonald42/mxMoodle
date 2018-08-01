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

    if ($oldversion < 2018072000) {

        // Define table local_mxschool_vt_site to be created.
        $table = new xmldb_table('local_mxschool_vt_site');

        // Adding fields to table local_mxschool_vt_site.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('deleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('enabled_departure', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('enabled_return', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');

        // Adding keys to table local_mxschool_vt_site.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for local_mxschool_vt_site.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table local_mxschool_vt_transport to be created.
        $table = new xmldb_table('local_mxschool_vt_transport');

        // Adding fields to table local_mxschool_vt_transport.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('campus_date_time', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('mx_transportation', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('siteid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('site_other', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('carrier', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('transportation_number', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('transportation_date_time', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('international', XMLDB_TYPE_INTEGER, '1', null, null, null, null);

        // Adding keys to table local_mxschool_vt_transport.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('site', XMLDB_KEY_FOREIGN, array('siteid'), 'local_mxschool_vt_site', array('id'));

        // Conditionally launch create table for local_mxschool_vt_transport.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table local_mxschool_vt_trip to be created.
        $table = new xmldb_table('local_mxschool_vt_trip');

        // Adding fields to table local_mxschool_vt_trip.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('departureid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('returnid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('destination', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('phone_number', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('time_created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('time_modified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_mxschool_vt_trip.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('student', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'user', array('id'));
        $table->add_key('departure', XMLDB_KEY_FOREIGN_UNIQUE, array('departureid'), 'local_mxschool_vt_transport', array('id'));
        $table->add_key('return', XMLDB_KEY_FOREIGN_UNIQUE, array('returnid'), 'local_mxschool_vt_transport', array('id'));

        // Conditionally launch create table for local_mxschool_vt_trip.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018072000, 'local', 'mxschool');
    }

    if ($oldversion < 2018072004) {
        $sites = array(
            array('name' => 'Logan', 'type' => 'Plane', 'enabled_departure' => 1, 'enabled_return' => 1),
            array('name' => 'South Station', 'type' => 'Train', 'enabled_departure' => 1, 'enabled_return' => 1),
            array('name' => '128 Westwood', 'type' => 'Train', 'enabled_departure' => 1, 'enabled_return' => 1),
            array('name' => 'South Station', 'type' => 'Bus', 'enabled_departure' => 1, 'enabled_return' => 1),
            array('name' => 'Stamford, CT', 'type' => 'NYC Direct', 'enabled_departure' => 1, 'enabled_return' => 1),
            array('name' => 'Upper East Side (NYC)', 'type' => 'NYC Direct', 'enabled_departure' => 1, 'enabled_return' => 1),
            array('name' => 'Penn Station', 'type' => 'NYC Direct', 'enabled_departure' => 1, 'enabled_return' => 1)
        );
        foreach ($sites as $site) {
            $DB->insert_record('local_mxschool_vt_site', (object) $site);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018072004, 'local', 'mxschool');
    }

    if ($oldversion < 2018072418) {

        // Rename field site_other on table local_mxschool_vt_transport to details.
        $table = new xmldb_table('local_mxschool_vt_transport');
        $field = new xmldb_field('site_other', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'siteid');

        // Launch rename field details.
        $dbman->rename_field($table, $field, 'details');

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018072418, 'local', 'mxschool');
    }

    if ($oldversion < 2018072420) {

        // Define key site (foreign) to be dropped form local_mxschool_vt_transport.
        $table = new xmldb_table('local_mxschool_vt_transport');
        $key = new xmldb_key('site', XMLDB_KEY_FOREIGN, array('siteid'), 'local_mxschool_vt_site', array('id'));

        // Launch drop key site.
        $dbman->drop_key($table, $key);

        // Changing nullability of field siteid on table local_mxschool_vt_transport to null.
        $table = new xmldb_table('local_mxschool_vt_transport');
        $field = new xmldb_field('siteid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'type');

        // Launch change of nullability for field siteid.
        $dbman->change_field_notnull($table, $field);

        // Define key site (foreign) to be added to local_mxschool_vt_transport.
        $table = new xmldb_table('local_mxschool_vt_transport');
        $key = new xmldb_key('site', XMLDB_KEY_FOREIGN, array('siteid'), 'local_mxschool_vt_site', array('id'));

        // Launch add key site.
        $dbman->add_key($table, $key);

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018072420, 'local', 'mxschool');
    }

    if ($oldversion < 2018072510) {
        set_config('esignout_form_instructions', 'Your driver must have submitted a form to be in the list below.', 'local_mxschool');

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018072510, 'local', 'mxschool');
    }

    if ($oldversion < 2018072702) {
        set_config('esignout_form_iperror', 'You must be on Middlesex\'s network to access this form.', 'local_mxschool');

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018072702, 'local', 'mxschool');
    }

    if ($oldversion < 2018072704) {
        set_config('esignout_report_iperror', 'You must be on Middlesex\'s network to sign in.', 'local_mxschool');

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018072704, 'local', 'mxschool');
    }

    if ($oldversion < 2018073008) {
        set_config('weekend_form_warning_closed', 'The weekend you have selected is a closed weekend - you will need special permissions from the deans.', 'local_mxschool');

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018073008, 'local', 'mxschool');
    }

    if ($oldversion < 2018073010) {

        // Rename field campus_date_time on table local_mxschool_vt_transport to date_time.
        $table = new xmldb_table('local_mxschool_vt_transport');
        $field = new xmldb_field('campus_date_time', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch rename field campus_date_time.
        $dbman->rename_field($table, $field, 'date_time');

        // Define field transportation_date_time to be dropped from local_mxschool_vt_transport.
        $table = new xmldb_table('local_mxschool_vt_transport');
        $field = new xmldb_field('transportation_date_time');

        // Conditionally launch drop field transportation_date_time.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018073010, 'local', 'mxschool');
    }

    if ($oldversion < 2018073013) {
        set_config('esignout_form_ipenabled', '1', 'local_mxschool');
        set_config('advisor_form_enabled_who', 'all', 'local_mxschool');
        set_config('vacation_form_returnenabled', '1', 'local_mxschool');

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018073013, 'local', 'mxschool');
    }

    if ($oldversion < 2018073104) {
        set_config('esignout_form_instructions_passenger', 'Your driver must have submitted a form to be in the list below.', 'local_mxschool');
        set_config('esignout_form_instructions_bottom', 'You will have {minutes} minutes to edit your form once you have submitted it.', 'local_mxschool');

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018073104, 'local', 'mxschool');
    }

    return true;
}
