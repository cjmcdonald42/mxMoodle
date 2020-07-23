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
 * Renderable class for action buttons for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\output;

defined('MOODLE_INTERNAL') || die();


/**
* NOTE: The action script should go in the code of the page in which the button appears
*        nested in a: if(isset($_POST['{$name}'])) conditional. See checkin/attendance_report.php for example.
*/
class action_button implements \renderable, \templatable {

    /** @var string a unqiue name for the button. Include in conditional to perform action */
    public $name;
    /** @var string the text to display on the button */
    public $text;
    /** @var mixed value, an optional value parameter accessed in the action */
    public $value;

    public function __construct($name, $text, $vale=0) {
	    $this->name = $name;
	    $this->text = $text;
	    $this->value = $value;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output The renderer which is rendering this renderable.
     * @return stdClass Object with properties depending on the button type.
     */
    public function export_for_template(\renderer_base $output) {
        return (object) array(
						'name' => $this->name,
						'text' => $this->text,
						'value' => $this->value
					);
    }

}
