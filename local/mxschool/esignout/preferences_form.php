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
 * Form for editing eSignout preferences for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage esignout
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_form.php');
require_once(__DIR__.'/../classes/notification/esignout.php');

class preferences_form extends local_mxschool_form {

    /**
     * Form definition.
     */
    protected function definition() {
        $fields = array(
            'config' => array(
                'editwindow' => array('element' => 'text', 'type' => PARAM_INT, 'rules' => array('required')),
                'tripwindow' => array('element' => 'text', 'type' => PARAM_INT, 'rules' => array('required')),
                'esignoutenabled' => array(
                    'element' => 'advcheckbox', 'name' => null, 'text' => get_string(
                        'esignout_preferences_config_esignoutenabled_text', 'local_mxschool'
                    )
                ),
                'ipenabled' => array(
                    'element' => 'advcheckbox', 'name' => null, 'text' => get_string(
                        'esignout_preferences_config_ipenabled_text', 'local_mxschool',
                        array('school' => get_config('local_mxschool', 'school_ip'), 'current' => $_SERVER['REMOTE_ADDR'])
                    )
                )
            ), 'notifications' => array(
                'tags' => self::email_tags(new \local_mxschool\local\esignout\submitted()),
                'subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
                'body' => self::ELEMENT_FORMATED_TEXT_REQUIRED
            ), 'text' => array(
                'ipformerror' => self::ELEMENT_FORMATED_TEXT_REQUIRED,
                'ipreporterror' => self::ELEMENT_FORMATED_TEXT_REQUIRED,
                'passengerinstructions' => self::ELEMENT_FORMATED_TEXT_REQUIRED,
                'bottominstructions' => self::ELEMENT_FORMATED_TEXT_REQUIRED,
                'nopassengers' => self::ELEMENT_FORMATED_TEXT_REQUIRED,
                'needparent' => self::ELEMENT_FORMATED_TEXT_REQUIRED,
                'onlyspecific' => self::ELEMENT_FORMATED_TEXT_REQUIRED,
                'confirmation' => self::ELEMENT_FORMATED_TEXT_REQUIRED
            ), 'emailtext' => array(
                'irregular' => self::ELEMENT_FORMATED_TEXT_REQUIRED,
                'driver' => self::ELEMENT_FORMATED_TEXT_REQUIRED,
                'any' => self::ELEMENT_FORMATED_TEXT_REQUIRED,
                'parent' => self::ELEMENT_FORMATED_TEXT_REQUIRED,
                'specific' => self::ELEMENT_FORMATED_TEXT_REQUIRED,
                'over21' => self::ELEMENT_FORMATED_TEXT_REQUIRED
            )
        );
        $this->set_fields($fields, 'esignout_preferences', true);
    }

}
