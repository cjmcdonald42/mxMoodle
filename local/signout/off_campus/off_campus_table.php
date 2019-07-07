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
 * Off-campus signout table for Middlesex School's eSignout Subplugin.
 *
 * @package    local_signout
 * @subpackage off_campus
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../mxschool/classes/mx_table.php');
require_once(__DIR__.'/../classes/output/renderable.php');

class off_campus_table extends local_mxschool_table {

    /** @var bool Whether the user is a student and only their records should be displayed. */
    private $isstudent;

    /**
     * Creates a new off_campus_table.
     *
     * @param stdClass $filter any filtering for the table - could include type, date, and search.
     * @param bool $isstudent Whether the user is a student and only their records should be displayed.
     */
    public function __construct($filter, $isstudent) {
        global $USER;
        $this->isstudent = $isstudent;
        $columns = array(
            'student', 'type', 'passengers', 'passengercount', 'driver', 'destination', 'date', 'departure', 'approver', 'signin'
        );
        if ($filter->type) {
            if ($filter->type === 'Driver' || $filter->type === 'Passenger' || $filter->type === 'Parent') {
                unset($columns[array_search('type', $columns)]);
            }
            if ($filter->type !== 'Driver') {
                unset($columns[array_search('passengers', $columns)]);
                unset($columns[array_search('passengercount', $columns)]);
            }
            if ($filter->type !== 'Passenger') {
                unset($columns[array_search('driver', $columns)]);
            }
        }
        $headers = array_map(function($column) {
            return get_string("off_campus_report_header_{$column}", 'local_signout');
        }, $columns);
        $columns[] = 'actions';
        $headers[] = get_string('report_header_actions', 'local_mxschool');
        $fields = array(
            'oc.id', 'oc.userid', "CONCAT(u.lastname, ', ', u.firstname) AS student", 'u.firstname', 'u.alternatename', 'oc.type',
            'oc.passengers', "du.firstname AS driverfirstname", "du.alternatename AS driveralternatename", "IF(oc.type = 'Driver', (
                SELECT COUNT(driverid) - 1 FROM {local_signout_off_campus} WHERE driverid = oc.id AND deleted = 0), '-'
            ) AS passengercount", "IF(oc.type = 'Passenger', CONCAT(du.lastname, ', ', du.firstname), '-') AS driver",
            'd.destination', 'd.departure_time AS date', 'd.departure_time AS departure',
            "CONCAT(a.lastname, ', ', a.firstname) AS approver", 'oc.sign_in_time AS signin, oc.time_created AS timecreated'
        );
        $from = array(
            '{local_signout_off_campus} oc', '{user} u ON oc.userid = u.id', '{local_signout_off_campus} d ON oc.driverid = d.id',
            '{user} du ON d.userid = du.id', '{user} a ON oc.approverid = a.id'
        );
        if ($filter->date) {
            $starttime = generate_datetime($filter->date);
            $endtime = clone $starttime;
            $endtime->modify('+1 day');
        }
        $where = array(
            'oc.deleted = 0', 'u.deleted = 0',
            $filter->date ? "d.departure_time >= {$starttime->getTimestamp()}" : '',
            $filter->date ? "d.departure_time < {$endtime->getTimestamp()}" : ''
        );
        if ($filter->type) {
            $types = array('Driver', 'Passenger', 'Parent');
            $otherstring = implode(' AND ', array_map(function($type) {
                return "oc.type <> '{$type}'";
            }, $types));
            $where[] = in_array($filter->type, $types) ? "oc.type = '{$filter->type}'" : $otherstring;
        }
        if ($isstudent) {
            $include = array(
                "oc.userid = {$USER->id}", "d.userid = {$USER->id}",
                "(SELECT COUNT(id) FROM {local_signout_off_campus} WHERE driverid = oc.id AND userid = {$USER->id})",
                "(SELECT COUNT(id) FROM {local_signout_off_campus} WHERE driverid = d.id AND userid = {$USER->id})"
            );
            $where[] = '('.implode(' OR ', $include).')';
            $starttime = generate_datetime('midnight');
            $where[] = "d.departure_time >= {$starttime->getTimestamp()}";
        }
        $sortable = array('student', 'driver', 'date', 'approver');
        $urlparams = array('type' => $filter->type, 'date' => $filter->date, 'search' => $filter->search);
        $centered = array('type', 'driver', 'passengers', 'passengercount', 'date', 'departure', 'signin');
        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename', 'd.destination');
        parent::__construct(
            'off_campus_table', $columns, $headers, $sortable, 'date', $fields, $from, $where, $urlparams, $centered,
            $filter->search, $searchable, array(), false
        );
        $this->column_class('signin', "{$this->column_class['signin']} sign-in");
    }

    /**
     * Formats the passengers column.
     */
    protected function col_passengers($values) {
        global $DB;
        if (!isset($values->passengers)) { // Not a driver.
            return '-';
        }
        $passengers = json_decode($values->passengers);
        return count($passengers) ? implode('<br>', array_map(function($passenger) use($DB) {
            $student = $DB->get_record(
                'user', array('id' => $passenger), "CONCAT(lastname, ', ', firstname) AS student, firstname, alternatename"
            );
            return $student->student . (
                $student->alternatename && $student->alternatename !== $student->firstname ? " ({$student->alternatename})" : ''
            );
        }, $passengers)) : get_string('off_campus_report_nopassengers', 'local_signout');
    }

    /**
     * Formats the passenger count column.
     */
    protected function col_passengercount($values) {
        if ($values->passengercount === '-') {
            return '-';
        }
        $count = count(json_decode($values->passengers));
        return "{$values->passengercount} / {$count}";
    }

    /**
     * Formats the driver column to "last, first (alternate)" or "last, first".
     */
    protected function col_driver($values) {
        return $values->driver . (
            $values->driver !== '-' && $values->driveralternatename && $values->driveralternatename !== $values->driverfirstname
                ? " ($values->driveralternatename)" : ''
        );
    }

    /**
     * Formats the date column to 'n/j/y'.
     */
    protected function col_date($values) {
        return format_date('n/j/y', $values->date);
    }

    /**
     * Formats the departure time column to 'g:i A'.
     */
    protected function col_departure($values) {
        return format_date('g:i A', $values->departure);
    }

    /**
     * Formats the sign-in time column to 'g:i A'.
     */
    protected function col_signin($values) {
        return $values->signin ? (
            format_date('n/j/y', $values->date) === format_date('n/j/y', $values->signin) ? format_date('g:i A', $values->signin)
                : format_date('n/j/y g:i A', $values->signin)
        ) : '-';
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        global $USER, $PAGE;
        if (!$this->isstudent) {
            return $this->edit_icon('/local/signout/off_campus/off_campus_enter.php', $values->id) . $this->delete_icon($values->id);
        }
        if ($values->userid !== $USER->id) {
            return '-';
        }
        if ($values->signin) {
            return '&#x2705;';
        }
        $editwindow = get_config('local_signout', 'off_campus_edit_window');
        $editcutoff = generate_datetime($values->timecreated);
        $editcutoff->modify("+{$editwindow} minutes");
        if (generate_datetime()->getTimestamp() < $editcutoff->getTimestamp()) {
            return $this->edit_icon('/local/signout/off_campus/off_campus_enter.php', $values->id);
        }
        $output = $PAGE->get_renderer('local_signout');
        $renderable = new \local_signout\output\signin_button($values->id);
        if (
            !get_config('local_signout', 'off_campus_form_ipenabled')
            || $_SERVER['REMOTE_ADDR'] === get_config('local_signout', 'school_ip')
        ) {
            return $output->render($renderable);
        }
        return '-';
    }

}
