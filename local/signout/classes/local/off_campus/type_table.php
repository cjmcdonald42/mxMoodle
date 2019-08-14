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
 * Type table for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @subpackage  off_campus
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_signout\local\off_campus;

defined('MOODLE_INTERNAL') || die();

class type_table extends \local_mxschool\table {

    /**
     * Creates a new type_table.
     */
    public function __construct() {
        $columns = array(
            'name', 'permissions', 'grade', 'boardingstatus', 'weekend', 'enabled', 'start', 'end', 'formwarning', 'emailwarning'
        );
        $headers = $this->generate_headers($columns, 'off_campus:type_report', 'local_signout');
        $sortable = array('name', 'permissions', 'grade', 'boardingstatus', 'weekend', 'enabled', 'start', 'end');
        $centered = array(
            'permissions', 'grade', 'boardingstatus', 'weekend', 'enabled', 'start', 'end', 'formwarning', 'emailwarning'
        );
        parent::__construct('type_table', $columns, $headers, $sortable, $centered);

        $fields = array(
            't.id', 't.required_permissions AS permissions', 't.name', 't.grade', 't.boarding_status AS boardingstatus',
            't.weekend_only AS weekend', 't.enabled', 't.start_date AS start', 't.end_date AS end', 't.form_warning AS formwarning',
            't.email_warning AS emailwarning'
        );
        $from = array('{local_signout_type} t');
        $where = array('t.deleted = 0');
        $this->define_sql($fields, $from, $where);
    }

    /**
     * Formats the permissions column.
     */
    protected function col_permissions($values) {
        return $values->permissions ?? '-';
    }

    /**
     * Formats the weekend column.
     */
    protected function col_weekend($values) {
        return format_boolean($values->weekend);
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
     * Formats the formwarning column.
     */
    protected function col_formwarning($values) {
        return isset($values->permissions) ? '-' : ($values->formwarning ?? '');
    }

    /**
     * Formats the emailwarning column.
     */
    protected function col_emailwarning($values) {
        return isset($values->permissions) ? '-' : (
            $values->emailwarning ?? get_string('off_campus:notification:warning:default', 'local_signout')
        );
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return $this->edit_icon('/local/signout/off_campus/type_edit.php', $values->id) . $this->delete_icon($values->id);
    }

}
