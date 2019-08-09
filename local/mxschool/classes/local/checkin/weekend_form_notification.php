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
 * Base class for all email notification regarding weekend forms for Middlesex's Dorm and Student Functions Plugin.
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

abstract class weekend_form_notification extends \local_mxschool\notification {

    /**
     * @param string $emailclass The class of the email as specified in the local_mxschool_notification database table.
     * @param int $id The id of the weekend form which has been submitted.
     *            The default value of 0 indicates a template email that should not be sent.
     * @throws coding_exception If the specified record does not exist.
     */
    public function __construct($emailclass, $id) {
        global $DB;
        parent::__construct($emailclass);

        if ($id) {
            $record = $DB->get_record_sql(
                "SELECT s.userid AS student, wf.departure_date_time AS departuretime, wf.return_date_time AS returntime,
                        wf.destination, wf.transportation, wf.phone_number AS phone, wf.time_modified AS timesubmitted,
                        d.hohid AS hoh, d.permissions_line AS permissionsline
                 FROM {local_mxschool_weekend_form} wf LEFT JOIN {local_mxschool_student} s ON wf.userid = s.userid
                                                       LEFT JOIN {user} u ON s.userid = u.id
                                                       LEFT JOIN {local_mxschool_dorm} d ON s.dormid = d.id
                 WHERE wf.id = ?", array($id)
            );
            if (!$record) {
                throw new \coding_exception("Record with id {$id} not found.");
            }
            $formatter = new \NumberFormatter('en_us', \NumberFormatter::ORDINAL);
            $instructions = get_config('local_mxschool', 'weekend_form_instructions_bottom');
            $replacements = (object) array(
                'hoh' => format_faculty_name($record->hoh),
                'permissionsline' => $record->permissionsline
            );

            $this->data['departuretime'] = format_date('n/j/y g:i A', $record->departuretime);
            $this->data['returntime'] = format_date('n/j/y g:i A', $record->returntime);
            $this->data['destination'] = $record->destination;
            $this->data['transportation'] = $record->transportation;
            $this->data['phone'] = $record->phone;
            $this->data['timesubmitted'] = format_date('n/j/y g:i A', $record->timesubmitted);
            $this->data['weekendnumber'] = calculate_weekends_used($record->student, get_current_semester());
            $this->data['weekendordinal'] = $formatter->format($this->data['weekendnumber']);
            $this->data['weekendtotal'] = calculate_weekends_allowed($record->student, get_current_semester());
            $this->data['instructions'] = self::replace_placeholders($instructions, $replacements);

            array_push(
                $this->recipients, $DB->get_record('user', array('id' => $record->student)),
                $DB->get_record('user', array('id' => $record->hoh))
            );
        }
    }

    /**
     * @return array The list of strings which can serve as tags for the notification.
     */
    public function get_tags() {
        return array_merge(parent::get_tags(), array(
            'departuretime', 'returntime', 'destination', 'transportation', 'phone', 'timesubmitted', 'weekendnumber',
            'weekendordinal', 'weekendtotal', 'instructions'
        ));
    }

}
