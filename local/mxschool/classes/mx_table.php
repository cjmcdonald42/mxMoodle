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

abstract class local_mxschool_table extends table_sql {

    /**
     * Creates a new table_sql with reasonable defaults.
     *
     * @param string $uniqueid A unique identifier for the table.
     * @param array $columns The columns of the table.
     * @param array $headers The headers of the table.
     * @param array $sortable The columns which can be sorted.
     * @param string $deafultsort The column to sort by before another column is specified.
     * @param array $fields The database fields to select.
     * @param array $from The database tables to query.
     * @param array $where The constaints on the query.
     * @param array $urlparams The parameters for the baseurl.
     * @param array $centered The columns whose text should be centered.
     * @param string $search The string to search for as a constraint, null indicates no search option.
     * @param array $searchable The database fields to search.
     * @param array $noprint The columns which should not be displayed if the page is printing.
     */
    public function __construct(
        $uniqueid, $columns, $headers, $sortable, $defaultsort, $fields, $from, $where, $urlparams,
        $centered = array(), $search = null, $searchable = array(), $noprint = array()
    ) {
        global $PAGE;

        parent::__construct($uniqueid);

        $this->define_columns($columns);
        $this->define_headers($headers);
        $this->sortable(true, $defaultsort);
        if (in_array('actions', $columns)) {
            $centered[] = $noprint[] = 'actions';
        }
        foreach ($columns as $column) {
            if (!in_array($column, $sortable)) {
                $this->no_sorting($column);
            }
            $columnclasses = array();
            if (in_array($column, $noprint)) {
                $columnclasses[] = 'noprint';
            }
            if (in_array($column, $centered)) {
                $columnclasses[] = 'centered';
            }
            if (count($columnclasses)) {
                $this->column_class($column, implode(' ', $columnclasses));
            }
        }

        $where[] = $search ? '(' . implode(' OR ', array_map(function($field) use($search) {
            return "$field LIKE '%$search%'";
        }, $searchable)) . ')' : '';

        $this->set_sql(implode(', ', $fields), implode(' LEFT JOIN ', $from), implode(' AND ', array_filter($where)));

        $this->define_baseurl(new moodle_url($PAGE->url, $urlparams));
        $this->collapsible(false);
    }

    /**
     * Formats the student column to "last, first (alternate)" or "last, first".
     */
    protected function col_student($values) {
        $alternatename = $values->alternatename && $values->alternatename !== $values->firstname ? " ($values->alternatename)" : '';
        return $values->student . $alternatename;
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
            null, array('onclick' => "return window.confirm(\"$warning\")")
        );
    }

}
