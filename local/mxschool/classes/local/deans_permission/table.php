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
use local_mxschool\output\comment;
use local_mxschool\output\checkbox;

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
        $columns = array('student', 'event', 'event_info', 'sport', 'missing', 'times_away', 'parent_perm', 'sports_perm', 'studyhours_perm', 'class_perm', 'comment', 'dean_perm', 'form_submitted');
	   if ($this->is_downloading()) {
		  unset($columns[array_search('sports_perm', $columns)]);
		  unset($columns[array_search('studyhours_perm', $columns)]);
		  unset($columns[array_search('class_perm', $columns)]);
	   }
        $headers = $this->generate_headers($columns, 'deans_permission:report');
        $sortable = array('student', 'form_submitted');
        $centered = array('event', 'sport', 'parent_perm', 'sports_perm', 'studyhours_perm', 'class_perm', 'comment', 'dean_perm', 'form_submitted');
        parent::__construct('deans_permission_table', $columns, $headers, $sortable, $centered, $filter, !$this->is_downloading());

        $fields = array(
		   'dp.id', 'dp.userid', "CONCAT(u.lastname, ', ', u.firstname) AS student", 'su.grade', 'su.boarding_status', 'dpe.name AS event', 'dp.event_info',
		   'dp.sport', 'dp.missing_sports', 'dp.missing_studyhours', 'dp.missing_class', 'dp.times_away', 'dp.parent_perm', 'dp.sports_perm', 'dp.studyhours_perm',
		   'dp.comment', 'dp.class_perm', 'dp.dean_perm', 'dp.form_submitted'
        );
        $from = array(
		   '{local_mxschool_deans_perm} dp', '{user} u ON dp.userid = u.id', '{local_mxschool_student} su ON dp.userid = su.userid',
		   '{local_mxschool_dp_event} dpe ON dp.event_id = dpe.id'
        );
	   $where = array('u.deleted = 0'
	   );
	   if($filter->approved == 'approved') $where[] = 'dp.dean_perm = 2';
	   else if($filter->approved == 'under_review') $where[] = '(dp.dean_perm = 0 OR dp.dean_perm = 1)';
	   if($filter->event) $where[] = "dpe.id = {$filter->event}";
        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename', 'dp.sport', 'dp.event');
        $this->define_sql($fields, $from, $where, $searchable, $filter->search);
    }

    /**
	* Formats the student column to include full name, grade, and boarding_status.
	*/
    protected function col_student($values) {
	    if($this->is_downloading()) return $values->student;
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

    protected function col_parent_perm($values) {
	    if($this->is_downloading()) return format_boolean($values->parent_perm);
	    global $PAGE;
	    $output = $PAGE->get_renderer('local_mxschool');
	    $renderable = new checkbox($values->id, 'local_mxschool_deans_perm', 'parent_perm', $values->parent_perm);
	    return $output->render($renderable);
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

    protected function col_comment($values) {
	    if($this->is_downloading()) return $values->comment;
	    global $PAGE;
	    $output = $PAGE->get_renderer('local_mxschool');
	    $renderable = new comment($values->id, $values->comment, 'Edit', 'Save', 'local_mxschool_deans_perm');
	    return $output->render($renderable);
    }

    protected function col_dean_perm($values) {
	    	if($this->is_downloading()) {
			if($values->dean_perm == 0) return 'No';
			if($values->dean_perm == 1) return 'Under Review';
			if($values->dean_perm == 2) return 'Yes';
		}
		global $PAGE;
		$output = $PAGE->get_renderer('local_mxschool');
		$renderable = new alternating_button($values->id, $values->userid, $values->dean_perm, 'deans', 'deans_permission');
		return $output->render($renderable);
    }

    protected function col_form_submitted($values) {
	    return isset($values->form_submitted) ? format_date('n/j/y g:i A', $values->form_submitted) : '';
    }

    /**
	* Formats the actions column.
	*/
    protected function col_actions($values) {
	   return isset($values->id) ? $this->edit_icon('/local/mxschool/deans_permission/form.php', $values->id).
	    						  $this->delete_icon($values->id): '';
    }
}
