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
 * Provides renderable classes for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

namespace local_mxschool\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;
use html_writer;

/**
 * Renderable class for index pages.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class index_page implements renderable, templatable {

    /** @var array $links array of links (displayText => url) to be passed to the template.*/
    private $links;

    /**
     * @param array $links array of links (displayText => url) to be passed to the template.
     */
    public function __construct($links) {
        $this->links = $links;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @return stdClass with property links which is an array of stdClass with properties text and url.
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;
        $data = new stdClass();
        $data->links = array();
        foreach ($this->links as $text => $url) {
            $data->links[] = array('text' => $text, 'url' => $CFG->wwwroot.$url);
        }
        return $data;
    }
}

/**
 * Renderable class for report pages.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_page implements renderable, templatable {

    /** @var mx_table $table table object to be outputed to the template.*/
    private $table;
    /** @var int $size the number of rows to output.*/
    private $size;
    /** @param array $dropdowns array of local_mxschool_dropdown objects.*/
    private $dropdowns;
    /** @var string $search default search text.*/
    private $search;

    /**
     * @param mx_table $table table object to be outputed to the template.
     * @param int $size the number of rows to output.
     * @param array $dropdowns array of local_mxschool_dropdown objects.
     * @param string $search default search text.
     */
    public function __construct($table, $size, $dropdowns, $search) {
        $this->table = $table;
        $this->size = $size;
        $this->dropdowns = $dropdowns;
        $this->search = $search;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @return stdClass with properties url, dropdowns, placeholder, search, submit, and table.
     */
    public function export_for_template(renderer_base $output) {
        global $PAGE;
        $data = new stdClass();
        $data->url = $PAGE->url;
        $data->dropdowns = array();
        foreach ($this->dropdowns as $dropdown) {
            $data->dropdowns[] = html_writer::select($dropdown->options, $dropdown->name, $dropdown->selected, $dropdown->nothing);
        }
        $data->placeholder = get_string('search').'...';
        $data->search = $this->search;
        $data->submit = get_string('search');
        ob_start();
        $this->table->out($this->size, true);
        $data->table = ob_get_clean();
        return $data;
    }

}

/**
 * Renderable class for form pages.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class form_page implements renderable, templatable {

    /** @var moodleform $form The form object to render.*/
    private $form;

    /**
     * @param moodleform $form The form object to render.
     */
    public function __construct($form) {
        $this->form = $form;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @return stdClass with property form which is an html string.
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        ob_start();
        $this->form->display();
        $data->form = ob_get_clean();
        return $data;
    }
}
