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
 * Form for bulk importing student pictures for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage user_management
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_form.php');

class picture_import_form extends local_mxschool_form {

    /**
     * Form definition.
     */
    protected function definition() {
        $fields = array(
            '' => array(
                'clear' => array(
                    'element' => 'advcheckbox', 'name' => null,
                    'text' => get_string('user_management_picture_import_clear', 'local_mxschool')
                ),
                'pictures' => array('element' => 'filemanager', 'options' => array(
                    'subdirs' => 0, 'accepted_types' => array('.jpg'), 'return_types' => FILE_INTERNAL
                ))
            )
        );
        $this->set_fields($fields, 'user_management_picture_import');

        $mform = $this->_form;
        $mform->hideIf('pictures', 'clear', 'checked');
    }

}
