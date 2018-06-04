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

    if ($oldversion < 2018052111) {

        // Rename field birthdate on table local_mxschool_student to birthday.
        $table = new xmldb_table('local_mxschool_student');
        $field = new xmldb_field('birthdate', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, 'phone_number');

        // Launch rename field birthdate.
        $dbman->rename_field($table, $field, 'birthday');

        // Changing precision of field birthday on table local_mxschool_student to (10).
        $table = new xmldb_table('local_mxschool_student');
        $field = new xmldb_field('birthday', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null, 'phone_number');

        // Launch change of precision for field birthday.
        $dbman->change_field_precision($table, $field);

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018052111, 'local', 'mxschool');
    }

    if ($oldversion < 2018052234) {

        // Changing precision of field relationship on table local_mxschool_parent to (20).
        $table = new xmldb_table('local_mxschool_parent');
        $field = new xmldb_field('relationship', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, 'is_primary_parent');

        // Launch change of precision for field relationship.
        $dbman->change_field_precision($table, $field);

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018052234, 'local', 'mxschool');
    }

    if ($oldversion < 2018060300) {

        // Define field deleted to be added to local_mxschool_parent.
        $table = new xmldb_table('local_mxschool_parent');
        $field = new xmldb_field('deleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'userid');

        // Conditionally launch add field deleted.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field deleted to be added to local_mxschool_dorm.
        $table = new xmldb_table('local_mxschool_dorm');
        $field = new xmldb_field('deleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'hohid');

        // Conditionally launch add field deleted.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018060300, 'local', 'mxschool');
    }

    return true;
}
