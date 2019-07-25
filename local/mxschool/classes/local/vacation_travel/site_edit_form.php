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
 * Form for editing vacation travel site data for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  vacation_travel
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\vacation_travel;

defined('MOODLE_INTERNAL') || die();

class site_edit_form extends \local_mxschool\form {

    /**
     * Form definition.
     */
    protected function definition() {
        $typeoptions = array('Plane' => 'Plane', 'Train' => 'Train', 'Bus' => 'Bus', 'NYC Direct' => 'NYC Direct');

        $fields = array(
            '' => array(
                'id' => self::ELEMENT_HIDDEN_INT
            ),
            'site' => array(
                'name' => self::ELEMENT_TEXT_REQUIRED,
                'type' => array('element' => 'select', 'options' => $typeoptions, 'rules' => array('required')),
                'departureenabled' => self::ELEMENT_BOOLEAN_REQUIRED,
                'defaultdeparturetime' => array(
                    'element' => 'date_time_selector', 'options' => self::date_options_school_year(true)
                ),
                'returnenabled' => self::ELEMENT_BOOLEAN_REQUIRED,
                'defaultreturntime' => array(
                    'element' => 'date_time_selector', 'options' => self::date_options_school_year(true)
                )
            )
        );
        $this->set_fields($fields, 'vacation_travel_site_edit');
    }
}
