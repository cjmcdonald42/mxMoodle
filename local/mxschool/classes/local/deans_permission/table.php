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
 * Deans permission table for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  deans_permission
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\deans_permission;

defined('MOODLE_INTERNAL') || die();

use local_mxschool\output\alternating_button;

class table extends \local_mxschool\table {

    /**
     * Creates a new deans_permission_table.
     *
     * @param stdClass $filter Any filtering for the table
     *                         - could include properties submitted, gender, roomtype, double, and search.
     * @param string $download Indicates whether the table is downloading.
     */
    public function __construct($filter, $download) {
        $this->is_downloading($download, 'Deans\' Permission', 'Deans\' Permission');
        $columns = array('student', 'event', 'sport', 'missing', 'departure_time', 'return_time', 'sports_perm', 'studyhours_perm', 'class_perm', 'dean_perm');
        $headers = $this->generate_headers($columns, 'deans_permission:report');
        $sortable = array('student', 'departure_time', 'return_time');
        $centered = array('event', 'sport', 'departure_time', 'return_time', 'sports_perm', 'studyhours_perm', 'class_perm', 'dean_perm');
        parent::__construct('deans_permission_table', $columns, $headers, $sortable, $centered, $filter, false);

        $fields = array(
		   'dp.id', 'dp.userid', "CONCAT(u.lastname, ', ', u.firstname) AS student", 'su.grade', 'su.boarding_status', 'dp.event', 'dp.sport', 'dp.missing_sports',
		   'dp.missing_studyhours', 'dp.missing_class', 'dp.departure_time', 'dp.return_time', 'dp.sports_perm', 'dp.studyhours_perm',
		   'dp.class_perm', 'dp.dean_perm'
        );
        $from = array(
		   '{local_mxschool_deans_perm} dp', '{user} u ON dp.userid = u.id', '{local_mxschool_student} su ON dp.userid = su.userid'
        );
	   $where = array('u.deleted = 0'
	   );
        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename', 'dp.sport', 'dp.event');
        $this->define_sql($fields, $from, $where, $searchable, $filter->search);
    }

    /**
	* Formats the student column to include full name, grade, and boarding_status.
	*/
    protected function col_student($values) {
	    return "{$values->student}<br>
	    		  Grade {$values->grade} ({$values->boarding_status})";
    }

    /**
	* Formats the missing column to include each event the student will miss.
	*/
    protected function col_missing($values) {
	    $result = "";
	    if($values->missing_sports==1) $result.='Sports, ';
	    if($values->missing_studyhours==1) $result.='Study Hours, ';
	    if($values->missing_class==1) $result.='Class, ';
	    if(strlen($result) < 1) return 'Nothing';
	    else return substr($result, 0, -2);
    }

    /**
	* Formats the departure date and time column to 'n/j/y g:i A'.
	*/
    protected function col_departure_time($values) {
    	    return $values->departure_time ? format_date('n/j/y g:i A', $values->departure_time) : '';
    }

    /**
 	* Formats the return date and time column to 'n/j/y g:i A'.
 	*/
    protected function col_return_time($values) {
    	    return $values->return_time ? format_date('n/j/y g:i A', $values->return_time) : '';
    }

    protected function col_sports_perm($values) {
	    global $PAGE;
	    $output = $PAGE->get_renderer('local_mxschool');
	    $renderable = new alternating_button($values->id, $values->userid, $values->sports_perm, 'sports', 'deans_permission');
	    return $output->render($renderable);
    }

    protected function col_studyhours_perm($values) {
		global $PAGE;
		$output = $PAGE->get_renderer('local_mxschool');
		$renderable = new alternating_button($values->id, $values->userid, $values->studyhours_perm, 'studyhours', 'deans_permission');
		return $output->render($renderable);
    }

    protected function col_class_perm($values) {
		global $PAGE;
		$output = $PAGE->get_renderer('local_mxschool');
		$renderable = new alternating_button($values->id, $values->userid, $values->class_perm, 'class', 'deans_permission');
		return $output->render($renderable);
    }

    protected function col_dean_perm($values) {
		global $PAGE;
		$output = $PAGE->get_renderer('local_mxschool');
		$renderable = new alternating_button($values->id, $values->userid, $values->dean_perm, 'deans', 'deans_permission');
		return $output->render($renderable);
    }
}
