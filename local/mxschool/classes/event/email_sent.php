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
 * Event that is triggered whenever an email notification is sent by Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\event;

defined('MOODLE_INTERNAL') || die();

use core\event\base;

class email_sent extends base {

    /**
     * Initializes an email_sent event.
     */
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->context = \context_system::instance();
    }

    /**
     * Retrieves the localized name of email_sent events.
     *
     * @return string the name of the event.
     */
    public static function get_name() {
        return get_string('event:email_sent', 'local_mxschool');
    }

    /**
     * Retrieves the unlocalized description of the email_sent event.
     *
     * @return string the description of the event.
     */
    public function get_description() {
        return "The user with id '{$this->userid}' sent the email notiification with class '{$this->data['other']['emailclass']}'.";
    }

}
