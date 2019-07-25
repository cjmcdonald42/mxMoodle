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
 * @package     PACKAGE
 * @subpackage  SUBPACKAGE
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace PACKAGE\local\SUBPACKAGE;

defined('MOODLE_INTERNAL') || die();

class NAME_form extends \local_mxschool\form {

    /**
     * Form definition.
     */
    protected function definition() {
        // TODO: Store other parameters from $this->_custom_data.

        // TODO: Define any static options.

        $fields = array(
            '' => array(
                'id' => self::ELEMENT_HIDDEN_INT
            // Other hidden fields.
            ), 'HEADER' => array(
                'FIELD' => array(
                    'ELEMENT' => 'TYPE', // ETC.
                ) // ETC.
            ) // ETC.
        );
        parent::set_fields($fields, 'PREFIX', /* true or false for top actions */, 'PACKAGE');
    }
}
