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
 * Renderable class for buttons for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\output;

defined('MOODLE_INTERNAL') || die();

class comment implements \renderable, \templatable {

	/** @var string The user's id.*/
	public $userid;
    /** @var string The text to display in the comment.*/
    public $comment_text;
    /** @var string The text to display in the edit button.*/
    public $edit_button_text;
    /** @var string The text to display in the save button.*/
    public $save_button_text;
    /** @var string The table to edit the comment in.*/
    public $table;


    public function __construct($userid, $comment_text, $edit_button_text, $save_button_text, $table) {
	   $this->userid = $userid;
        $this->comment_text = htmlspecialchars_decode($comment_text, ENT_QUOTES);
	   $this->edit_button_text = $edit_button_text;
	   $this->save_button_text = $save_button_text;
	   $this->table = $table;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output The renderer which is rendering this renderable.
     * @return stdClass Object with properties depending on the button type.
     */
    public function export_for_template(\renderer_base $output) {
        return (object) array('userid' => $this->userid,
		   				'comment_text' => $this->comment_text,
	   					'edit_button_text' => $this->edit_button_text,
						'save_button_text' => $this->save_button_text,
						'table' => $this->table);
    }

}
