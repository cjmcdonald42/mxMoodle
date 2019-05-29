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
 * Email notifications for the checkin subpackage of Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage checkin
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\checkin;

defined('MOODLE_INTERNAL') || die();

require_once('mx_notification.php');

use local_mxschool\local\notification;

/**
 * Base class for email notification regarding weekend forms for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage checkin
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class weekend_form_base extends notification {

    /**
     * @param string $emailclass The class of the email as specified in the local_mxschool_notification database table.
     * @param int $id The id of the weekend form which has been submitted.
     * @throws coding_exception If the specified record does not exist.
     */
    public function __construct($emailclass, $id) {
        global $DB;
        parent::__construct($emailclass);
        $record = $DB->get_record_sql(
            "SELECT s.userid AS student, u.firstname, u.lastname, u.alternatename, wf.departure_date_time AS departuretime,
                    wf.return_date_time AS returntime, wf.destination, wf.transportation, wf.phone_number AS phone,
                    wf.time_modified AS timesubmitted, d.hohid AS hoh, CONCAT(hoh.firstname, ' ', hoh.lastname) AS hohname,
                    d.permissions_line AS permissionsline
             FROM {local_mxschool_weekend_form} wf LEFT JOIN {local_mxschool_student} s ON wf.userid = s.userid
             LEFT JOIN {user} u ON s.userid = u.id LEFT JOIN {local_mxschool_dorm} d ON s.dormid = d.id
             LEFT JOIN {user} hoh ON d.hohid = hoh.id WHERE wf.id = ?", array($id)
        );
        if (!$record) {
            throw new coding_exception("Record with id {$id} not found.");
        }
        $formatter = new \NumberFormatter('en_us', \NumberFormatter::ORDINAL);
        $instructions = get_config('local_mxschool', 'weekend_form_instructions_bottom');
        $replacements = new \stdClass();
        $replacements->hoh = $record->hohname;
        $replacements->permissionsline = $record->permissionsline;

        $this->data['studentname'] = "{$record->lastname}, {$record->firstname}".(
            !empty($record->alternatename) && $record->alternatename !== $record->firstname ? " ({$record->alternatename})" : ''
        );
        $this->data['departuretime'] = date('n/j/y g:i A', $record->departuretime);
        $this->data['returntime'] = date('n/j/y g:i A', $record->returntime);
        $this->data['destination'] = $record->destination;
        $this->data['transportation'] = $record->transportation;
        $this->data['phone'] = $record->phone;
        $this->data['timesubmitted'] = date('n/j/y g:i A', $record->timesubmitted);
        $this->data['weekendnumber'] = calculate_weekends_used($record->student, get_current_semester());
        $this->data['weekendordinal'] = $formatter->format($this->data['weekendnumber']);
        $this->data['weekendtotal'] = calculate_weekends_allowed($record->student, get_current_semester());
        $this->data['instructions'] = self::replace_placeholders($instructions, $replacements);
        $this->data['hohname'] = $record->hohname;
        $this->data['permissionsline'] = $record->permissionsline;

        $this->recipients = array(
            $DB->get_record('user', array('id' => $record->student)), $DB->get_record('user', array('id' => $record->hoh))
        );
    }

    /**
     * @return array The list of strings which can serve as tags for the notification.
     */
    public function get_tags() {
        return array_merge(array(
            'studentname', 'departuretime', 'returntime', 'destination', 'transportation', 'phone', 'timesubmitted',
            'weekendnumber', 'weekendordinal', 'weekendtotal', 'instructions', 'hohname', 'permissionsline'
        ), parent::get_tags());
    }

}

/**
 * Email notification for when a weekend form is submitted for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage checkin
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class weekend_form_submitted extends weekend_form_base {

    /**
     * @param int $id The id of the weekend form which has been submitted.
     */
    public function __construct($id) {
        parent::__construct('weekend_form_submitted', $id);
    }

}

/**
 * Email notification for when a weekend form is approved for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage checkin
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class weekend_form_approved extends weekend_form_base {

    /**
     * @param int $id The id of the weekend form which has been submitted.
     */
    public function __construct($id) {
        parent::__construct('weekend_form_approved', $id);
    }

}
