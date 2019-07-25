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
 * Vacation travel transportation table for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  vacation_travel
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\vacation_travel;

defined('MOODLE_INTERNAL') || die();

class transportation_table extends \local_mxschool\table {

    /**
     * Creates a new transportation_table.
     *
     * @param stdClass $filter Any filtering for the table - could include properties portion, mxtransportation, type, and search.
     * @param string $download Indicates whether the table is downloading.
     */
    public function __construct($filter, $download) {
        $this->is_downloading($download, 'Vacation Travel Transportation', $filter->portion);
        $columns = array(
            'student', 'dorm', 'destination', 'phone', 'mxtransportation', 'type', 'site', 'details', 'carrier', 'number',
            'datetime', 'international', 'timemodified', 'email'
        );
        if ($filter->mxtransportation !== '') {
            unset($columns[array_search('mxtransportation', $columns)]);
            if (!$filter->mxtransportation) {
                unset($columns[array_search('site', $columns)]);
                unset($columns[array_search('international', $columns)]);
            }
        }
        if ($filter->type) {
            unset($columns[array_search('type', $columns)]);
            if ($filter->type === 'Car' || $filter->type === 'Non-MX Bus') {
                unset($columns[array_search('site', $columns)]);
            }
            if ($filter->type === 'NYC Direct') {
                unset($columns[array_search('details', $columns)]);
            }
            if ($filter->type !== 'Plane' && $filter->type !== 'Train' && $filter->type !== 'Bus') {
                unset($columns[array_search('carrier', $columns)]);
                unset($columns[array_search('number', $columns)]);
            }
            if ($filter->type !== 'Plane') {
                unset($columns[array_search('international', $columns)]);
            }
        }
        if (!$this->is_downloading()) {
            unset($columns[array_search('email', $columns)]);
        }
        $headers = $this->generate_headers($columns, "vacation_travel_transportation_report_{$filter->portion}");
        $sortable = array(
            'timemodified', 'student', 'destination', 'mxtransportation', 'type', 'site', 'carrier', 'number', 'datetime',
            'international'
        );
        $centered = array('mxtransportation', 'type', 'site', 'details', 'carrier', 'number', 'datetime', 'international');
        parent::__construct(
            'transportation_table', $columns, $headers, $sortable, $centered, $filter, !$this->is_downloading(), false
        );

        $fields = array(
            's.id', 's.userid', 't.id AS tid', "CONCAT(u.lastname, ', ', u.firstname) AS student", 's.dormid', 't.destination',
            't.phone_number AS phone', 'dr.mx_transportation AS mxtransportation', 'dr.type AS type', 'drs.name AS site',
            'dr.details', 'dr.carrier', 'dr.transportation_number AS number', 'dr.date_time AS datetime', 'dr.international',
            't.time_modified AS timemodified', 'u.email'
        );
        $from = array(
            '{local_mxschool_student} s', '{user} u ON s.userid = u.id', '{local_mxschool_vt_trip} t ON s.userid = t.userid',
            "{local_mxschool_vt_transport} dr ON t.{$filter->portion}id = dr.id",
            '{local_mxschool_vt_site} drs ON dr.siteid = drs.id'
        );
        $where = array('u.deleted = 0', "s.boarding_status = 'Boarder'");
        if ($filter->mxtransportation !== '') {
            $where[] = "dr.mx_transportation = '{$filter->mxtransportation}'";
        }
        if ($filter->type) {
            $where[] = "dr.type = '{$filter->type}'";
        }
        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename', 't.destination');
        $this->set_sql($fields, $from, $where, $searchable, $filter->search);
    }

    /**
     * Formats the school transportation column to 'Yes' or 'No'.
     */
    protected function col_mxtransportation($values) {
        return $values->tid ? format_boolean($values->mxtransportation) : '';
    }

    /**
     * Formats the type column.
     */
    protected function col_type($values) {
        return $values->tid ? ($values->type ?: '-') : '';
    }

    /**
     * Formats the site column.
     */
    protected function col_site($values) {
        return $values->tid ? (
            isset($values->site) ? (
                $values->site ?: get_string('vacation_travel_transportation_report_site_other', 'local_mxschool')
            ) : '-'
        ) : '';
    }

    /**
     * Formats the details column.
     */
    protected function col_details($values) {
        return $values->tid ? ($values->details ?: '-') : '';
    }

    /**
     * Formats the carrier column.
     */
    protected function col_carrier($values) {
        return $values->tid ? ($values->carrier ?: '-') : '';
    }

    /**
     * Formats the number column.
     */
    protected function col_number($values) {
        return $values->tid ? ($values->number ?: '-') : '';
    }

    /**
     * Formats the date and time column to 'n/j/y g:i A'.
     */
    protected function col_datetime($values) {
        return $values->tid ? ($values->datetime ? format_date('n/j/y g:i A', $values->datetime) : '-') : '';
    }

    /**
     * Formats the international column to 'Yes' or 'No'.
     */
    protected function col_international($values) {
        return $values->tid ? ($values->international ? format_boolean($values->international) : '-') : '';
    }

    /**
     * Formats the time modified column to 'n/j/y g:i A'.
     */
    protected function col_timemodified($values) {
        return $values->tid ? format_date('n/j/y g:i A', $values->timemodified) : '';
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return $values->tid ? $this->edit_icon('/local/mxschool/vacation_travel/vacation_enter.php', $values->tid) : '';
    }

}
