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
        switch($emailclass) {
            case 'weekend_form_submitted':
                if (!isset($params['id'])) {
                    return false;
                }
                $dormrecord = $DB->get_record_sql(
                    "SELECT s.userid AS student, d.hohid AS hoh, d.permissions_line AS permissionsline
                     FROM {local_mxschool_student} s LEFT JOIN {local_mxschool_dorm} d ON s.dormid = d.id WHERE s.userid = ?",
                     array($params['id'])
                );
                debugging("submitted: $dormrecord->student $dormrecord->hoh $dormrecord->permissionsline");
                return false;
            case 'weekend_form_approved':
                if (!isset($params['id'])) {
                    return false;
                }
                $dormrecord = $DB->get_record_sql(
                    "SELECT s.userid AS student, d.hohid AS hoh, d.permissions_line AS permissionsline
                     FROM {local_mxschool_weekend_form} wf LEFT JOIN {local_mxschool_student} s ON wf.userid = s.userid
                     LEFT JOIN {local_mxschool_dorm} d ON s.dormid = d.id WHERE wf.id = ?", array($params['id'])
                );
                debugging("approved: $dormrecord->student $dormrecord->hoh $dormrecord->permissionsline");
                return false;
            default:
                return false;
        }
    }

}
