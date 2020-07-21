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
 * Email notification for when class permission is requested for Middlesex's Dorm and Student Functions Plugin.
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

class class_permission_request extends deans_permission_notification {

    /**
     * @param int $id The id of the deans permission form which has been submitted.
     *                The default value of 0 indicates a template email that should not be sent.
     * @throws coding_exception If the specified record does not exist.
     */
    public function __construct($id = 0) {
	    parent::__construct('class_permission_request', $id);

	    global $DB;
	    $deans = $DB->get_record('user', array('id' => 2));
	    $deans->email = get_config('local_mxschool', 'academic_director_email_address');
	    $deans->firstname = 'Academic';
	    $deans->lastname = 'Director';

		array_push(
		    $this->recipients, $deans
		);
}
}
