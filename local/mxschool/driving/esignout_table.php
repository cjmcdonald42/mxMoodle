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
 * eSignout table for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_table.php');

class esignout_table extends local_mxschool_table {

    /**
     * Creates a new esignout_table.
     *
     * @param string $uniqueid a unique identifier for the table.
     * @param stdClass $filter any filtering for the table - could include type, date, and search.
     */
    public function __construct($uniqueid, $filter) {
        $columns = array('student', 'type');
        if ($filter->type !== 'driver') {
            $columns[] = 'driver';
        }
        if ($filter->type !== 'passenger') {
            $columns[] = 'passengers';
        }
        $columns = array_merge($columns, array('destination', 'date', 'departurereturn', 'permissionfrom'));
        $headers = array();
        foreach ($columns as $column) {
            $headers[] = get_string("esignout_report_header_{$column}", 'local_mxschool');
        }
        $columns[] = 'actions';
        $headers[] = get_string('report_header_actions', 'local_mxschool');
        $drivertext = get_string('esignout_report_select_type_driver', 'local_mxschool');
        $passengertext = get_string('esignout_report_select_type_passenger', 'local_mxschool');
        $fields = array(
            'es.id', "CONCAT(u.lastname, ', ', u.firstname) AS student", 'u.firstname', 'u.alternatename',
            "IF(es.id = d.id, '$drivertext', '$passengertext') AS type",
            "IF(es.id = d.id, '-', CONCAT(du.lastname, ', ', du.firstname)) AS driver",
            "du.firstname AS driverfirstname", "du.alternatename AS driveralternatename", "IF(es.id = d.id, (
                SELECT COUNT(driverid) FROM {local_mxschool_esignout} WHERE driverid = es.id AND deleted = 0), '-'
            ) AS passengers", 'd.destination', 'd.departure_date_time AS date', "'' AS departurereturn",
            'd.departure_date_time AS departuretime', 'd.return_date_time AS returntime',
            "CONCAT(f.lastname, ', ', f.firstname) AS permissionfrom"
        );
        $from = array(
            '{local_mxschool_esignout} es', '{user} u ON es.userid = u.id', '{local_mxschool_esignout} d ON es.driverid = d.id',
            '{user} du ON d.userid = du.id', '{user} f ON es.permission_from = f.id'
        );
        if ($filter->date) {
            $starttime = new DateTime('now', core_date::get_server_timezone_object());
            $starttime->setTimestamp($filter->date);
            $endtime = clone $starttime();
            $endtime->modify('+1 day');
        }
        $where = array(
            'es.deleted = 0', 'u.deleted = 0',
            $filter->type === 'driver' ? 'es.id = d.id' : ($filter->type === 'passenger' ? 'es.id <> d.id' : ''),
            $filter->date ? "es.departure_date_time > {$starttime->getTimestamp()}" : '',
            $filter->date ? "es.departure_date_time < {$endtime->getTimestamp()}" : ''
        );
        $sortable = array('student', 'driver', 'date', 'permissionfrom');
        $urlparams = array('type' => $filter->type, 'date' => $filter->date, 'search' => $filter->search);
        $centered = array('type', 'driver', 'passengers', 'date', 'departurereturn');
        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename', 'd.destination');
        parent::__construct(
            $uniqueid, $columns, $headers, $sortable, 'date', $fields, $from, $where, $urlparams, $centered,
            $filter->search, $searchable
        );
    }

    /**
     * Formats the driver column to "last, first (alternate)" or "last, first".
     */
    protected function col_driver($values) {
        return $values->driver.(
            $values->driver !== '-' && $values->driveralternatename && $values->driveralternatename !== $values->driverfirstname
            ? " ($values->driveralternatename)" : ''
        );
    }

    /**
     * Formats the date column to 'n/j/y'.
     */
    protected function col_date($values) {
        return date('n/j/y', $values->date);
    }

    /**
     * Formats the departure time and return time column to 'g:i A - g:i A'.
     */
    protected function col_departurereturn($values) {
        return date('g:i A', $values->departuretime).' - '.date('g:i A', $values->returntime);
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return $this->edit_icon('/local/mxschool/driving/esignout_enter.php', $values->id).$this->delete_icon($values->id);
    }

}
