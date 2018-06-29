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
 * Database updgrade steps for Middlesex School's Peer Tutoring Subplugin.
 *
 * @package    local_peertutoring
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_peertutoring_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2018062806) {

        // Define field deleted to be added to local_peertutoring_course.
        $table = new xmldb_table('local_peertutoring_course');
        $field = new xmldb_field('deleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'departmentid');

        // Conditionally launch add field deleted.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field deleted to be added to local_peertutoring_dept.
        $table = new xmldb_table('local_peertutoring_dept');
        $field = new xmldb_field('deleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'id');

        // Conditionally launch add field deleted.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field deleted to be added to local_peertutoring_rating.
        $table = new xmldb_table('local_peertutoring_rating');
        $field = new xmldb_field('deleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'id');

        // Conditionally launch add field deleted.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field deleted to be added to local_peertutoring_type.
        $table = new xmldb_table('local_peertutoring_type');
        $field = new xmldb_field('deleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'id');

        // Conditionally launch add field deleted.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Peertutoring savepoint reached.
        upgrade_plugin_savepoint(true, 2018062806, 'local', 'peertutoring');
    }

    if ($oldversion < 2018062817) {

        // Define key department (foreign) to be dropped form local_peertutoring_course.
        $table = new xmldb_table('local_peertutoring_course');
        $key = new xmldb_key('department', XMLDB_KEY_FOREIGN, array('departmentid'), 'local_mxschool_department', array('id'));

        // Launch drop key department.
        $dbman->drop_key($table, $key);

        // Define key department (foreign) to be added to local_peertutoring_course.
        $table = new xmldb_table('local_peertutoring_course');
        $key = new xmldb_key('department', XMLDB_KEY_FOREIGN, array('departmentid'), 'local_peertutoring_dept', array('id'));

        // Launch add key department.
        $dbman->add_key($table, $key);

        // Define field other to be added to local_peertutoring_session.
        $table = new xmldb_table('local_peertutoring_session');
        $field = new xmldb_field('other', XMLDB_TYPE_CHAR, '50', null, null, null, null, 'topic');

        // Conditionally launch add field other.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Peertutoring savepoint reached.
        upgrade_plugin_savepoint(true, 2018062817, 'local', 'peertutoring');
    }

    if ($oldversion < 2018062818) {

        // Rename field date on table local_peertutoring_session to NEWNAMEGOESHERE.
        $table = new xmldb_table('local_peertutoring_session');
        $field = new xmldb_field('date', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'ratingid');

        // Launch rename field date.
        $dbman->rename_field($table, $field, 'tutoring_date');

        // Peertutoring savepoint reached.
        upgrade_plugin_savepoint(true, 2018062818, 'local', 'peertutoring');
    }

    if ($oldversion < 2018062828) {

        // Define field deleted to be added to local_peertutoring_session.
        $table = new xmldb_table('local_peertutoring_session');
        $field = new xmldb_field('deleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'ratingid');

        // Conditionally launch add field deleted.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Peertutoring savepoint reached.
        upgrade_plugin_savepoint(true, 2018062828, 'local', 'peertutoring');
    }

    return true;
}
