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
 * Generic class to encapsulate dropdown fields for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class local_mxschool_dropdown {

    /** @var string $name The name of the dropdown.*/
    public $name;
    /** @param array $options The options for the dropdown.*/
    public $options;
    /** @param string $selected The initially selected option.*/
    public $selected;
    /** @param array|bool $default A 'nothing' option or false if there is no such option.*/
    public $nothing;

    /**
     * @param string $name The name of the dropdown.
     * @param array $options The options for the dropdown.
     * @param string $selected The initially selected option.
     * @param string|bool $default A 'nothing' option or false if there is no such option.
     */
    public function __construct($name, $options, $selected, $default = false) {
        $this->name = $name;
        $this->options = $options;
        $this->selected = $selected;
        $this->nothing = is_string($default) ? array('' => $default) : false;
    }

}
