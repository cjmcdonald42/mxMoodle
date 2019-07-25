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
 * Email notification for when an advisor selection form is submitted for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  advisor_selection
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\advisor_selection;

defined('MOODLE_INTERNAL') || die();

class submitted extends \local_mxschool\notification {

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
