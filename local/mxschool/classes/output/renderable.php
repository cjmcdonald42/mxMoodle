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
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

namespace local_mxschool\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * Renderable class for index pages.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class index_page implements renderable, templatable {

    /** @var array $links Array of links (displayText => url) to be passed to the template.*/
    private $links;

    /**
     * @param array $links Array of links (displayText => url) to be passed to the template.
     */
    public function __construct($links) {
        $this->links = $links;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass with property links which is an array of stdClass with properties text and url.
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;
        $data = new stdClass();
        $data->links = array();
        foreach ($this->links as $text => $url) {
            $data->links[] = array(text => $text, url => $CFG->wwwroot.$url);
        }
        return $data;
    }
}

/**
 * Renderable class for report pages.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_page implements renderable, templatable {

    /** @var mx_table $table table object to be outputed to the template.*/
    private $table;
    /** @var int $size  the number of rows to output.*/
    private $size;

    /**
     * @param mx_table $table table object to be outputed to the template.
     * @param int $size the number of rows to output
     */
    public function __construct($table, $size) {
        $this->table = $table;
        $this->size = $size;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass with property table which is an html string for the table.
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        ob_start();
        $this->table->out($this->size, true);
        $data->table = ob_get_clean();
        return $data;
    }

}
