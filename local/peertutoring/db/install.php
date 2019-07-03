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
 * Database installation steps for Middlesex School's Peer Tutoring Subplugin.
 *
 * @package    local_peertutoring
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_peertutoring_install() {
    global $DB;

    $package = array('package' => 'peertutoring', 'pages' => json_encode(array(
        'preferences' => 'preferences.php', 'tutoring_form' => 'tutoring_enter.php', 'tutoring_report' => 'tutoring_report.php'
    )));
    $DB->insert_record('local_mxschool_subpackage', (object) $package);

    $types = array(
        array('displaytext' => 'Homework help'),
        array('displaytext' => 'Study strategies'),
        array('displaytext' => 'Understanding a concept'),
        array('displaytext' => 'Help with a project'),
        array('displaytext' => 'Other (please speficy)')
    );
    foreach ($types as $type) {
        $DB->insert_record('local_peertutoring_type', (object) $type);
    }

    $ratings = array(
        array('displaytext' => '1 - extremely helpful; issue completely resolved'),
        array('displaytext' => '2 - very helpful; student is better-off'),
        array('displaytext' => '3 - somewhat helpful; some benefit to student'),
        array('displaytext' => '4 - slightly helpful; student still unclear'),
        array('displaytext' => '5 - not effective; student received no benefit')
    );
    foreach ($ratings as $rating) {
        $DB->insert_record('local_peertutoring_rating', (object) $rating);
    }
}
