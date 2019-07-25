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
 * Generic wrapper which serves as a superclass for all bulk email notifications sent
 * by Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool;

defined('MOODLE_INTERNAL') || die();

abstract class bulk_notification {

    /** @var array $notifications The array individual local_mxschool\notification objects to be sent. */
    protected $notifications;

    /**
     * Initializes the $notifications field to a default empty value.
     * Subclasses should call this constructor then add the appropriate entries to the $notifications array.
     */
    public function __construct() {
        $this->notifications = array();
    }

    /**
     * Sends all of the notification emails specified in the $notifications field.
     *
     * @return bool A value of true if all emails send successfully, false otherwise.
     * @throws coding_exception If any recipient has a non-valid email or
     *                          if the primary recipient has no adresseename and is missing either the firstname or lastname field.
     */
    final public function send() {
        return array_reduce($this->notifications, function($acc, $notification) {
            return $notification->send(true) && $acc;
        }, true);
    }
}
