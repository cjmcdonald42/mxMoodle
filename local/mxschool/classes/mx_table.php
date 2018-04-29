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
 * Generic sql table with desired defaults to be used for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

class local_mxschool_table extends table_sql {

    /**
     * Creates a new table_sql with reasonable defaults.
     *
     * @param string $uniqueid a unique identifier for the table.
     * @param array $columns the columns of the table.
     * @param array $headers the headers of the table.
     * @param array $urlparams the parameters for the baseurl.
     */
    public function __construct($uniqueid, $columns, $headers, $urlparams) {
        parent::__construct($uniqueid);

        $this->define_columns($columns);
        $this->define_headers($headers);
        $this->define_baseurl(new moodle_url($PAGE->url, $urlparams));
        $this->collapsible(false);
    }

    /**
     * Creates an edit icon for the actions column of a table.
     *
     * @param string $url the url of the edit form.
     * @param int $id the id of the record to edit.
     * @return string the html for the edit icon.
     */
    protected function edit_icon($url, $id) {
        global $OUTPUT;
        return $OUTPUT->action_icon(
            new moodle_url($url, array('id' => $id)),
            new pix_icon('t/edit', get_string('edit'), 'core', array('class' => 'iconsmall'))
        );
    }

    /**
     * Creates a delete icon for the actions column of a table.
     *
     * @param int $id the id of the record to delete.
     * @return string the html for the delete icon.
     */
    protected function delete_icon($id) {
        global $OUTPUT;
        $warning = get_string('report_delete_warning', 'local_mxschool');
        return $OUTPUT->action_icon(
            new moodle_url($this->baseurl, array('action' => 'delete', 'id' => $id)),
            new pix_icon('t/delete', get_string('delete'), 'core', array('class' => 'iconsmall')),
            null, array('onclick' => "return confirm($warning)")
        );
    }

}
