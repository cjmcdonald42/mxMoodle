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
 * Email notification for when an off-campus signout form is submitted for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @subpackage  off_campus
 * @author      mxMoodle Development Team
 * @copyright   2021 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_signout\local\off_campus;

defined('MOODLE_INTERNAL') || die();

class submitted extends \local_mxschool\notification {

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
                "SELECT u.id as student, oc.approverid AS approver, d.hohid AS hoh, oc.typeid,
                        t.required_permissions AS permissions, t.name AS type, t.email_warning AS warning, oc.other, oc.passengers,
                        dr.userid as driver, oc.destination, oc.departure_time AS departuretime, oc.time_modified AS timesubmitted,
                        p.may_drive_passengers AS driverpermission, p.may_drive_with_anyone AS passengerwithanyone,
                        p.may_drive_with_over_21 AS passengerwithadult, p.may_use_rideshare AS ridesharepermission
                 FROM {local_signout_off_campus} oc LEFT JOIN {user} u ON oc.userid = u.id
                                                    LEFT JOIN {local_mxschool_student} s ON u.id = s.userid
                                                    LEFT JOIN {local_mxschool_dorm} d ON s.dormid = d.id
                                                    LEFT JOIN {local_signout_type} t on oc.typeid = t.id
                                                    LEFT JOIN {local_signout_off_campus} dr ON oc.driverid = dr.id
                                                    LEFT JOIN {local_mxschool_permissions} p ON oc.userid = p.userid
                 WHERE oc.id = ?", array($id)
            );
            if (!$record) {
                throw new \coding_exception("Record with id {$id} not found.");
            }
            if (isset($record->passengers)) {
                $passengerlist = array_filter(array_map(function($passenger) {
                    return format_student_name($passenger);
                }, json_decode($record->passengers)));
                $passengers = count($passengerlist) ? implode('<br>', $passengerlist)
                    : get_string('off_campus:report:cell:passengers:none', 'local_signout');
            }
            $permissionswarning = get_string('off_campus:notification:warning:default', 'local_signout');
            $irregular = false;
            if (isset($record->permissions)) {
                switch ($record->permissions) {
                    case 'driver':
                        if (empty($record->driverpermission) || $record->driverpermission === 'No') {
                            $permissionswarning = get_config(
                                'local_signout', 'off_campus_notification_warning_driver_nopassengers'
                            );
                        }
                        break;

                // TODO I've changed permissions to these four possible scenarios in line with the new
                // Magnus Permissions
                    case 'passenger':
                        if (empty($record->passengerwithadult) || $record->passengerwithadult === 'No') {
                            $permissionswarning = get_config('local_signout', 'off_campus_notification_warning_passenger_over21');
                            $irregular = true; // Should never happen.
                        } else if ($record->passengerwithadult === 'Parent') {
                            $permissionswarning = get_config('local_signout', 'off_campus_notification_warning_passenger_parent');
                            $irregular = true;
                        } else if (empty($record->passengerwithanyone) || $record->passengerwithanyone === 'No') {
                            $permissionswarning = get_config('local_signout', 'off_campus_notification_warning_passenger_parent');
                            $irregular = true;
                        } else if ($record->passengerwithanyone === 'Parent') {
                            $permissionswarning = get_config('local_signout', 'off_campus_notification_warning_passenger_parent');
                            $irregular = true;
                        }
                        break;

                    case 'rideshare':
                        if (empty($record->ridesharepermission) || $record->ridesharepermission === 'No') {
                            $permissionswarning = get_config(
                                'local_signout', 'off_campus_notification_warning_rideshare_notallowed'
                            );
                            $irregular = true; // Should never happen.
                        } else if ($record->ridesharepermission === 'Parent') {
                            $permissionswarning = get_config('local_signout', 'off_campus_notification_warning_rideshare_parent');
                        }
                        break;
                }
            } else if (isset($record->warning)) {
                $permissionswarning = $record->warning;
            } else if ($record->typeid == -1) { // For 'other' types include both the passenger and the rideshare warnings.
                $passengerwarning = get_string('off_campus:notification:warning:default', 'local_signout');
                if (empty($record->passengerwithadult) || $record->passengerwithadult === 'Yes') {
                    $passengerwarning = get_config('local_signout', 'off_campus_notification_warning_passenger_over21');
                } else if ($record->passengerwithanyone === 'No') {
                    // TODO this case is no longer used - basically made No and Parent flag the permission warning
                    $passengerwarning = get_config('local_signout', 'off_campus_notification_warning_passenger_specific');
                } else if ($record->passengerwithadult === 'Parent') {
                    $passengerwarning = get_config('local_signout', 'off_campus_notification_warning_passenger_parent');
                }
                $ridesharewarning = get_string('off_campus:notification:warning:default', 'local_signout');
                if (empty($record->ridesharepermission) || $record->ridesharepermission === 'No') {
                    $ridesharewarning = get_config(
                        'local_signout', 'off_campus_notification_warning_rideshare_notallowed'
                    );
                } else if ($record->ridesharepermission === 'Parent') {
                    $ridesharewarning = get_config('local_signout', 'off_campus_notification_warning_rideshare_parent');
                }
                $permissionswarning = get_string('off_campus:notification:warning:other', 'local_signout', array(
                    'passengerwarning' => $passengerwarning, 'ridesharewarning' => $ridesharewarning
                ));
                $irregular = true;
            }

            $this->data['type'] = $record->type ?? $record->other;
            $this->data['driver'] = isset($record->driver) ? format_student_name($record->driver) : '-';
            $this->data['passengers'] = $passengers ?? '-';
            $this->data['destination'] = $record->destination;
            $this->data['date'] = format_date('n/j/y', $record->departuretime);
            $this->data['departuretime'] = format_date('g:i A', $record->departuretime);
            $this->data['approver'] = isset($record->approver) ? format_faculty_name($record->approver, false) : '-';
            $this->data['permissionswarning'] = $permissionswarning;
            $this->data['timesubmitted'] = format_date('g:i A', $record->timesubmitted);
            $this->data['irregular'] = $irregular ? get_config('local_signout', 'off_campus_notification_warning_irregular') : '';

            array_push(
                $this->recipients, $DB->get_record('user', array('id' => $record->student)),
                $DB->get_record('user', array('id' => $record->hoh))
            );
            if (isset($record->approver)) {
                $this->recipients[] = $DB->get_record('user', array('id' => $record->approver));
            }
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
