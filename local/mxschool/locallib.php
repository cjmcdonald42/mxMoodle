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
 * Local functions for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Queries the database to create a list of all the available dorms.
 *
 * @return array the available dorms as id => name
 */
function get_dorms_list() {
    global $DB;
    $list = array();
    $dorms = $DB->get_records_sql("SELECT id, name FROM {local_mxschool_dorm} WHERE available = 'Yes' ORDER BY name");
    if ($dorms) {
        foreach ($dorms as $dorm) {
            $list[$dorm->id] = $dorm->name;
        }
    }
    return $list;
}

/**
 * Querys the database to create a list of all the faculty.
 *
 * @return array the faculty as userid => name
 */
function get_faculty_list() {
    global $DB;
    $list = array();
    $allfaculty = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_faculty} f LEFT JOIN {user} u ON f.userid = u.id
         WHERE u.deleted = 0
         ORDER BY name"
    );
    if ($allfaculty) {
        foreach ($allfaculty as $faculty) {
            $list[$faculty->id] = $faculty->name;
        }
    }
    return $list;
}

/**
 * Queries the database to create a list of all the available advisors.
 *
 * @return array the available advisors as userid => name
 */
function get_advisor_list() {
    global $DB;
    $list = array();
    $advisors = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_faculty} f LEFT JOIN {user} u ON f.userid = u.id
         WHERE u.deleted = 0 and f.advisory_available = 'Yes' and f.advisory_closing = 'No'
         ORDER BY name"
    );
    if ($advisors) {
        foreach ($advisors as $advisor) {
            $list[$advisor->id] = $advisor->name;
        }
    }
    return $list;
}

/**
 * Generates and performs an SQL query to retrieve a record from the database.
 *
 * @param array $queryfields must be organized as [table => [abbreviation, join, fields => [header => name]]].
 * @param string $where a where clause (without the WHERE keyword).
 * @param array $params and parameters for the where clause.
 */
function get_record($queryfields, $where, $params = array()) {
    global $DB;
    $selectarray = array();
    $fromarray = array();
    foreach ($queryfields as $table => $tablefields) {
        $abbreviation = $tablefields['abbreviation'];
        foreach ($tablefields['fields'] as $header => $name) {
            if (is_numeric($header)) {
                $header = $name;
            }
            $selectarray[] = "{$abbreviation}.$header AS {$name}";
        }
        if (!isset($tablefields['join'])) {
            $fromarray[] = "{{$table}} {$abbreviation}";
        } else {
            $join = $tablefields['join'];
            $fromarray[] = "LEFT JOIN {{$table}} {$abbreviation} ON {$join}";
        }
    }
    $select = implode($selectarray, ', ');
    $from = implode($fromarray, ' ');
    return $DB->get_record_sql("SELECT $select FROM $from WHERE $where", $params);
}

/**
 * Updates a record in the database or inserts it if it doesn't already exist.
 *
 * @param array $queryfields must be organized as [table => [abbreviation, join, fields => [header => name]]].
 * @param stdClass $data the new data to update the database with.
 */
function update_record($queryfields, $data) {
    global $DB;
    foreach ($queryfields as $table => $tablefields) {
        $record = new stdClass();
        foreach ($tablefields['fields'] as $header => $name) {
            if (is_numeric($header)) {
                $header = $name;
            }
            $record->$header = $data->$name;
        }
        if ($record->id) {
            $DB->update_record($table, $record);
        } else {
            $DB->insert_record($table, $record);
        }
    }
}
