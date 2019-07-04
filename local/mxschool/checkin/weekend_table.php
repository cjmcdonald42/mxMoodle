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
 * Weekend checkin sheet table for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage checkin
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_table.php');

class weekend_table extends local_mxschool_table {

    /**
     * Creates a new weekend_table.
     *
     * @param stdClass $filter Any filtering for the table - could include dorm, weekend, submitted, and search keys.
     * @param int $start An offest for the start day.
     * @param int $end An offest for the end day.
     */
    public function __construct($filter, $start, $end) {
        global $DB;
        $columns1 = array('student', 'dorm', 'room', 'grade');
        if ($filter->dorm) {
            unset($columns1[array_search('dorm', $columns1)]);
            if ($DB->get_field('local_mxschool_dorm', 'type', array('id' => $filter->dorm)) === 'Day') {
                unset($columns1[array_search('room', $columns1)]);
            }
        }
        $headers1 = array_map(function($column) {
            return get_string("checkin_weekend_report_header_{$column}", 'local_mxschool');
        }, $columns1);
        $centered = array('room', 'grade', 'parent', 'invite', 'approved');
        $fields = array(
            's.id', 'wf.id AS wfid', "CONCAT(u.lastname, ', ', u.firstname) AS student", 'u.firstname', 'u.alternatename',
            'd.name AS dorm', 's.room', 's.grade', "'' AS clean", 'wf.parent', 'wf.invite', 'wf.approved',
            "'' AS destinationtransportation", 'wf.destination', 'wf.transportation', 'wf.phone_number AS phone',
            "'' AS departurereturn", 'wf.departure_date_time AS departuretime', 'wf.return_date_time AS returntime'
        );
        for ($i = 1; $i <= $end - $start + 1; $i++) {
            $columns1[] = $centered[] = "early_{$i}";
            $headers1[] = get_string('checkin_weekend_report_header_early', 'local_mxschool');
            $fields[] = "'' AS early_{$i}";
            $columns1[] = $centered[] = "late_{$i}";
            $headers1[] = get_string('checkin_weekend_report_header_late', 'local_mxschool');
            $fields[] = "'' AS late_{$i}";
        }
        $columns2 = array('clean', 'parent', 'invite', 'approved', 'destinationtransportation', 'phone', 'departurereturn');
        $headers2 = array_map(function($column) {
            return get_string("checkin_weekend_report_header_{$column}", 'local_mxschool');
        }, $columns2);
        $columns = array_merge($columns1, $columns2);
        $headers = array_merge($headers1, $headers2);
        $columns[] = 'actions';
        $headers[] = get_string('report_header_actions', 'local_mxschool');
        $from = array(
            '{local_mxschool_student} s', '{user} u ON s.userid = u.id', '{local_mxschool_dorm} d ON s.dormid = d.id',
            "{local_mxschool_weekend_form} wf ON s.userid = wf.userid AND wf.weekendid = {$filter->weekend} AND wf.active = 1"
        );
        $where = array(
            'u.deleted = 0', "s.boarding_status = 'Boarder'", $filter->dorm ? "s.dormid = {$filter->dorm}" : '',
            $filter->submitted === '1' ? "EXISTS (
                SELECT userid FROM mdl_local_mxschool_weekend_form wf
                WHERE s.userid = userid AND wf.active = 1 AND wf.weekendid = {$filter->weekend}
            )" : ($filter->submitted === '0' ? "NOT EXISTS (
                SELECT userid FROM mdl_local_mxschool_weekend_form wf
                WHERE s.userid = userid AND wf.active = 1 AND wf.weekendid = {$filter->weekend}
            )" : '')
        );
        $sortable = array('student', 'dorm', 'room', 'grade');
        if (!$filter->dorm) {
            unset($sortable[array_search('room', $sortable)]);
        }
        $urlparams = array(
            'dorm' => $filter->dorm, 'weekend' => $filter->weekend, 'start' => $filter->start, 'end' => $filter->end,
            'submitted' => $filter->submitted, 'search' => $filter->search
        );
        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename', 'wf.destination', 'wf.transportation');
        parent::__construct(
            'weekend_table', $columns, $headers, $sortable, 'student', $fields, $from, $where, $urlparams, $centered,
            $filter->search, $searchable
        );
    }

    /**
     * Formats the parent column to a checkbox.
     */
    protected function col_parent($values) {
        global $PAGE;
        if (!isset($values->wfid)) {
            return '';
        }
        $output = $PAGE->get_renderer('local_mxschool');
        $renderable = new \local_mxschool\output\checkbox($values->wfid, 'local_mxschool_weekend_form', 'parent', $values->parent);
        return $output->render($renderable);
    }

    /**
     * Formats the invite column to a checkbox.
     */
    protected function col_invite($values) {
        global $PAGE;
        if (!isset($values->wfid)) {
            return '';
        }
        $output = $PAGE->get_renderer('local_mxschool');
        $renderable = new \local_mxschool\output\checkbox($values->wfid, 'local_mxschool_weekend_form', 'invite', $values->invite);
        return $output->render($renderable);
    }

    /**
     * Formats the approved column to a checkbox and an email button.
     */
    protected function col_approved($values) {
        global $PAGE;
        if (!isset($values->wfid)) {
            return '';
        }
        $output = $PAGE->get_renderer('local_mxschool');
        $checkboxrenderable = new \local_mxschool\output\checkbox(
            $values->wfid, 'local_mxschool_weekend_form', 'approved', $values->approved
        );
        $buttonrenderable = new \local_mxschool\output\email_button(
            get_string('email_button_default', 'local_mxschool'), $values->wfid, 'weekend_form_approved', true
        );
        return "{$output->render($checkboxrenderable)}{$output->render($buttonrenderable)}";
    }

    /**
     * Formats the destination and transportation time column to 'destination<br>transportation'.
     */
    protected function col_destinationtransportation($values) {
        return ($values->destination ?: '') . '<br>' . ($values->transportation ?: '');
    }

    /**
     * Formats the departure and return time column to 'n/j/y g:i A'<br>'n/j/y g:i A'.
     */
    protected function col_departurereturn($values) {
        return ($values->departuretime ? date('n/j/y g:i A', $values->departuretime) : '')
               . '<br>' . ($values->returntime ? date('n/j/y g:i A', $values->returntime) : '');
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return $this->edit_icon('/local/mxschool/checkin/weekend_enter.php', $values->wfid)
               . $this->delete_icon($values->wfid);
    }

}
