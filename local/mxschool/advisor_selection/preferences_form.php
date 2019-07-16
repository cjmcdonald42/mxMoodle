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
 * Form for editing advisor selection preferences for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage advisor_selection
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_form.php');
require_once(__DIR__.'/../classes/notification/advisor_selection.php');

class preferences_form extends local_mxschool_form {

    /**
     * Form definition.
     */
    protected function definition() {
        $fields = array(
            'availability' => array(
                'start' => array('element' => 'group', 'children' => array(
                    'time' => self::time_selector(1),
                    'date' => array('element' => 'date_selector', 'parameters' => self::date_parameters_school_year())
                )),
                'stop' => array('element' => 'group', 'children' => array(
                    'time' => self::time_selector(1),
                    'date' => array('element' => 'date_selector', 'parameters' => self::date_parameters_school_year())
                )),
                'who' => array('element' => 'radio', 'options' => array('new', 'all'), 'rules' => array('required'))
            ),
            'notifications' => array(
                'submitted_tags' => self::email_tags(new \local_mxschool\local\advisor_selection\submitted()),
                'submitted_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
                'submitted_body' => self::ELEMENT_FORMATED_TEXT_REQUIRED,
                'unsubmitted_tags' => self::email_tags(new \local_mxschool\local\advisor_selection\unsubmitted_notification()),
                'unsubmitted_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
                'unsubmitted_body' => self::ELEMENT_FORMATED_TEXT_REQUIRED,
                'results_tags' => self::email_tags(new \local_mxschool\local\advisor_selection\results_notification()),
                'results_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
                'results_body' => self::ELEMENT_FORMATED_TEXT_REQUIRED
            ),
            'text' => array(
                'closing_warning' => self::ELEMENT_FORMATED_TEXT_REQUIRED,
                'instructions' => self::ELEMENT_FORMATED_TEXT_REQUIRED
            )
        );
        $this->set_fields($fields, 'advisor_selection_preferences', true);
    }

}
