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
 * Querys the database to create a list of all the available dorms.
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
 * Querys the database to create a list of all the available advisors.
 *
 * @return array the available advisors as id => name
 */
function get_advisor_list() {
    global $DB;
    $list = array();
    $advisors = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_faculty} f LEFT JOIN {user} u ON f.userid = u.id
         WHERE f.advisory_available = 'Yes' and f.advisory_closing = 'No'
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
 * Generates a select string for an SQL query.
 *
 * @param array $queryfields must be organized as [table => [abbreviation, join, fields => [header => name]]].
 * @return string the select string for the data
 */
function get_select_string($queryfields) {
    $selectfields = array();
    foreach ($queryfields as $table => $tablefields) {
        $abbreviation = $tablefields['abbreviation'];
        foreach ($tablefields['fields'] as $header => $name) {
            $selectfields[] = "{$abbreviation}.$header AS {$name}";
        }
    }
    return implode($selectfields, ', ');
}

/**
 * Generates a from string for an SQL query.
 *
 * @param array $queryfields must be organized as [table => [abbreviation, join, fields => [header => name]]].
 * @return string the from string for the data
 */
function get_from_string($queryfields) {
    $from = '';
    foreach ($queryfields as $table => $tablefields) {
        $abbreviation = $tablefields['abbreviation'];
        if (!isset($tablefields['join'])) {
            $from .= "{{$table}} {$abbreviation}";
        } else {
            $join = $tablefields['join'];
            $from .= " LEFT JOIN {{$table}} {$abbreviation} ON {$join}";
        }
    }
    return $from;
}
