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

    if ($oldversion < 2018090506) {

    // Define field start_time to be dropped from local_mxschool_weekend.
    $table = new xmldb_table('local_mxschool_weekend');
    $field = new xmldb_field('start_time');

    // Conditionally launch drop field start_time.
    if ($dbman->field_exists($table, $field)) {
        $dbman->drop_field($table, $field);
    }

    // Define field start_offset to be added to local_mxschool_weekend.
    $table = new xmldb_table('local_mxschool_weekend');
    $field = new xmldb_field('start_offset', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '-1', 'type');

    // Conditionally launch add field start_offset.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Define field end_time to be dropped from local_mxschool_weekend.
    $table = new xmldb_table('local_mxschool_weekend');
    $field = new xmldb_field('end_time');

    // Conditionally launch drop field end_time.
    if ($dbman->field_exists($table, $field)) {
        $dbman->drop_field($table, $field);
    }

    // Define field end_offset to be added to local_mxschool_weekend.
    $table = new xmldb_table('local_mxschool_weekend');
    $field = new xmldb_field('end_offset', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'start_offset');

    // Conditionally launch add field end_offset.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Mxschool savepoint reached.
    upgrade_plugin_savepoint(true, 2018090506, 'local', 'mxschool');
}

    return true;
}
