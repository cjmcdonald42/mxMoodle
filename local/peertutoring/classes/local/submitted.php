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
 * Email notification for when a deans permission form is submitted for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  deans_permission
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_peertutoring\local;

defined('MOODLE_INTERNAL') || die();

class submitted extends \local_mxschool\notification {

    /**
     * @param int $id The id of the deans permission form which has been submitted.
     *                The default value of 0 indicates a template email that should not be sent.
     * @throws coding_exception If the specified record does not exist.
     */
     public function __construct($id = 0) {
         parent::__construct('peer_tutor_submitted', $id);
         global $DB;

         $userid = $DB->get_field('local_peertutoring_session', 'studentid', array('id' => $id));

         array_push(
             $this->recipients, $DB->get_record('user', array('id' => $userid)),
             $DB->get_record('user', array('id' => get_student_advisor_id($userid)))
 	    );
     }

     /**
      * @return array The list of strings which can serve as tags for the notification.
      */
     public function get_tags() {
         return array_merge(parent::get_tags(), array('total', 'table'));
     }

}
