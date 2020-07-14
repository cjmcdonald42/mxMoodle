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
 * Renderable class for permission buttons for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @author	 Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\output;

defined('MOODLE_INTERNAL') || die();


/**
* NOTE: Permission value increases by one or resets to 0 each time the button is clicked
* Permission Values are as follows:
* @value int 0: No current value. If clicked will send an email to user specificed in external_lib according to package_name.
* @value int 1: Under Review. If clicked will change to approved.
* @value int 2: Approved. If clicked will reset to No Current Value.
*/
class permission_button implements \renderable, \templatable {

    /** @var int a unqiue id for the button. Can be the same as userid. */
    public $id;
    /** @var int The id of the user for whom to grant permission.*/
    public $userid;
    /** @var int The current permission value of the button.*/
    public $current_value;
    /** @var string A name for the button. Must be unique across the row. */
    public $name;
    /** @var string The name of the subpackage for which the button is being used. Can alter button actions in external_lib based on package_name */
    public $package_name;

    public function __construct($id, $userid, $current_value, $name, $package_name) {
	   $this->id = $id;
        $this->userid = $userid;
	   $this->current_value = $current_value;
	   $this->name = $name;
	   $this->package_name = $package_name;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output The renderer which is rendering this renderable.
     * @return stdClass Object with properties depending on the button type.
     */
    public function export_for_template(\renderer_base $output) {
        return (object) array('id' => $this->id,
		   				'userid' => $this->userid,
   						'current_value' => $this->current_value,
						'name' => $this->name,
						'package_name' => $this->package_name
					);
    }

}
