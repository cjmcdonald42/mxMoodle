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
 * Renderable class for report filters for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\output;

defined('MOODLE_INTERNAL') || die();

class report_filter implements \renderable, \templatable {

    /** @var string Default search text, null if there is no search option.*/
    private $search;
    /** @param array Array of local_mxschool\dropdown objects.*/
    private $dropdowns;
    /** @var array Array of button objects.*/
    private $buttons;
    /** @var bool Whether to display a print button.*/
    private $printable;

    /**
     * @param string $search Default search text, null if there is no search option.
     * @param array $dropdowns Array of local_mxschool\dropdown objects.
     * @param array $buttons Array of button objects.
     * @param bool $printable Whether to display a print button.
     */
    public function __construct($search, $dropdowns, $buttons, $printable) {
        $this->search = $search;
        $this->dropdowns = $dropdowns;
        $this->buttons = $buttons;
        $this->printable = $printable;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output The renderer which is rendering this renderable.
     * @return stdClass Object with properties url, dropdowns, searchable, search, buttons, and printable.
     */
    public function export_for_template($output) {
        global $PAGE;
        $data = new \stdClass();
        $data->url = $PAGE->url;
        $data->dropdowns = array_map(function($dropdown) {
            return $dropdown->out();
        }, $this->dropdowns);
        $data->searchable = isset($this->search);
        $data->search = $this->search;
        $data->filterable = $data->searchable || count($data->dropdowns);
        $data->buttons = array_map(function($button) use($output) {
            return $output->render($button);
        }, $this->buttons);
        $data->printable = $this->printable;
        return $data;
    }

}
