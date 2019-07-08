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
 * Database updgrade steps for Middlesex School's eSignout Subplugin.
 *
 * @package    local_signout
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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

        // Mxschool savepoint reached.
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

    return true;
}
