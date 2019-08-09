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
 * Renderable class for reports for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\output;

defined('MOODLE_INTERNAL') || die();

class report implements \renderable, \templatable {

    /** @var report_table The table for the report.*/
    private $table;
    /** @var report_filter The filter for the report.*/
    private $filter;

    /**
     * @param local_mxschool\table $table The table object to output to the template.
     * @param string $search Default search text, null if there is no search option.
     * @param array $dropdowns Array of local_mxschool\output\dropdown objects.
     * @param array $buttons Array of button objects.
     * @param bool $printable Whether to display a print button.
     * @param array|bool $headers Array of headers as ['text', 'length'] to prepend or false.
     */
    public function __construct(
        $table, $search = null, $dropdowns = array(), $buttons = array(), $printable = false, $headers = false
    ) {
        $this->table = new report_table($table, $headers);
        $this->filter = new report_filter($search, $dropdowns, $buttons, $printable);
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output The renderer which is rendering this renderable.
     * @return stdClass Object with properties filter and table.
     */
    public function export_for_template(\renderer_base $output) {
        return (object) array('filter' => $output->render($this->filter), 'table' => $output->render($this->table));
    }

}
