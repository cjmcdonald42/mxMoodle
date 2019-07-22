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
 * Email notifications for the off_campus subpackage of Middlesex's eSignout Subplugin.
 *
 * @package    local_signout
 * @subpackage off_campus
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_signout\local\off_campus;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../../mxschool/classes/notification/mx_notification.php');

use \local_mxschool\local\notification;

/**
 * Email notification for when an off-campus signout form is submitted for Middlesex's eSignout Subplugin.
 *
 * @package    local_signout
 * @subpackage off_campus
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submitted extends notification {

    /**
     * @param int $id The id of the off-campus signout form which has been submitted.
     *                The default value of 0 indicates a template email that should not be sent.
     * @throws coding_exception If the specified record does not exist.
     */
    public function __construct($id = 0) {
        global $DB;
        parent::__construct('off_campus_submitted');

        if ($id) {
            $record = $DB->get_record_sql(
                "SELECT u.id as student, d.approverid AS approver, sd.hohid AS hoh, oc.type, oc.passengers, d.userid as driver,
                        d.destination, d.departure_time AS departuretime, oc.time_modified AS timesubmitted,
                        p.may_ride_with AS passengerpermission, p.ride_permission_details AS specificdrivers
                 FROM {local_signout_off_campus} oc LEFT JOIN {user} u ON oc.userid = u.id
                                                    LEFT JOIN {local_signout_off_campus} d ON oc.driverid = d.id
                                                    LEFT JOIN {local_mxschool_student} s ON u.id = s.userid
                                                    LEFT JOIN {local_mxschool_dorm} sd ON s.dormid = sd.id
                                                    LEFT JOIN {local_mxschool_permissions} p ON oc.userid = p.userid
                 WHERE oc.id = ?", array($id)
            );
            if (!$record) {
                throw new coding_exception("Record with id {$id} not found.");
            }
            if (isset($record->passengers)) {
                $passengerlist = array_filter(array_map(function($passenger) use($DB) {
                    return format_student_name($passenger);
                }, json_decode($record->passengers)));
                $passengers = count($passengerlist) ? implode('<br>', $passengerlist)
                    : get_string('off_campus_report_nopassengers', 'local_signout');
            }
            $irregular = false;
            if ($record->type === 'Driver') {
                $permissionswarning = get_config('local_signout', 'off_campus_notification_warning_driver');
            } else {
                if (!in_array($record->type, array('Passenger', 'Parent', 'Rideshare'))) { // Records with an 'Other' type.
                    $irregular = true;
                }
                switch($record->passengerpermission) {
                    case 'Any Driver':
                        $permissionswarning = get_config('local_signout', 'off_campus_notification_warning_any');
                        break;
                    case 'Parent Permission':
                        $permissionswarning = get_config('local_signout', 'off_campus_notification_warning_parent');
                        break;
                    case 'Specific Drivers':
                        $permissionswarning = get_config('local_signout', 'off_campus_notification_warning_specific')
                            . " {$record->specificdrivers}";
                        $irregular = true;
                        break;
                    case 'Over 21':
                        $permissionswarning = get_config('local_signout', 'off_campus_notification_warning_over21');
                        $irregular = true;
                        break;
                    default:
                        $permissionswarning = get_config('local_signout', 'off_campus_notification_warning_unsetpermissions');
                        $irregular = true;
                }
            }

            $this->data['type'] = $record->type;
            $this->data['driver'] = format_student_name($record->driver);
            $this->data['passengers'] = $passengers ?? '';
            $this->data['destination'] = $record->destination;
            $this->data['date'] = format_date('n/j/y', $record->departuretime);
            $this->data['departuretime'] = format_date('g:i A', $record->departuretime);
            $this->data['approver'] = format_faculty_name($record->approver, false);
            $this->data['permissionswarning'] = $permissionswarning;
            $this->data['timesubmitted'] = format_date('g:i A', $record->timesubmitted);
            $this->data['irregular'] = $irregular ? get_config('local_signout', 'off_campus_notification_warning_irregular') : '';

            array_push(
                $this->recipients, $DB->get_record('user', array('id' => $record->student)),
                $DB->get_record('user', array('id' => $record->approver)), $DB->get_record('user', array('id' => $record->hoh))
            );
            if ($irregular) {
                $this->recipients[] = self::get_deans_user();
            }
        }
    }

    /**
     * @return array The list of strings which can serve as tags for the notification.
     */
    public function get_tags() {
        return array_merge(parent::get_tags(), array(
            'type', 'driver', 'passengers', 'destination', 'date', 'departuretime', 'approver', 'permissionswarning',
            'timesubmitted', 'irregular'
        ));
    }

}
