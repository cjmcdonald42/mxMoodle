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
    // TODO: Implement submission notification.
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
