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
 * Weekday check-in sheet for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  checkin
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:view_checkin', context_system::instance());

$filter = new stdClass();
$filter->dorm = get_param_faculty_dorm(false);

setup_mxschool_page('weekday_report', 'checkin');

$table = new local_mxschool\local\checkin\weekday_table($filter);
$dropdowns = array(\local_mxschool\dropdown::dorm_dropdown($filter->dorm, false));
$headers = array(array('text' => '', 'length' => $filter->dorm ? 3 : 4));
$day = generate_datetime('Sunday this week');
for ($i = 1; $i <= 5; $i++) {
    $day->modify("+1 day");
    $headers[] = array('text' => $day->format('l'), 'length' => 2);
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\report($table, null, $dropdowns, array(), true, $headers);

echo $output->header();
echo $output->heading(
    get_string('checkin_weekday_report_title', 'local_mxschool', $filter->dorm ? format_dorm_name($filter->dorm) . ' ' : '')
);
echo $output->render($renderable);
echo $output->footer();
