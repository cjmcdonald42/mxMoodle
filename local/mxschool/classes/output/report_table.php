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
 * Renderable class for report tables for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\output;

defined('MOODLE_INTERNAL') || die();

class report_table implements \renderable, \templatable {

    /** @var local_mxschool\table The table object to output to the template.*/
    private $table;
    /** @var array|bool Array of headers as ['text', 'length'] to prepend or false.*/
    private $headers;
    /** @var int The number of rows to output. If null, uses the default number from the admin setting.*/
    private $rows;

    /**
     * @param local_mxschool\table $table The table object to output to the template.
     * @param array|bool $headers Array of headers as ['text', 'length'] to prepend or false.
     * @param int $rows The number of rows to output. If null, uses the default number from the admin setting.
     */
    public function __construct($table, $headers = false, $rows = null) {
        $this->table = $table;
        $this->headers = $headers;
        $this->rows = $rows;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output The renderer which is rendering this renderable.
     * @return stdClass Object with properties table and headers.
     */
    public function export_for_template(\renderer_base $output) {
        $data = new \stdClass();
        ob_start();
        $this->table->out(isset($this->rows) ? $this->rows : get_config('local_mxschool', 'table_size'), true);
        $data->table = ob_get_clean();
        $data->headers = $this->headers ? json_encode($this->headers) : $this->headers;
        return $data;
    }

}
