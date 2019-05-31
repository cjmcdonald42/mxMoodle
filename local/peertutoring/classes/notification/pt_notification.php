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
 * Email notifications for Middlesex School's Peer Tutoring Subplugin.
 *
 * @package    local_peertutoring
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_peertutoring\local;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../../mxschool/classes/notification/mx_notification.php');
require_once(__DIR__.'/../../../mxschool/classes/output/renderable.php');
require_once(__DIR__.'/../../tutoring_table.php');

use local_mxschool\local\notification as mx_notification;
use \local_mxschool\output\report;

/**
 * Generic email notification for Middlesex School's Peer Tutoring Subplugin.
 *
 * @package    local_peertutoring
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class notification extends mx_notification {

    /**
     * Generates a user object to which emails should be sent to reach the deans.
     * @return stdClass The deans user object.
     */
    final protected static function get_peertutoradmin_user() {
        $supportuser = \core_user::get_support_user();
        $peertutoradmin = clone $supportuser;
        $peertutoradmin->email = get_config('local_peertutoring', 'email_peertutoradmin');
        $peertutoradmin->addresseename = get_config('local_peertutoring', 'addressee_peertutoradmin');
        return $peertutoradmin;
    }

}

/**
 * Email notification to the peer tutor admin with a summary of that day's tutoring for Middlesex School's Peer Tutoring Subplugin.
 *
 * @package    local_peertutoring
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class daily_summary extends notification {

    public function __construct() {
        global $DB, $PAGE;
        parent::__construct('peer_tutor_summary');

        $time = new \DateTime('now', \core_date::get_server_timezone_object());
        $time->modify('-1 day');
        $record = $DB->get_record_sql(
            "SELECT COUNT(id) AS total FROM {local_peertutoring_session} WHERE time_modified >= ?",
            array($time->getTimestamp())
        );
        $filter = new \stdClass();
        $filter->date = $time->getTimestamp();
        $table = new \tutoring_table($filter, '', true);
        $output = $PAGE->get_renderer('local_mxschool');
        $renderable = new \local_mxschool\output\report($table);

        $this->data['total'] = $record->total;
        $this->data['table'] = $output->render($renderable);

        $this->recipients[] = self::get_peertutoradmin_user();
    }

    /**
     * @return array The list of strings which can serve as tags for the notification.
     */
    public function get_tags() {
        return array_merge(array('total', 'table'), parent::get_tags());
    }

}
