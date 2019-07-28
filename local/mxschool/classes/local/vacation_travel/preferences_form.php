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
 * Form for editing vacation travel preferences for Middlesex's Dorm and Student Functions Plugin.
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

use local_mxschool\local\vacation_travel\submitted;
use local_mxschool\local\vacation_travel\unsubmitted;

class preferences_form extends \local_mxschool\form {

    /**
     * Form definition.
     */
    protected function definition() {
        $fields = array(
            'availability' => array(
                'start' => array('element' => 'group', 'children' => array(
                    'time' => self::time_selector(1),
                    'date' => array('element' => 'date_selector', 'options' => self::date_options_school_year())
                )),
                'stop' => array('element' => 'group', 'children' => array(
                    'time' => self::time_selector(1),
                    'date' => array('element' => 'date_selector', 'options' => self::date_options_school_year())
                )),
                'returnenabled' => array(
                    'element' => 'advcheckbox', 'name' => null,
                    'text' => get_string('vacation_travel_preferences_availability_returnenabled_text', 'local_mxschool')
                )
            ),
            'notifications' => array(
                'submitted_tags' => self::email_tags(new submitted()),
                'submitted_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
                'submitted_body' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'unsubmitted_tags' => self::email_tags(new unsubmitted()),
                'unsubmitted_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
                'unsubmitted_body' => self::ELEMENT_FORMATTED_TEXT_REQUIRED
            )
        );
        $this->set_fields($fields, 'vacation_travel_preferences', true);
    }

}
