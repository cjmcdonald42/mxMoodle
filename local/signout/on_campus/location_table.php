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
 * Location table for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../mxschool/classes/mx_table.php');

class location_table extends local_mxschool_table {

    /**
     * Creates a new location_table.
     */
    public function __construct() {
        $columns = array('name', 'grade', 'enabled', 'start', 'end', 'warning');
        $headers = $this->generate_headers($columns, 'on_campus_location_report', 'local_signout');
        $sortable = array('name', 'grade');
        $centered = array('grade', 'enabled', 'start', 'end');
        parent::__construct('location_table', $columns, $headers, $sortable, $centered);

        $fields = array(
            'l.id', 'l.name', 'l.grade', 'l.enabled', 'l.start_date AS start', 'l.end_date AS end', 'l.warning AS warning'
        );
        $from = array('{local_signout_location} l');
        $where = array('l.deleted = 0');
        $this->set_sql($fields, $from, $where);
    }

    /**
     * Formats the enabled column.
     */
    protected function col_enabled($values) {
        return format_boolean($values->enabled);
    }

    /**
     * Formats the start date column to 'n/j/y'.
     */
    protected function col_start($values) {
        return isset($values->start) ? format_date('n/j/y', $values->start) : '-';
    }

    /**
     * Formats the end date column to 'n/j/y'.
     */
    protected function col_end($values) {
        return isset($values->end) ? format_date('n/j/y', $values->end) : '-';
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return $this->edit_icon('/local/signout/on_campus/location_edit.php', $values->id) . $this->delete_icon($values->id);
    }

}
