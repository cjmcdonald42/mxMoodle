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
 * Renderable class for legend tables for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  checkin
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\output;

defined('MOODLE_INTERNAL') || die();

class legend_table implements \renderable, \templatable {

    /** @var string The header text for the table.*/
    private $header;
    /** @var array Objects with properties lefttext, righttext, leftclass, and rightclass for each row of the table.*/
    private $rows;

    /**
     * @param string $header The header text for the table.
     * @param array $rows Objects with properties lefttext, righttext, leftclass, and rightclass for each row of the table.
     */
    public function __construct($header, $rows) {
        $this->header = $header;
        $this->rows = $rows;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output The renderer which is rendering this renderable.
     * @return stdClass Object with properties header and rows.
     */
    public function export_for_template($output) {
        $data = new \stdClass();
        $data->header = $this->header;
        $data->rows = $this->rows;
        return $data;
    }

}
