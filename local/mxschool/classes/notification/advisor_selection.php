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
 * Email notifications for the advisor selection subpackage of Middlesex School's Dorm and Student functions plugin.
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

require_once('mx_notification.php');

use local_mxschool\local\notification;

/**
 * Email notification for when an advisor selection form is submitted for Middlesex School's Dorm and Student functions plugin.
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
    public function __construct($id=0) {
        global $DB;
        parent::__construct('advisor_selection_submitted');
        if ($id) {
            $record = $DB->get_record_sql(
                "SELECT a.userid AS student, a.keep_current AS keepcurrent, CONCAT(ca.lastname, ', ', ca.firstname) AS current,
                        CONCAT(o1.lastname, ', ', o1.firstname) AS option1, CONCAT(o2.lastname, ', ', o2.firstname) AS option2,
                        CONCAT(o3.lastname, ', ', o3.firstname) AS option3, CONCAT(o4.lastname, ', ', o4.firstname) AS option4,
                        CONCAT(o5.lastname, ', ', o5.firstname) AS option5, a.time_modified AS timesubmitted
                 FROM {local_mxschool_adv_selection} a LEFT JOIN {local_mxschool_student} s on a.userid = s.userid
                 LEFT JOIN {user} ca ON s.advisorid = ca.id LEFT JOIN {user} o1 ON a.option1id = o1.id
                 LEFT JOIN {user} o2 ON a.option2id = o2.id LEFT JOIN {user} o3 ON a.option3id = o3.id
                 LEFT JOIN {user} o4 ON a.option4id = o4.id LEFT JOIN {user} o5 ON a.option5id = o5.id
                 WHERE a.id = ?", array($id)
            );
            if (!$record) {
                throw new coding_exception("Record with id {$id} not found.");
            }

            $this->data['keepcurrent'] = boolean_to_yes_no($record->keepcurrent);
            $this->data['current'] = $record->current;
            $this->data['option1'] = $record->option1;
            $this->data['option2'] = $record->option2;
            $this->data['option3'] = $record->option3;
            $this->data['option4'] = $record->option4;
            $this->data['option5'] = $record->option5;
            $this->data['timesubmitted'] = date('n/j/y g:i A', $record->timesubmitted);

            $this->recipients[] = $DB->get_record('user', array('id' => $record->student));
        }
    }

    /**
     * @return array The list of strings which can serve as tags for the notification.
     */
    public function get_tags() {
        return array_merge(array(
            'keepcurrent', 'current', 'option1', 'option2', 'option3', 'option4', 'option5', 'timesubmitted'
        ), parent::get_tags());
    }

}

/**
 * Email notification to remind students to complete the advisor selection form
 * for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage advisor_selection
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notify_unsubmitted extends notification {

    public function __construct() {
        global $DB;
        parent::__construct('advisor_selection_notify_unsubmitted');

        $list = get_student_without_advisor_form_list();
        foreach ($list as $userid => $name) {
            $this->recipients[] = $DB->get_record('user', array('id' => $userid));
        }
        $this->recipients[] = self::get_deans_user();
    }

}

/**
 * Email notification to notify students and advisors of the new pairings
 * for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage advisor_selection
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notify_results extends notification {

    public function __construct() {
        global $DB;
        parent::__construct('advisor_selection_notify_results');

        $list = get_new_student_advisor_pair_list();
        foreach ($list as $suserid => $auserid) {
            $student = $DB->get_record('user', array('id' => $suserid));
            $advisor = $DB->get_record('user', array('id' => $auserid));
            $student->replacements = $advisor->replacements = array(
                'studentname' => "{$student->lastname}, {$student->firstname}" . (
                    !empty($student->alternatename) && $student->alternatename !== $student->firstname
                        ? " ({$student->alternatename})" : ''
                ), 'advisorname' => "{$advisor->firstname} {$advisor->lastname}"
            );
            array_push($this->recipients, $student, $advisor);
        }
        $this->recipients[] = self::get_deans_user();
    }

    /**
     * @return array The list of strings which can serve as tags for the notification.
     */
    public function get_tags() {
        return array_merge(array('studentname', 'advisorname'), parent::get_tags());
    }

}
