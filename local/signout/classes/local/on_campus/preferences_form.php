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
 * Form for editing on-campus signout preferences for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @subpackage  on_campus
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_signout\local\on_campus;

defined('MOODLE_INTERNAL') || die();

class preferences_form extends \local_mxschool\form {

    /**
     * Form definition.
     */
    protected function definition() {
        $fields = array(
            'config' => array(
                'enabled' => array(
                    'element' => 'advcheckbox', 'name' => null,
                    'text' => get_string('on_campus:preferences:config:enabled:text', 'local_signout')
                ),
                'ip_enabled' => array(
                    'element' => 'advcheckbox', 'name' => null,
                    'text' => get_string('on_campus:preferences:config:ip_enabled:text', 'local_signout', array(
                        'school' => get_config('local_signout', 'school_ip'), 'current' => $_SERVER['REMOTE_ADDR']
                    ))
                ),
                'confirmation_enabled' => array(
                    'element' => 'advcheckbox', 'name' => null,
                    'text' => get_string('on_campus:preferences:config:confirmation_enabled:text', 'local_signout')
                ),
                'refresh' => array('element' => 'text', 'type' => PARAM_INT),
                'confirmation_undo' => array('element' => 'text', 'type' => PARAM_INT)
            ),
            'text' => array(
                'ip_form_error' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'ip_sign_in_error_boarder' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'ip_sign_in_error_day' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'confirmation' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'underclassman_warning' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'junior_warning' => self::ELEMENT_FORMATTED_TEXT_REQUIRED
            )
        );
        $this->set_fields($fields, 'on_campus:preferences', true, 'local_signout');
    }

}
