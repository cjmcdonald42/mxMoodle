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
 * Renderable class for email buttons for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\output;

defined('MOODLE_INTERNAL') || die();

class email_button extends button {

    /** @var string The string identifier for the email.*/
    private $emailclass;
    /** @var int The value attribute of the button.*/
    private $value;
    /** @var bool Whether the button needs user confirmation before it sends a bulk email.*/
    private $requireconfirmation;
    /** @var bool Whether the button should be hidden by default and should have show/hide functionality.*/
    private $hidden;

    /**
     * @param string $text The text to display on the button.
     * @param int $value The value attribute of the button.
     * @param string $emailclass The string identifier for the email.
     * @param bool $requireconfirmation Whether the button needs user confirmation before it sends a bulk email.
     * @param bool $hidden Whether the button should be hidden by default and should have show/hide functionality.
     */
    public function __construct($text, $emailclass, $value = 0, $requireconfirmation = true, $hidden = false) {
        parent::__construct($text);
        $this->emailclass = $emailclass;
        $this->value = $value;
        $this->requireconfirmation = $requireconfirmation;
        $this->hidden = $hidden;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output The renderer which is rendering this renderable.
     * @return stdClass Object with properties text, value, emailclass, requireconfirmation, and hidden.
     */
    public function export_for_template($output) {
        $data = parent::export_for_template($output);
        $data->emailclass = $this->emailclass;
        $data->value = $this->value;
        $data->requireconfirmation = $this->requireconfirmation;
        $data->hidden = $this->hidden;
        return $data;
    }

}
