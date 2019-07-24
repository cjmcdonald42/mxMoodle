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
 * Combined signout table for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../mxschool/classes/mx_table.php');
require_once(__DIR__.'/classes/output/renderable.php');

class combined_table extends local_mxschool_table {

    /**
     * Creates a new combined_table.
     *
     * @param stdClass $filter any filtering for the table - could include properties dorm and search.
     */
    public function __construct($filter) {
        global $USER;
        $columns = array('student', 'grade', 'dorm', 'status', 'location', 'signouttime');
        if ($filter->dorm > 0) {
            unset($columns[array_search('dorm', $columns)]);
        }
        $headers = $this->generate_headers($columns, 'combined_report', 'local_signout');
        $sortable = array('student', 'grade', 'status', 'signouttime');
        $centered = array('grade', 'status', 'signouttime');
        parent::__construct('combined_table', $columns, $headers, $sortable, $centered, $filter);

        $fields = array(
            's.id', 's.userid', 'onc.id AS onid', 'offc.id AS offid', "CONCAT(u.lastname, ', ', u.firstname) AS student", 's.grade',
            's.dormid', 'l.name AS location', 'onc.other', 'dr.destination',
            "CASE
                WHEN onc.id IS NOT NULL AND (offc.id IS NULL OR onc.time_created > offc.time_created) THEN 'signed_out_on_campus'
                WHEN offc.id IS NOT NULL AND (onc.id IS NULL OR offc.time_created > onc.time_created) THEN 'signed_out_off_campus'
                WHEN s.boarding_status = 'Boarder' THEN 'signed_in_boarder'
                ELSE 'signed_in_day'
            END AS status",
            "CASE
                WHEN onc.id IS NOT NULL AND (offc.id IS NULL OR onc.time_created > offc.time_created) THEN onc.time_created
                WHEN offc.id IS NOT NULL AND (onc.id IS NULL OR offc.time_created > onc.time_created) THEN offc.time_created
                ELSE NULL
            END AS signouttime"
        );
        $starttime = generate_datetime('midnight')->getTimestamp();
        $from = array(
            '{local_mxschool_student} s', '{user} u ON s.userid = u.id', '{local_mxschool_dorm} d ON s.dormid = d.id',
            "{local_signout_on_campus} onc ON onc.id = (
                SELECT oc.id
                FROM {local_signout_on_campus} oc LEFT JOIN {local_signout_location} l ON oc.locationid = l.id
                WHERE s.userid = oc.userid AND oc.time_created >= {$starttime} AND oc.sign_in_time IS NULL AND oc.deleted = 0
                                           AND l.deleted = 0
                ORDER BY oc.time_created DESC
                LIMIT 1
            )",
            "{local_signout_off_campus} offc ON offc.id = (
                SELECT oc.id
                FROM {local_signout_off_campus} oc LEFT JOIN {local_signout_off_campus} d ON oc.driverid = d.id
                WHERE s.userid = oc.userid AND oc.time_created >= {$starttime} AND oc.sign_in_time IS NULL AND oc.deleted = 0
                                           AND d.deleted = 0
                ORDER BY oc.time_created DESC
                LIMIT 1
            )",
            '{local_signout_location} l ON onc.locationid = l.id', '{local_signout_off_campus} dr ON offc.driverid = dr.id'
        );
        $where = array('u.deleted = 0');
        if ($filter->dorm) {
            $where[] = $this->get_dorm_where($filter->dorm);
        }
        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename', 'l.name', 'oc.other', 'dr.destination');
        $this->set_sql($fields, $from, $where, $searchable, $filter->search);
    }

    /**
     * Formats the status column.
     */
    protected function col_status($values) {
        return get_string("combined_report_status_{$values->status}", 'local_signout');
    }

    /**
     * Formats the location column.
     */
    protected function col_location($values) {
        switch ($values->status) {
            case 'signed_out_on_campus':
                return $values->other ?? $values->location;
            case 'signed_out_off_campus':
                return $values->destination;
            default:
                return '';
        }
    }

    /**
     * Formats the sign out time time column to 'g:i A'.
     */
    protected function col_signouttime($values) {
        return isset($values->signouttime) ? format_date('g:i A', $values->signouttime) : '';
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        switch ($values->status) {
            case 'signed_out_on_campus':
                return $this->edit_icon('/local/signout/on_campus/on_campus_enter.php', $values->onid)
                    . $this->delete_icon($values->onid, 'on_campus');
            case 'signed_out_off_campus':
                return $this->edit_icon('/local/signout/off_campus/off_campus_enter.php', $values->offid)
                    . $this->delete_icon($values->offid, 'off_campus');
            default:
                return '';
        }
    }

}
