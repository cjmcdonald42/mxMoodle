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
            'student', 'type', 'passengers', 'passengercount', 'driver', 'destination', 'departuredate', 'departuretime',
            'approver', 'signin'
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
        if ($filter->date) {
            unset($columns[array_search('departuredate', $columns)]);
        }
        $headers = array_map(function($column) {
            return get_string("off_campus_report_header_{$column}", 'local_signout');
        }, $columns);
        $columns[] = 'actions';
        $headers[] = get_string('report_header_actions', 'local_mxschool');
        $fields = array(
            'oc.id', 'oc.userid', "CONCAT(u.lastname, ', ', u.firstname) AS student", 'u.firstname', 'u.alternatename', 'oc.type',
            'oc.passengers', 'du.id AS driverid', 'd.destination', 'd.departure_time AS departuredate',
            'd.departure_time AS departuretime', "CONCAT(a.lastname, ', ', a.firstname) AS approver", 'oc.sign_in_time AS signin',
            'oc.time_created AS timecreated'
        );
        $from = array(
            '{local_signout_off_campus} oc', '{user} u ON oc.userid = u.id', '{local_signout_off_campus} d ON oc.driverid = d.id',
            '{user} du ON d.userid = du.id', '{user} a ON oc.approverid = a.id'
        );
        $where = array('oc.deleted = 0', 'u.deleted = 0');
        if ($filter->date) {
            $starttime = generate_datetime($filter->date);
            $endtime = clone $starttime;
            $endtime->modify('+1 day');
            $where[] = "d.departure_time >= {$starttime->getTimestamp()}";
            $where[] = "d.departure_time < {$endtime->getTimestamp()}";
        }
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
            $starttime = generate_datetime('midnight')->getTimestamp();
            $where[] = "d.departure_time >= {$starttime}";
        }
        $sortable = array('student', $filter->date ? 'departuretime' : 'departuredate', 'approver');
        $urlparams = array('type' => $filter->type, 'date' => $filter->date, 'search' => $filter->search);
        $centered = array('type', 'driver', 'passengers', 'passengercount', 'departuredate', 'departuretime', 'signin');
        $searchable = array(
            'u.firstname', 'u.lastname', 'u.alternatename', 'du.firstname', 'du.lastname', 'du.alternatename', 'd.destination',
            'a.firstname', 'a.lastname'
        );
        parent::__construct(
            'off_campus_table', $columns, $headers, $sortable, $filter->date ? 'departuretime' : 'departuredate', $fields, $from,
            $where, $urlparams, $centered, $filter->search, $searchable, array(), false
        );
        $this->column_class('signin', "{$this->column_class['signin']} sign-in");
    }

    /**
     * Formats the passengers column.
     */
    protected function col_passengers($values) {
        global $DB;
        if ($values->type !== 'Driver') {
            return '-';
        }
        $passengers = array_filter(array_map(function($passenger) use($DB) {
            return format_student_name_userid($passenger);
        }, json_decode($values->passengers)));
        return count($passengers) ? implode('<br>', $passengers) : get_string('off_campus_report_nopassengers', 'local_signout');
    }

    /**
     * Formats the passenger count column.
     */
    protected function col_passengercount($values) {
        global $DB;
        if ($values->type !== 'Driver') {
            return '-';
        }
        $submitted = $DB->count_records_sql(
            "SELECT COUNT(oc.id) FROM {local_signout_off_campus} oc LEFT JOIN {user} u ON oc.userid = u.id
             WHERE oc.driverid = ? AND oc.deleted = 0 AND u.deleted = 0", array($values->id)
        ) - 1;
        $count = count(array_filter(json_decode($values->passengers), function($passenger) use ($DB) {
            return !$DB->get_field('user', 'deleted', array('id' => $passenger));
        }));
        return "{$submitted} / {$count}";
    }

    /**
     * Formats the driver column to "last, first (alternate)" or "last, first".
     */
    protected function col_driver($values) {
        return $values->type === 'Passenger' ? format_student_name_userid($values->driverid) : '-';
    }

    /**
     * Formats the departure date column to 'n/j/y'.
     */
    protected function col_departuredate($values) {
        return format_date('n/j/y', $values->departuredate);
    }

    /**
     * Formats the departure time column to 'g:i A'.
     */
    protected function col_departuretime($values) {
        return format_date('g:i A', $values->departuretime);
    }

    /**
     * Formats the sign-in time column to 'g:i A'.
     */
    protected function col_signin($values) {
        return isset($values->signin) ? (
            format_date('n/j/y', $values->departuredate) === format_date('n/j/y', $values->signin)
                ? format_date('g:i A', $values->signin) : format_date('n/j/y g:i A', $values->signin)
        ) : '-';
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        global $USER, $PAGE;
        if (!$this->isstudent) {
            return $this->edit_icon('/local/signout/off_campus/off_campus_enter.php', $values->id)
                . $this->delete_icon($values->id);
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
        $renderable = new \local_signout\output\signin_button($values->id, 'local_signout_off_campus');
        if (
            !get_config('local_signout', 'off_campus_form_ipenabled')
            || $_SERVER['REMOTE_ADDR'] === get_config('local_signout', 'school_ip')
        ) {
            return $output->render($renderable);
        }
        return '-';
    }

}
