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

    if ($oldversion < 2018060802) {

        // Define table local_mxschool_weekend_form to be created.
        $table = new xmldb_table('local_mxschool_weekend_form');

        // Adding fields to table local_mxschool_weekend_form.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('weekendid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('departure_date_time', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('return_date_time', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('destination', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('transportation', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('phone_number', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('time_created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('time_modified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_mxschool_weekend_form.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('user', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'user', array('id'));
        $table->add_key('weekend', XMLDB_KEY_FOREIGN_UNIQUE, array('weekendid'), 'local_mxschool_weekend', array('id'));

        // Conditionally launch create table for local_mxschool_weekend_form.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018060802, 'local', 'mxschool');
    }

    if ($oldversion < 2018060808) {

        // Define key user (foreign) to be dropped form local_mxschool_weekend_form.
        $table = new xmldb_table('local_mxschool_weekend_form');
        $key = new xmldb_key('user', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'user', array('id'));

        // Launch drop key user.
        $dbman->drop_key($table, $key);

        // Define key weekend (foreign) to be dropped form local_mxschool_weekend_form.
        $table = new xmldb_table('local_mxschool_weekend_form');
        $key = new xmldb_key('weekend', XMLDB_KEY_FOREIGN_UNIQUE, array('weekendid'), 'local_mxschool_weekend', array('id'));

        // Launch drop key weekend.
        $dbman->drop_key($table, $key);

        // Define key user (foreign) to be added to local_mxschool_weekend_form.
        $table = new xmldb_table('local_mxschool_weekend_form');
        $key = new xmldb_key('user', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Launch add key user.
        $dbman->add_key($table, $key);

        // Define key weekend (foreign) to be added to local_mxschool_weekend_form.
        $table = new xmldb_table('local_mxschool_weekend_form');
        $key = new xmldb_key('weekend', XMLDB_KEY_FOREIGN, array('weekendid'), 'local_mxschool_weekend', array('id'));

        // Launch add key weekend.
        $dbman->add_key($table, $key);

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018060808, 'local', 'mxschool');
    }

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

    return true;
}
