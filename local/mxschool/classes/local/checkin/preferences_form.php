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
 * Form for editing checkin preferences for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  checkin
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\checkin;

defined('MOODLE_INTERNAL') || die();

use local_mxschool\local\checkin\weekend_form_submitted;
use local_mxschool\local\checkin\weekend_form_approved;

class preferences_form extends \local_mxschool\form {

    /**
     * Form definition.
     */
    protected function definition() {
        $weekends = $this->_customdata['weekends'];

        $weekendfields = array();
        foreach ($weekends as $weekend) {
            $startoptions = get_weekend_start_day_list();
            $endoptions = get_weekend_end_day_list();
            $date = generate_datetime($weekend->sunday_time);
            $date->modify("-1 day");
            $weekendfields["weekend_$weekend->id"] = array(
                'element' => 'group', 'name' => 'label', 'nameparam' => $date->format('m/d/y'), 'children' => array(
                    'type' => array('element' => 'radio', 'name' => 'type', 'options' => array(
                        'Open', 'Closed', 'Free', 'Vacation'
                    )),
                    'start' => array('element' => 'select', 'options' => $startoptions),
                    'end' => array('element' => 'select', 'options' => $endoptions)
                )
            );
        }

        $dateoptions = array(
            'startyear' => format_date('Y', '-1 year'),
            'stopyear' => format_date('Y', '+1 year'),
            'timezone' => \core_date::get_user_timezone_object()
        );

        $fields = array(
            'dates' => array(
                'dormsopen' => array('element' => 'date_selector', 'options' => $dateoptions),
                'secondsemester' => array('element' => 'date_selector', 'options' => $dateoptions),
                'dormsclose' => array('element' => 'date_selector', 'options' => $dateoptions)
            ),
            'weekends' => $weekendfields,
            'notifications' => array(
                'submitted_tags' => self::email_tags(new weekend_form_submitted()),
                'submitted_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
                'submitted_body' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'approved_tags' => self::email_tags(new weekend_form_approved()),
                'approved_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
                'approved_body' => self::ELEMENT_FORMATTED_TEXT_REQUIRED
            ),
            'text' => array(
                'topinstructions' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'bottominstructions' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
                'closedwarning' => self::ELEMENT_FORMATTED_TEXT_REQUIRED
            )
        );
        $this->set_fields($fields, 'checkin_preferences', true);
    }

}
