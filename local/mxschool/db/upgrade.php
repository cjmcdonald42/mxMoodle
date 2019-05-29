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

    return true;
}
