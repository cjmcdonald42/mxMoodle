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
 * Vacation travel transportation table for Middlesex School's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage vacation_travel
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_table.php');

class transportation_table extends local_mxschool_table {

    /**
     * Creates a new transportation_table.
     *
     * @param string $view The records to view - either 'departure' or 'return'.
     * @param stdClass $filter Any filtering for the table - could include properties mxtransportation, type, and search.
     * @param string $download Indicates whether the table is downloading.
     */
    public function __construct($view, $filter, $download) {
        $this->is_downloading($download, 'Vacation Travel Transportation', $view);
        $columns = array(
            'student', 'destination', 'phone', 'mxtransportation', 'type', 'site', 'details', 'carrier', 'number',
            'datetime', 'international', 'timemodified'
        );
        if ($filter->mxtransportation !== '') {
            unset($columns[array_search('mxtransportation', $columns)]);
            if (!$filter->mxtransportation) {
                unset($columns[array_search('site', $columns)]);
                unset($columns[array_search('international', $columns)]);
            }
        }
        if ($filter->type !== '') {
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
        if ($this->is_downloading()) {
            $columns[] = 'email';
        }
        $headers = array_map(function($column) use($view) {
            return get_string("vacation_travel_transportation_report_{$view}_header_{$column}", 'local_mxschool');
        }, $columns);
        if (!$this->is_downloading()) {
            $columns[] = 'actions';
            $headers[] = get_string('report_header_actions', 'local_mxschool');
        }
        $fields = array(
            's.id', 'u.id AS userid', 't.id AS tid', "CONCAT(u.lastname, ', ', u.firstname) AS student", 'u.firstname',
            'u.alternatename', 't.destination', 't.phone_number AS phone', 'dr.mx_transportation AS mxtransportation',
            'dr.type AS type', 'drs.name AS site', 'dr.details', 'dr.carrier', 'dr.transportation_number AS number',
            'dr.date_time AS datetime', 'dr.international', 't.time_modified AS timemodified', 'u.email'
        );
        $from = array(
            '{local_mxschool_student} s', '{user} u ON s.userid = u.id', '{local_mxschool_vt_trip} t ON s.userid = t.userid',
            "{local_mxschool_vt_transport} dr ON t.{$view}id = dr.id", '{local_mxschool_vt_site} drs ON dr.siteid = drs.id'
        );
        $where = array(
            'u.deleted = 0', "s.boarding_status = 'Boarder'", $filter->mxtransportation === ''
            ? '' : "dr.mx_transportation = {$filter->mxtransportation}", $filter->type ? "dr.type = '{$filter->type}'" : ''
        );
        $sortable = array(
            'student', 'destination', 'mxtransportation', 'type', 'site', 'carrier', 'number', 'datetime',
            'international', 'timemodified'
        );
        $urlparams = array(
            'view' => $view, 'mxtransportation' => $filter->mxtransportation, 'type' => $filter->type, 'search' => $filter->search
        );
        $centered = array('mxtransportation', 'type', 'site', 'details', 'carrier', 'number', 'datetime', 'international');
        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename', 't.destination');
        parent::__construct(
            'transportation_table', $columns, $headers, $sortable, 'timemodified', $fields, $from, $where, $urlparams, $centered,
            $filter->search, $searchable, array(), false
        );
    }

    /**
     * Formats the school transportation column to 'Yes' or 'No'.
     */
    protected function col_mxtransportation($values) {
        return $values->tid ? boolean_to_yes_no($values->mxtransportation) : '';
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
        return $values->tid ? ($values->datetime ? date('n/j/y g:i A', $values->datetime) : '-') : '';
    }

    /**
     * Formats the international column to 'Yes' or 'No'.
     */
    protected function col_international($values) {
        return $values->tid ? ($values->international ? boolean_to_yes_no($values->international) : '-') : '';
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return $values->tid ? $this->edit_icon('/local/mxschool/vacation_travel/vacation_enter.php', $values->tid) : '';
    }

    /**
     * Formats the time modified column to 'n/j/y g:i A'.
     */
    protected function col_timemodified($values) {
        return $values->tid ? date('n/j/y g:i A', $values->timemodified) : '';
    }

}
