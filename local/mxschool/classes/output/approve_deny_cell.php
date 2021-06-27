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
 * Renderable class for an approve/deny column for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @author      Cannon Caspar, Class of 2021 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2021 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\output;

defined('MOODLE_INTERNAL') || die();

class approve_deny_cell implements \renderable, \templatable {

    /** @var int id, the id of the row. */
    public $id;

    /** @var string field, the field name of the table to edit */
    public $field;

    /** @var string table, the name of the table to edit */
    public $table;

    /** @var int current status, the current status for the button, as follows:
    *	0: Under Review (displays both the approved and denied button)
    *	1: Approved (displays the text 'Approved' with an undo button)
    *	2: Denied (displasys the text 'Denied' with an undo button) */
    public $status;

    /** To send emails when approve or deny is clicked, hard code it for the specific table in the external_lib update_approve_deny_cell function */

    /**
     * @param string $text The text to display on the button.
     */
    public function __construct($id, $field, $table, $status) {
        $this->id = $id;
	   $this->field = $field;
	   $this->table = $table;
	   $this->status = $status;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output The renderer which is rendering this renderable.
     * @return stdClass Object with properties depending on the button type.
     */
    public function export_for_template(\renderer_base $output) {
        return (object) array('id' => $this->id,
   						'field' => $this->field,
						'table' => $this->table,
						'status' => $this->status);
    }

}
