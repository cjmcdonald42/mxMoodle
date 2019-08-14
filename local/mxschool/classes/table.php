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
 * Generic sql table with desired defaults to be used for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

abstract class table extends \table_sql {

    /**
     * Creates a new table_sql with reasonable defaults.
     *
     * @param string $uniqueid A unique identifier for the table.
     * @param array $columns The columns of the table.
     * @param array $headers The headers of the table.
     * @param array $sortable The columns which can be sorted. The first element will be the default sort.
     * @param array $centered The columns whose text should be centered.
     * @param array $filter The parameters for the baseurl.
     * @param bool $actions Whether there should be an actions column.
     * @param bool $ascending Whether the default sort should be in ascending or descending order.
     */
    public function __construct(
        $uniqueid, $columns, $headers, $sortable, $centered = array(), $filter = array(), $actions = true, $ascending = true
    ) {
        global $PAGE;
        parent::__construct($uniqueid);

        if ($actions) {
            $columns[] = $centered[] = 'actions';
            $headers[] = get_string('report:header:actions', 'local_mxschool');
        }

        $this->define_columns(array_values($columns));
        $this->define_headers(array_values($headers));
        if ($sortable) {
            $this->sortable(true, $sortable[0], $ascending ? SORT_ASC : SORT_DESC);
            $this->define_baseurl(new \moodle_url($PAGE->url, (array) $filter));
        }
        foreach ($columns as $column) {
            if (!in_array($column, $sortable)) {
                $this->no_sorting($column);
            }
            if (in_array($column, $centered)) {
                $this->add_column_class($column, 'text-center');
            }
        }
        if ($actions) {
            $this->add_column_class('actions', 'hidden-print');
        }

        $this->collapsible(false);
    }

    /**
     * Sets the sql for the table.
     *
     * @param array $fields The database fields to select.
     * @param array $from The database tables to query.
     * @param array $where The constaints on the query.
     * @param array $searchable The database fields to search.
     * @param string $search The string to search for as a constraint, null indicates no search option.
     */
    public function define_sql($fields, $from, $where, $searchable = array(), $search = null) {
        if ($search) {
            $where[] = '(' . implode(' OR ', array_map(function($field) use($search) {
                return "{$field} LIKE '%{$search}%'";
            }, $searchable)) . ')';
        }

        parent::set_sql(implode(', ', $fields), implode(' LEFT JOIN ', $from), implode(' AND ', $where));
    }

    /**
     * Generates an array of localized strings to be used as the headers for the table based on an array of columns.
     *
     * @param array $columns The array of columns.
     * @param string $prefix The prefix for the language strings.
     *                       The expected form of the langauge strings is "{$prefix}_header_{$column}".
     * @param string $plugin The plugin to retrieve the language strings from.
     * @return array The localized headers.
     */
    protected function generate_headers($columns, $prefix, $plugin = 'local_mxschool') {
        return array_map(function($column) use($prefix, $plugin) {
            return get_string("{$prefix}:header:{$column}", $plugin);
        }, $columns);
    }

    /**
     * Adds a class to every cell in a column to be used for css formatting or Javascript interaction.
     *
     * @param string $column The name of the column to apply the class to.
     * @param string $class The class to add.
     */
    protected function add_column_class($column, $class) {
        $this->column_class($column, "{$this->column_class[$column]} {$class}");
    }

    /**
     * Generates a where string for the sql query based on a dorm filter parameter.
     *
     * NOTE: This method assumes the local_mxschool_dorm table is referenced as 'd' in the sql query.
     *
     * @param int $dorm A number which specifies the filtering:
     *                      -2 indicates all (boarding) dorms
     *                      -1 indicates all day houses
     *                      0 indicates all houses
     *                      any other integer references a particular dorm's id.
     * @return string An appropriate where string, as specified.
     */
    protected function get_dorm_where($dorm) {
        switch ($dorm) {
            case -2:
                return 'd.type = "Boarding"';
            case -1:
                return 'd.type = "Day"';
            case 0:
                return '';
            default:
                return "d.id = {$dorm}";
        }
    }

    /**
     * Formats the student column to "last, first (preferred)" or "last, first".
     *
     * NOTE: This method assumes that the student's user id is stored in the property 'userid'.
     */
    protected function col_student($values) {
        return format_student_name($values->userid);
    }

    /**
     * Formats the dorm column.
     *
     * NOTE: This method assumes that the dorm id is stored in the property 'dormid'.
     */
    protected function col_dorm($values) {
        return format_dorm_name($values->dormid);
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
            new \moodle_url($url, array('id' => $id)),
            new \pix_icon('t/edit', get_string('edit'), 'core', array('class' => 'iconsmall'))
        );
    }

    /**
     * Creates a delete icon for the actions column of a table.
     *
     * @param int $id The id of the record to delete.
     * @param string $table The table to delete from or null if it can be implied.
     * @return string The html for the delete icon.
     */
    protected function delete_icon($id, $table = null) {
        global $OUTPUT;
        $warning = get_string('report:delete_icon:confirmation', 'local_mxschool');
        $params = array('action' => 'delete', 'id' => $id);
        if (isset($table)) {
            $params['table'] = $table;
        }
        return $OUTPUT->action_icon(
            new \moodle_url($this->baseurl, $params),
            new \pix_icon('t/delete', get_string('delete'), 'core', array('class' => 'iconsmall')),
            null, array('onclick' => "return window.confirm(\"{$warning}\")")
        );
    }

}
