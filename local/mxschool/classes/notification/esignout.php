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
 * Email notifications for the eSignout subpackage of Middlesex School's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage esignout
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\esignout;

defined('MOODLE_INTERNAL') || die();

require_once('mx_notification.php');

use \local_mxschool\local\notification;

/**
 * Email notification for when an eSignout form is submitted for Middlesex School's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage esignout
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submitted extends notification {

    /**
     * @param int $id The id of the esignout form which has been submitted.
     *                The default value of 0 indicates a template email that should not be sent.
     * @throws coding_exception If the specified record does not exist.
     */
    public function __construct($id=0) {
        global $DB;
        parent::__construct('esignout_submitted');

        if ($id) {
            $record = $DB->get_record_sql(
                "SELECT u.id as student, a.id as approver, sd.hohid AS hoh, es.type, es.passengers, du.firstname AS dfirstname,
                        du.lastname AS dlastname, du.alternatename AS dalternatename, d.destination,
                        d.departure_time AS departuretime, CONCAT(a.firstname, ' ', a.lastname) AS approvername,
                        es.time_modified AS timesubmitted, p.may_ride_with AS passengerpermission,
                        p.ride_permission_details AS specificdrivers
                 FROM {local_mxschool_esignout} es LEFT JOIN {user} u ON es.userid = u.id
                 LEFT JOIN {local_mxschool_esignout} d ON es.driverid = d.id LEFT JOIN {user} du ON d.userid = du.id
                 LEFT JOIN {user} a ON es.approverid = a.id LEFT JOIN {local_mxschool_student} s ON u.id = s.userid
                 LEFT JOIN {local_mxschool_dorm} sd ON s.dormid = sd.id
                 LEFT JOIN {local_mxschool_permissions} p ON es.userid = p.userid WHERE es.id = ?", array($id)
            );
            if (!$record) {
                throw new coding_exception("Record with id {$id} not found.");
            }
            if (isset($record->passengers)) {
                $passengerlist = json_decode($record->passengers);
                $passengers = count($passengerlist) ? implode('<br>', array_map(function($passenger) use($DB) {
                    $passengerrecord = $DB->get_record_sql(
                        "SELECT firstname, lastname, alternatename FROM {user} WHERE id = ?", array($passenger)
                    );
                    return "{$passengerrecord->lastname}, {$passengerrecord->firstname}" . (
                        !empty($passengerrecord->alternatename) && $passengerrecord->alternatename !== $passengerrecord->firstname
                            ? " ({$passengerrecord->alternatename})" : ''
                    );
                }, $passengerlist)) : $passengers = get_string('esignout_report_nopassengers', 'local_mxschool');
            }
            $emaildeans = false;
            if ($record->type === 'Driver') {
                $permissionswarning = get_config('local_mxschool', 'esignout_notification_warning_driver');
            } else {
                if ($record->type !== 'Passenger' && $record->type !== 'Parent') {
                    $emaildeans = true;
                }
                switch ($record->passengerpermission) {
                    case 'Any Driver':
                        $permissionswarning = get_config('local_mxschool', 'esignout_notification_warning_any');
                        break;
                    case 'Parent Permission':
                        $permissionswarning = get_config('local_mxschool', 'esignout_notification_warning_parent');
                        break;
                    case 'Specific Drivers':
                        $permissionswarning = get_config('local_mxschool', 'esignout_notification_warning_specific')
                            . " {$record->specificdrivers}";
                        $emaildeans = true;
                        break;
                    default:
                        $permissionswarning = get_config('local_mxschool', 'esignout_notification_warning_over21');
                        $emaildeans = true;
                }
            }

            $this->data['type'] = $record->type;
            $this->data['driver'] = "{$record->dlastname}, {$record->dfirstname}" . (
                !empty($record->dalternatename) && $record->dalternatename !== $record->dfirstname
                    ? " ({$record->dalternatename})" : ''
            );
            $this->data['passengers'] = $passengers ?? '';
            $this->data['destination'] = $record->destination;
            $this->data['date'] = date('n/j/y', $record->departuretime);
            $this->data['departuretime'] = date('g:i A', $record->departuretime);
            $this->data['approver'] = $record->approvername;
            $this->data['permissionswarning'] = $permissionswarning;
            $this->data['timesubmitted'] = date('g:i A', $record->timesubmitted);
            $this->data['irregular'] = $emaildeans ? get_config('local_mxschool', 'esignout_notification_warning_irregular') : '';

            array_push(
                $this->recipients, $DB->get_record('user', array('id' => $record->student)),
                $DB->get_record('user', array('id' => $record->approver)), $DB->get_record('user', array('id' => $record->hoh))
            );
            if ($emaildeans) {
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
