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
 * Generic checkin sheet for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage checkin
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/mx_dropdown.php');
require_once('generic_table.php');

require_login();
require_capability('local/mxschool:view_checkin', context_system::instance());

$dorm = get_param_faculty_dorm();

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('checkin', 'local_mxschool') => '/local/mxschool/checkin/index.php'
);
$url = '/local/mxschool/checkin/generic_report.php';
$title = get_string('generic_report', 'local_mxschool');

setup_mxschool_page($url, $title, $parents);

$dorms = get_dorm_list();

$table = new generic_table($dorm);

$dropdowns = array(new local_mxschool_dropdown('dorm', $dorms, $dorm, get_string('report_select_dorm', 'local_mxschool')));

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\report($table, null, $dropdowns, true);

echo $output->header();
echo $output->heading(get_string('generic_report_title', 'local_mxschool', $dorm ? "{$dorms[$dorm]} " : ''));
echo $output->render($renderable);
echo $output->footer();
