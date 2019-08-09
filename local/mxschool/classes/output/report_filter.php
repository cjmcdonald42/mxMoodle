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
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\output;

defined('MOODLE_INTERNAL') || die();

class report_filter implements \renderable, \templatable {

    /** @var string Default search text, null if there is no search option.*/
    private $search;
    /** @param array Array of local_mxschool\output\dropdown objects.*/
    private $dropdowns;
    /** @var array Array of local_mxschool\output\button objects.*/
    private $buttons;
    /** @var bool Whether to display a print button.*/
    private $printable;

    /**
     * @param string $search Default search text, null if there is no search option.
     * @param array $dropdowns Array of local_mxschool\output\dropdown objects.
     * @param array $buttons Array of local_mxschool\output\button objects.
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
     * @return stdClass Object with properties filterable, url, dropdowns, searchable, search, buttons, and printable.
     */
    public function export_for_template(\renderer_base $output) {
        global $PAGE;
        return (object) array(
            'url' => $PAGE->url,
            'dropdowns' => array_map(function($dropdown) use($output) {
                return $output->render($dropdown);
            }, $this->dropdowns),
            'searchable' => isset($this->search),
            'search' => $this->search,
            'filterable' => isset($this->search) || count($data->dropdowns),
            'buttons' => array_map(function($button) use($output) {
                return $output->render($button);
            }, $this->buttons),
            'printable' => $this->printable
        );
    }

}
