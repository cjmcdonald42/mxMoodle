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
 * @subpackage driving
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_form.php');

class preferences_form extends local_mxschool_form {

    /**
     * Form definition.
     */
    protected function definition() {
        $emailtags = implode(', ', array_map(function($tag) {
            return "{{$tag}}";
        }, array(
            'studentname', 'type', 'driver', 'passengers', 'destination', 'date', 'departuretime', 'timesubmitted', 'approver',
            'passengerwarning'
        )));

        $fields = array(
            'config' => array('editwindow' => array('element' => 'text', 'type' => PARAM_INT, 'rules' => array('required'))),
            'notifications' => array(
                'available' => array('element' => 'static', 'text' => $emailtags),
                'subject' => parent::ELEMENT_LONG_TEXT_REQUIRED,
                'body' => parent::ELEMENT_FORMATED_TEXT_REQUIRED
            ), 'text' => array(
                'instructions' => parent::ELEMENT_FORMATED_TEXT_REQUIRED,
                'nopassengers' => parent::ELEMENT_FORMATED_TEXT_REQUIRED,
                'needparent' => parent::ELEMENT_FORMATED_TEXT_REQUIRED,
                'onlyspecific' => parent::ELEMENT_FORMATED_TEXT_REQUIRED,
                'confirmation' => parent::ELEMENT_FORMATED_TEXT_REQUIRED
            ), 'emailtext' => array(
                'driver' => parent::ELEMENT_FORMATED_TEXT_REQUIRED,
                'any' => parent::ELEMENT_FORMATED_TEXT_REQUIRED,
                'parent' => parent::ELEMENT_FORMATED_TEXT_REQUIRED,
                'specific' => parent::ELEMENT_FORMATED_TEXT_REQUIRED,
                'over21' => parent::ELEMENT_FORMATED_TEXT_REQUIRED
            )
        );
        parent::set_fields($fields, 'esignout_preferences', true);
    }

}
