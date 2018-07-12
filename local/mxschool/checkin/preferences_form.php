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
 * Form for editing checkin preferences for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
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
        $weekends = $this->_customdata['weekends'];

        $weekendfields = array();
        foreach ($weekends as $weekend) {
            $sundaytime = new DateTime('now', core_date::get_server_timezone_object());
            $sundaytime->setTimestamp($weekend->sunday_time);
            $startoptions = array();
            for ($i = 4; $i >= 1; $i--) {
                $starttime = clone $sundaytime;
                $starttime->modify("-$i days");
                $startoptions[$starttime->getTimestamp()] = $starttime->format('l');
            }
            $endoptions = array();
            for ($i = 1; $i <= 3; $i++) {
                $endtime = clone $sundaytime;
                $endtime->modify("+$i days -1 second");
                $endoptions[$endtime->getTimestamp()] = $endtime->format('l');
            }
            $labeltime = clone $sundaytime;
            $labeltime->modify("-1 day");
            $weekendfields["weekend_$weekend->id"] = array(
                'element' => 'group', 'name' => 'label', 'nameparam' => $labeltime->format('m/d/y'), 'children' => array(
                    'type' => array('element' => 'radio', 'name' => 'type', 'options' => array(
                        'Open', 'Closed', 'Free', 'Vacation'
                    )),
                    'starttime' => array('element' => 'select', 'options' => $startoptions),
                    'endtime' => array('element' => 'select', 'options' => $endoptions)
                )
            );
        }

        $dateparameters = array(
            'startyear' => (new DateTime('-1 year', core_date::get_server_timezone_object()))->format('Y'),
            'stopyear' => (new DateTime('+1 year', core_date::get_server_timezone_object()))->format('Y'),
            'timezone' => core_date::get_server_timezone_object()
        );
        $emailtags = array(
            'studentname', 'departuretime', 'returntime', 'destination', 'transportation', 'phone', 'timesubmitted', 'weekendnum',
            'weekendtotal', 'instructions', 'hohname', 'permissionsline'
        );

        $fields = array(
            'dates' => array(
                'dormsopen' => array('element' => 'date_selector', 'parameters' => $dateparameters),
                'secondsemester' => array('element' => 'date_selector', 'parameters' => $dateparameters),
                'dormsclose' => array('element' => 'date_selector', 'parameters' => $dateparameters)
            ), 'weekends' => $weekendfields,
            'displaytext' => array(
                'topinstructions' => parent::ELEMENT_FORMATED_TEXT_REQUIRED,
                'bottominstructions' => parent::ELEMENT_FORMATED_TEXT_REQUIRED
            ), 'notifications' => array(
                'available' => array('element' => 'static', 'text' => implode(', ', array_map(function($tag) {
                    return "{{$tag}}";
                }, $emailtags))),
                'submittedsubject' => parent::ELEMENT_LONG_TEXT_REQUIRED,
                'submittedbody' => parent::ELEMENT_FORMATED_TEXT_REQUIRED,
                'approvedsubject' => parent::ELEMENT_LONG_TEXT_REQUIRED,
                'approvedbody' => parent::ELEMENT_FORMATED_TEXT_REQUIRED
            )
        );
        parent::set_fields($fields, 'checkin_preferences', true);
    }

}
