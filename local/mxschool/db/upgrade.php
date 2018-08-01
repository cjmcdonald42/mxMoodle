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

    if ($oldversion < 2018073107) {

        // Define field available to be dropped from local_mxschool_dorm.
        $table = new xmldb_table('local_mxschool_dorm');
        $field = new xmldb_field('available');

        // Conditionally launch drop field available.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field available to be added to local_mxschool_dorm.
        $table = new xmldb_table('local_mxschool_dorm');
        $field = new xmldb_field('available', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'gender');

        // Conditionally launch add field available.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field is_primary_parent to be dropped from local_mxschool_parent.
        $table = new xmldb_table('local_mxschool_parent');
        $field = new xmldb_field('is_primary_parent');

        // Conditionally launch drop field is_primary_parent.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field is_primary_parent to be added to local_mxschool_parent.
        $table = new xmldb_table('local_mxschool_parent');
        $field = new xmldb_field('is_primary_parent', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'deleted');

        // Conditionally launch add field is_primary_parent.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Mxschool savepoint reached.
        upgrade_plugin_savepoint(true, 2018073107, 'local', 'mxschool');
    }

    return true;
}
