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
 * Form for editing vacation travel preferences for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage vacation_travel
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_form.php');
require_once(__DIR__.'/../classes/notification/vacation_travel.php');

class preferences_form extends local_mxschool_form {

    /**
     * Form definition.
     */
    protected function definition() {
        $dateparameters = array(
            'startyear' => strftime('%Y', get_config('local_mxschool', 'dorms_open_date')),
            'stopyear' => strftime('%Y', get_config('local_mxschool', 'dorms_close_date')),
            'timezone'  => core_date::get_server_timezone_object()
        );

        $fields = array(
            'availability' => array(
                'start' => array('element' => 'group', 'children' => array(
                    'time' => self::time_selector(1),
                    'date' => array('element' => 'date_selector', 'parameters' => $dateparameters)
                )), 'stop' => array('element' => 'group', 'children' => array(
                    'time' => self::time_selector(1),
                    'date' => array('element' => 'date_selector', 'parameters' => $dateparameters)
                )), 'returnenabled' => array('element' => 'advcheckbox', 'name' => null, 'text' => get_string(
                        'vacation_travel_preferences_availability_returnenabled_text', 'local_mxschool'
                    ))
            ), 'notifications' => array(
                'submitted_tags' => self::email_tags(new \local_mxschool\local\vacation_travel\submitted()),
                'submitted_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
                'submitted_body' => self::ELEMENT_FORMATED_TEXT_REQUIRED,
                'unsubmitted_tags' => self::email_tags(new \local_mxschool\local\vacation_travel\unsubmitted_notification()),
                'unsubmitted_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
                'unsubmitted_body' => self::ELEMENT_FORMATED_TEXT_REQUIRED
            )
        );
        $this->set_fields($fields, 'vacation_travel_preferences', true);
    }

}
