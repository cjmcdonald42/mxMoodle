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
 * Weekday checkin sheet for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once('weekday_table.php');
require_once(__DIR__.'/../classes/mx_dropdown.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/events/page_visited.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:view_checkin', context_system::instance());

$dorm = get_param_faculty_dorm();

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('checkin', 'local_mxschool') => '/local/mxschool/checkin/index.php'
);
$url = '/local/mxschool/checkin/weekday_report.php';
$title = get_string('weekday_report', 'local_mxschool');

$dorms = get_dorms_list();

// $date = new DateTime('now', core_date::get_user_timezone_object());
// $date->modify('-4 days'); // Map 0:00:00 on Friday to 0:00:00 on Monday.
// $date->modify('Sunday this week');

$event = \local_mxschool\event\page_visited::create(array('other' => array('page' => $title)));
$event->trigger();

$PAGE->set_url(new moodle_url($url));
$PAGE->set_context(context_system::instance());
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('incourse');
foreach ($parents as $display => $url) {
    $PAGE->navbar->add($display, new moodle_url($url));
}
$PAGE->navbar->add($title);

$table = new weekday_table('weekday_table', $dorm);

$dropdowns = array(new local_mxschool_dropdown('dorm', $dorms, $dorm, get_string('report_select_dorm', 'local_mxschool')));
$headers = array(array('text' => '', 'length' => 3));
for ($i = 1; $i <= 5; $i++) {
    $headers[] = array('text' => get_string("day_$i", 'local_mxschool'), 'length' => 2);
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\report_page('checkin-weekday-report', $table, 50, null, $dropdowns, true, false, $headers);

echo $output->header();
echo $output->heading(($dorm ? $dorms[$dorm] : '')." $title for the Week of __________");
echo $output->render($renderable);
echo $output->footer();
