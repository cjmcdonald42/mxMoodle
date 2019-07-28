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
                'oncampusenabled' => array(
                    'element' => 'advcheckbox', 'name' => null,
                    'text' => get_string('on_campus_preferences_config_oncampusenabled_text', 'local_signout')
                ),
                'ipenabled' => array(
                    'element' => 'advcheckbox', 'name' => null,
                    'text' => get_string('on_campus_preferences_config_ipenabled_text', 'local_signout', array(
                        'school' => get_config('local_signout', 'school_ip'), 'current' => $_SERVER['REMOTE_ADDR']
                    ))
                ),
                'refresh' => array('element' => 'text', 'type' => PARAM_INT)
            ),
            'text' => array(
                'ipformerror' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'ipsigninerrorboarder' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'ipsigninerrorday' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'underclassmanwarning' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'juniorwarning' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'confirmation' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
            )
        );
        $this->set_fields($fields, 'on_campus_preferences', true, 'local_signout');
    }

}
