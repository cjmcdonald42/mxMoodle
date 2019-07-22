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
 * Database updgrade steps for Middlesex's Peer Tutoring Subplugin.
 *
 * @package    local_peertutoring
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_peertutoring_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2019031000) {

        // Define field deleted to be added to local_peertutoring_tutor.
        $table = new xmldb_table('local_peertutoring_tutor');
        $field = new xmldb_field('deleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'userid');

        // Conditionally launch add field deleted.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Peertutoring savepoint reached.
        upgrade_plugin_savepoint(true, 2019031000, 'local', 'peertutoring');
    }

    if ($oldversion < 2019070302) {
        $package = array('package' => 'peertutoring', 'pages' => json_encode(array(
            'preferences' => 'preferences.php', 'tutoring_form' => 'tutoring_enter.php', 'tutoring_report' => 'tutoring_report.php'
        )));
        $DB->insert_record('local_mxschool_subpackage', (object) $package);

        // Peertutoring savepoint reached.
        upgrade_plugin_savepoint(true, 2019070302, 'local', 'peertutoring');
    }

    if ($oldversion < 2019072209) {

        // Define key department (foreign) to be dropped form local_peertutoring_course.
        $table = new xmldb_table('local_peertutoring_course');
        $key = new xmldb_key('department', XMLDB_KEY_FOREIGN, array('departmentid'), 'local_peertutoring_dept', array('id'));

        // Launch drop key department.
        $dbman->drop_key($table, $key);

        // Define key tutor (foreign) to be dropped form local_peertutoring_session.
        $table = new xmldb_table('local_peertutoring_session');
        $key = new xmldb_key('tutor', XMLDB_KEY_FOREIGN, array('tutorid'), 'user', array('id'));

        // Launch drop key tutor.
        $dbman->drop_key($table, $key);

        // Define key student (foreign) to be dropped form local_peertutoring_session.
        $table = new xmldb_table('local_peertutoring_session');
        $key = new xmldb_key('student', XMLDB_KEY_FOREIGN, array('studentid'), 'user', array('id'));

        // Launch drop key student.
        $dbman->drop_key($table, $key);

        // Define key course (foreign) to be dropped form local_peertutoring_session.
        $table = new xmldb_table('local_peertutoring_session');
        $key = new xmldb_key('course', XMLDB_KEY_FOREIGN, array('courseid'), 'local_peertutoring_course', array('id'));

        // Launch drop key course.
        $dbman->drop_key($table, $key);

        // Define key type (foreign) to be dropped form local_peertutoring_session.
        $table = new xmldb_table('local_peertutoring_session');
        $key = new xmldb_key('type', XMLDB_KEY_FOREIGN, array('typeid'), 'local_peertutoring_type', array('id'));

        // Launch drop key type.
        $dbman->drop_key($table, $key);

        // Define key rating (foreign) to be dropped form local_peertutoring_session.
        $table = new xmldb_table('local_peertutoring_session');
        $key = new xmldb_key('ratingrating', XMLDB_KEY_FOREIGN, array('ratingid'), 'local_peertutoring_rating', array('id'));

        // Launch drop key rating.
        $dbman->drop_key($table, $key);

        // Define key user (foreign-unique) to be dropped form local_peertutoring_tutor.
        $table = new xmldb_table('local_peertutoring_tutor');
        $key = new xmldb_key('user', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'user', array('id'));

        // Launch drop key user.
        $dbman->drop_key($table, $key);

        // Changing the default of field departmentid on table local_peertutoring_course to drop it.
        $table = new xmldb_table('local_peertutoring_course');
        $field = new xmldb_field('departmentid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of default for field departmentid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field tutorid on table local_peertutoring_session to drop it.
        $table = new xmldb_table('local_peertutoring_session');
        $field = new xmldb_field('tutorid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of default for field tutorid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field studentid on table local_peertutoring_session to drop it.
        $table = new xmldb_table('local_peertutoring_session');
        $field = new xmldb_field('studentid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'tutorid');

        // Launch change of default for field studentid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field courseid on table local_peertutoring_session to drop it.
        $table = new xmldb_table('local_peertutoring_session');
        $field = new xmldb_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'studentid');

        // Launch change of default for field courseid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field typeid on table local_peertutoring_session to drop it.
        $table = new xmldb_table('local_peertutoring_session');
        $field = new xmldb_field('typeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'courseid');

        // Launch change of default for field typeid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field ratingid on table local_peertutoring_session to drop it.
        $table = new xmldb_table('local_peertutoring_session');
        $field = new xmldb_field('ratingid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'typeid');

        // Launch change of default for field ratingid.
        $dbman->change_field_default($table, $field);

        // Changing the default of field userid on table local_peertutoring_tutor to drop it.
        $table = new xmldb_table('local_peertutoring_tutor');
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of default for field userid.
        $dbman->change_field_default($table, $field);

        // Define key department (foreign) to be added form local_peertutoring_course.
        $table = new xmldb_table('local_peertutoring_course');
        $key = new xmldb_key('department', XMLDB_KEY_FOREIGN, array('departmentid'), 'local_peertutoring_dept', array('id'));

        // Launch add key department.
        $dbman->add_key($table, $key);

        // Define key tutor (foreign) to be added form local_peertutoring_session.
        $table = new xmldb_table('local_peertutoring_session');
        $key = new xmldb_key('tutor', XMLDB_KEY_FOREIGN, array('tutorid'), 'user', array('id'));

        // Launch add key tutor.
        $dbman->add_key($table, $key);

        // Define key student (foreign) to be added form local_peertutoring_session.
        $table = new xmldb_table('local_peertutoring_session');
        $key = new xmldb_key('student', XMLDB_KEY_FOREIGN, array('studentid'), 'user', array('id'));

        // Launch add key student.
        $dbman->add_key($table, $key);

        // Define key course (foreign) to be added form local_peertutoring_session.
        $table = new xmldb_table('local_peertutoring_session');
        $key = new xmldb_key('course', XMLDB_KEY_FOREIGN, array('courseid'), 'local_peertutoring_course', array('id'));

        // Launch add key course.
        $dbman->add_key($table, $key);

        // Define key type (foreign) to be added form local_peertutoring_session.
        $table = new xmldb_table('local_peertutoring_session');
        $key = new xmldb_key('type', XMLDB_KEY_FOREIGN, array('typeid'), 'local_peertutoring_type', array('id'));

        // Launch add key type.
        $dbman->add_key($table, $key);

        // Define key rating (foreign) to be added form local_peertutoring_session.
        $table = new xmldb_table('local_peertutoring_session');
        $key = new xmldb_key('rating', XMLDB_KEY_FOREIGN, array('ratingid'), 'local_peertutoring_rating', array('id'));

        // Launch add key rating.
        $dbman->add_key($table, $key);

        // Define key user (foreign-unique) to be added to local_peertutoring_tutor.
        $table = new xmldb_table('local_peertutoring_tutor');
        $key = new xmldb_key('user', XMLDB_KEY_FOREIGN_UNIQUE, array('userid'), 'user', array('id'));

        // Launch add key user.
        $dbman->add_key($table, $key);

        // Peertutoring savepoint reached.
        upgrade_plugin_savepoint(true, 2019072209, 'local', 'peertutoring');
    }

    return true;

}
