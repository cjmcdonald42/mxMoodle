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

    if ($oldversion < 2018041400) {

        // Define table local_mxschool_parents to be dropped.
        $table = new xmldb_table('local_mxschool_parents');

        // Conditionally launch drop table for local_mxschool_parents.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table local_mxschool_students to be dropped.
        $table = new xmldb_table('local_mxschool_students');

        // Conditionally launch drop table for local_mxschool_students.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table local_mxschool_dorm to be dropped.
        $table = new xmldb_table('local_mxschool_dorm');

        // Conditionally launch drop table for local_mxschool_dorm.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table local_mxschool_faculty to be dropped.
        $table = new xmldb_table('local_mxschool_faculty');

        // Conditionally launch drop table for local_mxschool_faculty.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table local_mxschool_parent to be dropped.
        $table = new xmldb_table('local_mxschool_parent');

        // Conditionally launch drop table for local_mxschool_parent.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table local_mxschool_permissions to be dropped.
        $table = new xmldb_table('local_mxschool_permissions');

        // Conditionally launch drop table for local_mxschool_permissions.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table local_mxschool_student to be dropped.
        $table = new xmldb_table('local_mxschool_student');

        // Conditionally launch drop table for local_mxschool_student.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table local_mxschool_dorm to be created.
        $table = new xmldb_table('local_mxschool_dorm');

        // Adding fields to table local_mxschool_dorm.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('hohid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('abbreviation', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('gender', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('available', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, 'Yes');

        // Adding keys to table local_mxschool_dorm.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('hoh', XMLDB_KEY_FOREIGN_UNIQUE, array('hohid'), 'user', array('id'));

        // Conditionally launch create table for local_mxschool_dorm.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table local_mxschool_faculty to be created.
        $table = new xmldb_table('local_mxschool_faculty');

        // Adding fields to table local_mxschool_faculty.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('dormid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('faculty_code', XMLDB_TYPE_CHAR, '5', null, XMLDB_NOTNULL, null, null);
        $table->add_field('advisory_available', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('advisory_closing', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_mxschool_faculty.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('user', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'user', array('id'));
        $table->add_key('dorm', XMLDB_KEY_FOREIGN, array('dormid'), 'local_mxschool_dorm', array('id'));

        // Conditionally launch create table for local_mxschool_faculty.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table local_mxschool_parent to be created.
        $table = new xmldb_table('local_mxschool_parent');

        // Adding fields to table local_mxschool_parent.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('is_primary_parent', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, 'Yes');
        $table->add_field('relationship', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('parent_name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('home_phone', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('cell_phone', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('work_phone', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('email', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_mxschool_parent.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('student', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Conditionally launch create table for local_mxschool_parent.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table local_mxschool_permissions to be created.
        $table = new xmldb_table('local_mxschool_permissions');

        // Adding fields to table local_mxschool_permissions.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('overnight', XMLDB_TYPE_CHAR, '10', null, null, null, null);
        $table->add_field('may_ride_with', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('ride_permission_details', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('ride_share', XMLDB_TYPE_CHAR, '10', null, null, null, null);
        $table->add_field('may_drive_to_boston', XMLDB_TYPE_CHAR, '10', null, null, null, null);
        $table->add_field('may_drive_to_town', XMLDB_TYPE_CHAR, '10', null, null, null, null);
        $table->add_field('may_drive_passengers', XMLDB_TYPE_CHAR, '10', null, null, null, null);
        $table->add_field('swim_competent', XMLDB_TYPE_CHAR, '10', null, null, null, null);
        $table->add_field('swim_allowed', XMLDB_TYPE_CHAR, '10', null, null, null, null);
        $table->add_field('boat_allowed', XMLDB_TYPE_CHAR, '10', null, null, null, null);

        // Adding keys to table local_mxschool_permissions.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('student', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'user', array('id'));

        // Conditionally launch create table for local_mxschool_permissions.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table local_mxschool_student to be created.
        $table = new xmldb_table('local_mxschool_student');

        // Adding fields to table local_mxschool_student.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('dormid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('advisorid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('permissionsid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('admission_year', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);
        $table->add_field('grade', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('gender', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('boarding_status', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('boarding_status_next_year', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('room', XMLDB_TYPE_INTEGER, '3', null, null, null, null);
        $table->add_field('phone_number', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('birthdate', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_mxschool_student.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('user', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'user', array('id'));
        $table->add_key('dorm', XMLDB_KEY_FOREIGN, array('dormid'), 'local_mxschool_dorm', array('id'));
        $table->add_key('advisor', XMLDB_KEY_FOREIGN, array('advisorid'), 'local_mxschool_faculty', array('id'));
        $table->add_key('permissions', XMLDB_KEY_FOREIGN_UNIQUE, array('permissionsid'), 'local_mxschool_permissions', array('id'));

        // Conditionally launch create table for local_mxschool_student.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018041400, 'local', 'mxschool');
    }

    return true;
}
