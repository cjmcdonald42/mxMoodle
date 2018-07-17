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
 * Edit form for effectiveness rating records for Middlesex School's Peer Tutoring Subplugin.
 *
 * @package    local_peertutoring
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../mxschool/classes/mx_form.php');

class rating_edit_form extends local_mxschool_form {

    /**
     * Form definition.
     */
    protected function definition() {
        $id = $this->_customdata['id'];

        $fields = array(
            '' => array('id' => parent::ELEMENT_HIDDEN_INT),
            'rating' => array('displaytext' => array(
                'element' => 'text', 'type' => PARAM_TEXT, 'attributes' => array('size' => 60), 'rules' => array('required')
            ))
        );
        parent::set_fields($fields, 'rating_edit', false, 'local_peertutoring');
    }
}
