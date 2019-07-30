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
 * Renderable class for selection buttons for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  advisor_selection
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\output;

defined('MOODLE_INTERNAL') || die();

class selection_button extends button {

    /** @var int The user id of the student whose record the button interacts with.*/
    private $student;
    /** @var int The user id of the selected advisor.*/
    private $option;

    /**
     * @param string $text The text to display on the button.
     * @param int $student The user id of the student whose record the button interacts with.
     * @param int $option The user id of the selected advisor.
     */
    public function __construct($text, $student, $option) {
        parent::__construct($text);
        $this->student = $student;
        $this->option = $option;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output The renderer which is rendering this renderable.
     * @return stdClass Object with properties text and value and text.
     */
    public function export_for_template(\renderer_base $output) {
        $data = parent::export_for_template($output);
        $value = new \stdClass();
        $value->student = $this->student;
        $value->choice = $this->option;
        $data->value = json_encode($value);
        return $data;
    }

}
