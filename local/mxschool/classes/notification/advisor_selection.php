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
 * Email notifications for the advisor selection subpackage of Middlesex School's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage advisor_selection
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\advisor_selection;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/mx_notification.php');

use \local_mxschool\local\notification;
use \local_mxschool\local\bulk_notification;

/**
 * Email notification for when an advisor selection form is submitted for Middlesex School's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage advisor_selection
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submitted extends notification {

    /**
     * @param int $id The id of the advisor selection form which has been submitted.
     *                The default value of 0 indicates a template email that should not be sent.
     * @throws coding_exception If the specified record does not exist.
     */
    public function __construct($id = 0) {
        global $DB;
        parent::__construct('advisor_selection_submitted');
        if ($id) {
            $record = $DB->get_record_sql(
                "SELECT a.userid AS student, a.keep_current AS keepcurrent, s.advisorid AS current, a.option1id AS option1,
                        a.option2id AS option2, a.option3id AS option3, a.option4id AS option4, a.option5id AS option5,
                        a.time_modified AS timesubmitted
                 FROM {local_mxschool_adv_selection} a LEFT JOIN {local_mxschool_student} s ON a.userid = s.userid
                 WHERE a.id = ?", array($id)
            );
            if (!$record) {
                throw new \coding_exception("Record with id {$id} not found.");
            }

            $this->data['keepcurrent'] = format_boolean($record->keepcurrent);
            $this->data['current'] = format_faculty_name($record->current);
            $this->data['option1'] = $record->option1 ? format_faculty_name($record->option1) : '';
            $this->data['option2'] = $record->option2 ? format_faculty_name($record->option2) : '';
            $this->data['option3'] = $record->option3 ? format_faculty_name($record->option3) : '';
            $this->data['option4'] = $record->option4 ? format_faculty_name($record->option4) : '';
            $this->data['option5'] = $record->option5 ? format_faculty_name($record->option5) : '';
            $this->data['timesubmitted'] = format_date('n/j/y g:i A', $record->timesubmitted);

            $this->recipients[] = $DB->get_record('user', array('id' => $record->student));
        }
    }

    /**
     * @return array The list of strings which can serve as tags for the notification.
     */
    public function get_tags() {
        return array_merge(parent::get_tags(), array(
            'keepcurrent', 'current', 'option1', 'option2', 'option3', 'option4', 'option5', 'timesubmitted'
        ));
    }

}

/**
 * Email notification to remind students to complete the advisor selection form
 * for Middlesex School's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage advisor_selection
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class unsubmitted_notification extends notification {

    /**
     * @param int $id The userid of the recipient. A value of 0 indicates that the notification should be sent to the deans.
     */
    public function __construct($id = 0) {
        global $DB;
        parent::__construct('advisor_selection_notify_unsubmitted');

        $this->recipients[] = $id ? $DB->get_record('user', array('id' => $id)) : self::get_deans_user();
    }

}

/**
 * Bulk wrapper for the the unsubmitted_notification for Middlesex School's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage advisor_selection
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notify_unsubmitted extends bulk_notification {

    public function __construct() {
        $list = get_student_without_advisor_form_list();
        foreach ($list as $userid => $name) {
            $this->notifications[] = new unsubmitted_notification($userid);
        }
        $this->notifications[] = new unsubmitted_notification();
    }

}

/**
 * Email notification to notify students and advisors of the new pairings
 * for Middlesex School's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage advisor_selection
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class results_notification extends notification {

    /**
     * @param int $sid The userid of the student. A value of 0 indicates that the notification should be sent to the deans.
     * @param int $aid The userid of the advisor. A value of 0 indicates that the notification should be sent to the deans.
     */
    public function __construct($sid = 0, $aid = 0) {
        global $DB;
        parent::__construct('advisor_selection_notify_results');

        if ($sid && $aid) {
            $this->data['advisorname'] = format_faculty_name($aid, false);
            array_push(
                $this->recipients, $DB->get_record('user', array('id' => $sid)), $DB->get_record('user', array('id' => $aid))
            );
        } else {
            $this->recipients[] = self::get_deans_user();
        }
    }

    /**
     * @return array The list of strings which can serve as tags for the notification.
     */
    public function get_tags() {
        return array_merge(parent::get_tags(), array('advisorname'));
    }

}

/**
 * Bulk wrapper for the the results_notification for Middlesex School's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage advisor_selection
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notify_results extends bulk_notification {

    public function __construct() {
        $list = get_new_student_advisor_pair_list();
        foreach ($list as $suserid => $auserid) {
            $this->notifications[] = new results_notification($suserid, $auserid);
        }
        $this->notifications[] = new results_notification();
    }

}
