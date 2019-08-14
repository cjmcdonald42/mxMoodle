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
 * Form for editing off-campus signout preferences for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @subpackage  off_campus
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_signout\local\off_campus;

defined('MOODLE_INTERNAL') || die();

use local_signout\local\off_campus\submitted;

class preferences_form extends \local_mxschool\form {

    /**
     * Form definition.
     */
    protected function definition() {
        $fields = array(
            'config' => array(
                'edit_window' => array('element' => 'text', 'type' => PARAM_INT, 'rules' => array('required')),
                'trip_window' => array('element' => 'text', 'type' => PARAM_INT, 'rules' => array('required')),
                'enabled' => array(
                    'element' => 'advcheckbox', 'name' => null,
                    'text' => get_string('off_campus:preferences:config:enabled:text', 'local_signout')
                ),
                'permissions_active' => array(
                    'element' => 'advcheckbox', 'name' => null,
                    'text' => get_string('off_campus:preferences:config:permissions_active:text', 'local_signout')
                ),
                'ip_enabled' => array(
                    'element' => 'advcheckbox', 'name' => null,
                    'text' => get_string('off_campus:preferences:config:ip_enabled:text', 'local_signout', array(
                        'school' => get_config('local_signout', 'school_ip'), 'current' => $_SERVER['REMOTE_ADDR']
                    ))
                )
            ),
            'notifications' => array(
                'tags' => self::email_tags(new submitted()),
                'subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
                'body' => self::ELEMENT_FORMATTED_TEXT_REQUIRED
            ),
            'formtext' => array(
                'ip_form_error' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'ip_sign_in_error' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'passenger_instructions' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'bottom_instructions' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'confirmation' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'form_driver_no_passengers' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'form_passenger_parent' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'form_passenger_specific' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'form_passenger_over_21' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'form_rideshare_parent' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'form_rideshare_not_allowed' => self::ELEMENT_FORMATTED_TEXT_REQUIRED
            ),
            'emailtext' => array(
                'email_driver_no_passengers' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'email_passenger_parent' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'email_passenger_specific' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'email_passenger_over_21' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'email_rideshare_parent' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'email_rideshare_not_allowed' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'irregular' => self::ELEMENT_FORMATTED_TEXT_REQUIRED
            )
        );
        $this->set_fields($fields, 'off_campus:preferences', true, 'local_signout');
    }

}
