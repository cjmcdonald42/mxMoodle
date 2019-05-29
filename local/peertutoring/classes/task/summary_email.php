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
 * Daily task that emails the peer tutor admin with a summary of that day's tutoring for Middlesex School's Peer Tutoring Subplugin.
 *
 * @package    local_peertutoring
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_peertutoring\task;

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__.'/../../../mxschool/classes/mx_notification.php');

use \core\task\scheduled_task;
use \DateTime;
use \core_date;
use \mx_notifications;

class summary_email extends scheduled_task {

    /**
     * @return string The name of the task to be displayed in admin screens.
     */
    public function get_name() {
        return get_string('task_summary_email', 'local_peertutoring');
    }

    /**
     * Emails the peer tutor admin a summary of the tutoring which took place in the last 24 hours.
     */
    public function execute() {
        global $DB;
        $time = new DateTime('now', core_date::get_server_timezone_object());
        $time->modify('-1 day');
        if ($DB->record_exists_sql(
            "SELECT id FROM {local_peertutoring_session} WHERE deleted = 0 AND time_modified >= ?", array($time->getTimestamp())
        )) {
            mx_notifications::send_email('peer_tutor_summary');
        }
    }

}
