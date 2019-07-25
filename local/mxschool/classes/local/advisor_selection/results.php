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
 * Email notification to notify students and advisors of the new pairings
 * for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  advisor_selection
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\advisor_selection;

defined('MOODLE_INTERNAL') || die();

class results extends \local_mxschool\notification {

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
