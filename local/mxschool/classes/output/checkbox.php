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
 * Renderable class for checkboxes for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\output;

defined('MOODLE_INTERNAL') || die();

class checkbox implements \renderable, \templatable {

    /** @var string The value attribute of the checkbox.*/
    private $value;
    /** @var string The table in the database which the checkbox interacts with.*/
    private $table;
    /** @var string The field in the database which the checkbox interacts with.*/
    private $field;
    /** @var bool Whether the checkbox should be checked by default.*/
    private $checked;

    /**
     * @param string $value The value attribute of the checkbox.
     * @param string $table The table in the database which the checkbox interacts with.
     * @param string $field The field in the database which the checkbox interacts with.
     * @param bool $checked Whether the checkbox should be checked by default.
     */
    public function __construct($value, $table, $field, $checked) {
        $this->value = $value;
        $this->table = $table;
        $this->field = $field;
        $this->checked = $checked;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output The renderer which is rendering this renderable.
     * @return stdClass Object with properties value, name, checked, and table.
     */
    public function export_for_template(\renderer_base $output) {
        $data = new \stdClass();
        $data->value = $this->value;
        $data->table = $this->table;
        $data->field = $this->field;
        $data->checked = $this->checked;
        return $data;
    }

}
