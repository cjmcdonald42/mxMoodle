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
 * Database updgrade steps for Middlesex School's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
        $usermanagement = $DB->get_record('local_mxschool_subpackage', array('subpackage' => 'user_management'));
        $usermanagement->pages = json_encode(array(
            'student_report' => 'student_report.php', 'faculty_report' => 'faculty_report.php', 'dorm_report' => 'dorm_report.php',
            'vehicle_report' => 'vehicle_report.php',
        ));
        $DB->update_record('local_mxschool_subpackage', $usermanagement);
        $esignout = $DB->get_record('local_mxschool_subpackage', array('subpackage' => 'esignout'));
        $esignout->pages = json_encode(array(
            'preferences' => 'preferences.php', 'form' => 'esignout_enter.php', 'report' => 'esignout_report.php'
        ));
        $DB->update_record('local_mxschool_subpackage', $esignout);

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2019070402, 'local', 'mxschool');
    }

    return true;
}
