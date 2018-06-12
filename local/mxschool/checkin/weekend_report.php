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
 * Weekend checkin sheet for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once('weekend_table.php');
require_once(__DIR__.'/../classes/mx_dropdown.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/events/page_visited.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:view_checkin', context_system::instance());

$filter = new stdClass();
$filter->dorm = get_param_faculty_dorm();
$filter->weekend = get_param_current_weekend();
$filter->submitted = optional_param('submitted', '', PARAM_RAW);
$filter->search = optional_param('search', '', PARAM_RAW);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('checkin', 'local_mxschool') => '/local/mxschool/checkin/index.php'
);
$url = '/local/mxschool/checkin/weekend_report.php';
$title = get_string('weekend_report', 'local_mxschool');
$dorms = get_dorms_list();
$weekends = get_weekend_list();
$submittedoptions = array(
    '1' => get_string('weekend_report_select_submitted_true', 'local_mxschool'),
    '0' => get_string('weekend_report_select_submitted_false', 'local_mxschool')
);

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

$table = new weekend_table('weekend_table', $filter);

$dormselect = new local_mxschool_dropdown('dorm', $dorms, $filter->dorm, get_string('report_select_dorm', 'local_mxschool'));
$weekendselect = new local_mxschool_dropdown('weekend', $weekends, $filter->weekend);
$submittedselect = new local_mxschool_dropdown(
    'submitted', $submittedoptions, $filter->submitted, get_string('weekend_report_select_submitted_all', 'local_mxschool')
);

$addbutton = array(
    'text' => get_string('weekend_report_add', 'local_mxschool'),
    'url' => new moodle_url('/local/mxschool/checkin/weekend_enter.php')
);

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\report_page(
    'weekend_report', $table, 50, $filter->search, array($dormselect, $weekendselect, $submittedselect), true, $addbutton
);

echo $output->header();
echo $output->heading(($filter->dorm ? $dorms[$filter->dorm] : '')." $title");
echo $output->render($renderable);
echo $output->footer();
