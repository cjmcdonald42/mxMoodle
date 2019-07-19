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
 * Vacation travel site preferences table for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage vacation_travel
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_table.php');

class site_table extends local_mxschool_table {

    /**
     * Creates a new site_table.
     */
    public function __construct() {
        $columns = array('name', 'type', 'departureenabled', 'defaultdeparturetime', 'returnenabled', 'defaultreturntime');
        $headers = $this->generate_headers($columns, 'vacation_travel_site_report');
        $sortable = array('name', 'type');
        $centered = array('departureenabled', 'defaultdeparturetime', 'returnenabled', 'defaultreturntime');
        parent::__construct('site_table', $columns, $headers, $sortable, $centered);

        $fields = array(
            's.id', 's.name', 's.type', 's.enabled_departure AS departureenabled', 's.enabled_return AS returnenabled',
            's.default_departure_time AS defaultdeparturetime', 's.default_return_time AS defaultreturntime'
        );
        $from = array('{local_mxschool_vt_site} s');
        $where = array('s.deleted = 0');
        $this->set_sql($fields, $from, $where);
    }

    /**
     * Formats the departure enabled column to a checkbox.
     */
    protected function col_departureenabled($values) {
        global $PAGE;
        $output = $PAGE->get_renderer('local_mxschool');
        $renderable = new \local_mxschool\output\checkbox(
            $values->id, 'local_mxschool_vt_site', 'enabled_departure', $values->departureenabled
        );
        return $output->render($renderable);
    }

    /**
     * Formats the default departure time column to 'n/j/y g:i A'.
     */
    protected function col_defaultdeparturetime($values) {
        return $values->defaultdeparturetime ? format_date('n/j/y g:i A', $values->defaultdeparturetime) : '-';
    }

    /**
     * Formats the return enabled column to a checkbox.
     */
    protected function col_returnenabled($values) {
        global $PAGE;
        $output = $PAGE->get_renderer('local_mxschool');
        $renderable = new \local_mxschool\output\checkbox(
            $values->id, 'local_mxschool_vt_site', 'enabled_return', $values->returnenabled
        );
        return $output->render($renderable);
    }

    /**
     * Formats the default return time column to 'n/j/y g:i A'.
     */
    protected function col_defaultreturntime($values) {
        return $values->defaultreturntime ? format_date('n/j/y g:i A', $values->defaultreturntime) : '-';
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return $this->edit_icon('/local/mxschool/vacation_travel/site_edit.php', $values->id) . $this->delete_icon($values->id);
    }

}
