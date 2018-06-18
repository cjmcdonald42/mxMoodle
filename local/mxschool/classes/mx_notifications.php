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
 * Email notification system for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../locallib.php');

class mx_notifications {

    /**
     * Sends an email notification based on a specified class.
     *
     * @param string $emailclass The class of the email.
     * @param array $params Parameters for the email.
     * @return bool True if email send successfully, false otherwise.
     */
    public static function send_email($emailclass, $params = array()) {
        global $DB;
        $notification = $DB->get_record('local_mxschool_notification', array('class' => $emailclass));
        $supportuser = core_user::get_support_user();
        if (!$notification) {
            return false;
        }
        switch($emailclass) {
            case 'weekend_form_submitted':
            case 'weekend_form_approved':
                if (!isset($params['id'])) {
                    return false;
                }
                $record = $DB->get_record_sql(
                    "SELECT s.userid AS student, CONCAT(u.firstname, ' ', u.lastname) AS studentname,
                            wf.departure_date_time AS departuretime, wf.return_date_time AS returntime, wf.destination,
                            wf.transportation, wf.phone_number AS phone, wf.time_modified AS timesubmitted, d.hohid AS hoh,
                            CONCAT(hoh.firstname, ' ', hoh.lastname) AS hohname, d.permissions_line AS permissionsline
                     FROM {local_mxschool_weekend_form} wf LEFT JOIN {local_mxschool_student} s ON wf.userid = s.userid
                     LEFT JOIN {user} u ON s.userid = u.id LEFT JOIN {local_mxschool_dorm} d ON s.dormid = d.id
                     LEFT JOIN {user} hoh ON d.hohid = hoh.id WHERE wf.id = ?", array($params['id'])
                );
                $weekendsused = calculate_weekends_used($record->student, get_current_semester());
                $record->weekendnum = (new NumberFormatter('en_us', NumberFormatter::ORDINAL))->format($weekendsused);
                $record->weekendtotal = calculate_weekends_allowed($record->student, get_current_semester());
                $record->instructions = get_string(
                    'weekend_form_bottomdescription', 'local_mxschool', array(
                        'hoh' => $record->hohname, 'permissionsline' => $record->permissionsline
                    )
                );
                $studentemail = $DB->get_record('user', array('id' => $record->student));
                $hohemail = $DB->get_record('user', array('id' => $record->hoh));
                $subject = self::replace($notification->subject, $record);
                $body = self::replace($notification->body_html, $record);
                return email_to_user($studentemail, $supportuser, $subject, '', $body)
                    && email_to_user($hohemail, $supportuser, $subject, '', $body);
            default:
                return false;
        }
    }

    /**
     * Substitutes values for placeholders.
     *
     * @param string $string The string with placeholders.
     * @param stdClass|array $replacements The substitutions to make as [placeholder => value].
     * @return string The original string with the substitutions.
     */
    private static function replace($string, $replacements) {
        $replacements = (array)$replacements;
        foreach ($replacements as $placeholder => $value) {
            $string = str_replace("{{$placeholder}}", $value, $string);
        }
        return $string;
    }

}
