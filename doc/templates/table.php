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
 * TODO: Description.
 *
 * @package    PACKAGE
 * @subpackage SUBPACKAGE
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once('PATH_TO_PLUGIN_HOME/classes/mx_table.php');

class NAME_table extends local_mxschool_table {

    /**
     * Creates a new NAME_table.
     *
     * @param stdClass $filter Any filtering for the table - could include properties // TODO: list properties.
     * TODO: Description of other parameters
     */
    public function __construct($filter) {
        // $columns = TODO: array of column identifiers;
        $headers = $this->generate_headers($columns, 'NAME_report');
        // $sortable = TODO: array of column identifiers that are sortable (first element is default);
        // $centered = TODO: array of column identifiers that are centered;
        parent::__construct('UNIQUE_ID', $columns, $headers, $sortable, $centered, $filter, /* false to turn off actions column, *//* false to sort descending (useful for timestamps) */);

        // $fields = TODO: array of fields from the database;
        // $from = TODO: array of database tables;
        // $where = TODO: array of constraints;
        // $searchable = TODO: array of fields that are searchable;
        $this->set_sql($fields, $from, $where, $searchable, $filter->search); // NOTE: remove last two parameters if the table has no search functionality.
    }

    // TODO: any column transformations as protected functions.

}
