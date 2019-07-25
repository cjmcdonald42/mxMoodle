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
 * Email notification to the peer tutor admin with a summary of the current day's tutoring for Middlesex's Peer Tutoring Subplugin.
 *
 * @package     local_peertutoring
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_peertutoring\local;

defined('MOODLE_INTERNAL') || die();

use local_peertutoring\local\table;
use local_mxschool\output\report_table;

class daily_summary extends \local_peertutoring\notification {

    public function __construct() {
        global $PAGE, $DB;
        parent::__construct('peer_tutor_summary');

        $filter = new \stdClass();
        $filter->tutor = 0;
        $filter->department = 0;
        $filter->type = 0;
        $filter->date = generate_datetime('-1 day')->getTimestamp();
        $filter->search = '';
        $table = new table($filter, '', true);

        $output = $PAGE->get_renderer('local_mxschool');
        $renderable = new report_table($table);

        $this->data['total'] = $DB->count_records_select('local_peertutoring_session', "tutoring_date >= ?", array($filter->date));
        $this->data['table'] = $output->render($renderable);

        $this->recipients[] = self::get_peertutoradmin_user();
    }

    /**
     * @return array The list of strings which can serve as tags for the notification.
     */
    public function get_tags() {
        return array_merge(parent::get_tags(), array('total', 'table'));
    }

}
