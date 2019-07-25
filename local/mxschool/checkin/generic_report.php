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
 * Generic check-in sheet for Middlesex's Dorm and Student Functions Plugin.
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
$filter->dorm = get_param_faculty_dorm();

setup_mxschool_page('generic_report', 'checkin');

$table = new local_mxschool\local\checkin\generic_table($filter);
$dropdowns = array(\local_mxschool\output\dropdown::dorm_dropdown($filter->dorm));

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\report($table, null, $dropdowns, array(), true);

echo $output->header();
echo $output->heading(
    get_string('checkin_generic_report_title', 'local_mxschool', $filter->dorm > 0 ? format_dorm_name($filter->dorm) . ' ' : '')
);
echo $output->render($renderable);
echo $output->footer();
