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
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_signout\local;

defined('MOODLE_INTERNAL') || die();

class combined_table extends \local_mxschool\table {

    /** @var bool Whether the user is a proctor and personal information and actions should be omitted. */
    private $isproctor;

    /**
     * Creates a new combined_table.
     *
     * @param stdClass $filter Any filtering for the table - could include properties dorm and search.
     * @param bool $isproctor Whether the user is a proctor and personal information and actions should be omitted.
     */
    public function __construct($filter, $isproctor) {
        global $USER;
        $this->isproctor = $isproctor;
        $columns = array('student', 'grade', 'dorm', 'status', 'location', 'signouttime');
        if ($filter->dorm > 0) {
            unset($columns[array_search('dorm', $columns)]);
        }
        $headers = $this->generate_headers($columns, 'combined_report', 'local_signout');
        $sortable = array('student', 'grade', 'status', 'signouttime');
        $centered = array('grade', 'status', 'location', 'signouttime');
        parent::__construct('combined_table', $columns, $headers, $sortable, $centered, $filter, !$isproctor);

        $fields = array(
            's.id', 's.userid', 'onc.id AS onid', 'offc.id AS offid', "CONCAT(u.lastname, ', ', u.firstname) AS student", "CONCAT(ua.lastname, ', ', ua.firstname) AS advisor", 's.grade',
            's.dormid', 'l.name AS location', 'onc.other', 'offc.destination',
            "CASE
                WHEN onc.id IS NOT NULL AND (offc.id IS NULL OR onc.time_created > offc.time_created) THEN 'on_campus'
                WHEN offc.id IS NOT NULL AND (onc.id IS NULL OR offc.time_created > onc.time_created) THEN 'off_campus'
                ELSE NULL
            END AS status",
            "CASE
                WHEN onc.id IS NOT NULL AND (offc.id IS NULL OR onc.time_created > offc.time_created) THEN onc.time_created
                WHEN offc.id IS NOT NULL AND (onc.id IS NULL OR offc.time_created > onc.time_created) THEN offc.time_created
                ELSE NULL
            END AS signouttime"
        );
        $starttime = generate_datetime('midnight')->getTimestamp();
        $from = array(
            '{local_mxschool_student} s', '{user} u ON s.userid = u.id', '{user} ua ON s.advisorid = ua.id', '{local_mxschool_dorm} d ON s.dormid = d.id',
            "{local_signout_on_campus} onc ON onc.id = (
                SELECT oc.id
                FROM {local_signout_on_campus} oc LEFT JOIN {local_signout_location} l ON oc.locationid = l.id
                WHERE s.userid = oc.userid AND oc.deleted = 0 AND oc.time_created >= {$starttime} AND oc.sign_in_time IS NULL
                                           AND (oc.locationid = -1 OR l.deleted = 0)
                ORDER BY oc.time_created DESC
                LIMIT 1
            )",
            "{local_signout_off_campus} offc ON offc.id = (
                SELECT oc.id
                FROM {local_signout_off_campus} oc LEFT JOIN {local_signout_type} t ON oc.typeid = t.id
                                                   LEFT JOIN {local_signout_off_campus} d ON oc.driverid = d.id
                                                   LEFT JOIN {user} du ON d.userid = du.id
                WHERE s.userid = oc.userid AND oc.deleted = 0 AND oc.time_created >= {$starttime} AND oc.sign_in_time IS NULL
                                           AND (oc.typeid = -1 OR t.deleted = 0)
                                           AND (oc.driverid IS NULL OR d.deleted = 0 AND du.deleted = 0)
                ORDER BY oc.time_created DESC
                LIMIT 1
            )",
            '{local_signout_location} l ON onc.locationid = l.id', '{local_signout_off_campus} dr ON offc.driverid = dr.id'
        );
        $where = array('u.deleted = 0');
        if ($filter->dorm) {
            $where[] = $this->get_dorm_where($filter->dorm);
        }
        if ($filter->advisor) {
            $where[] = "ua.id = {$filter->advisor}";
        }
        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename', 'l.name', 'onc.other', 'offc.destination');
        $this->define_sql($fields, $from, $where, $searchable, $filter->search);
    }

    /**
     * Formats the status column.
     */
    protected function col_status($values) {
        switch ($values->status) {
            case 'on_campus':
                return get_string('combined_report:cell:status:on_campus', 'local_signout');
            case 'off_campus':
                return get_string('combined_report:cell:status:off_campus', 'local_signout');
            default:
                return '-';
        }
    }

    /**
     * Formats the location column.
     */
    protected function col_location($values) {
        switch ($values->status) {
            case 'on_campus':
                return $values->other ?? $values->location;
            case 'off_campus':
                return $this->isproctor ? '-' : $values->destination;
            default:
                return '-';
        }
    }

    /**
     * Formats the sign out time time column to 'g:i A'.
     */
    protected function col_signouttime($values) {
        return isset($values->signouttime) ? format_date('g:i A', $values->signouttime) : '-';
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        switch ($values->status) {
            case 'on_campus':
                return $this->edit_icon('/local/signout/on_campus/form.php', $values->onid)
                    . $this->delete_icon($values->onid, 'on_campus');
            case 'off_campus':
                return $this->edit_icon('/local/signout/off_campus/form.php', $values->offid)
                    . $this->delete_icon($values->offid, 'off_campus');
            default:
                return '';
        }
    }

}
