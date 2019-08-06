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
 * On-campus duty report for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @subpackage  on_campus
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/signout:confirm_on_campus', context_system::instance());

$filter = new stdClass();
$filter->active = optional_param('active', 1, PARAM_INT);
$filter->pictures = optional_param('pictures', 1, PARAM_INT);
$filter->location = optional_param('location', '', PARAM_RAW);
$filter->search = optional_param('search', '', PARAM_RAW);

setup_mxschool_page('duty_report', 'on_campus', 'signout');
$refresh = get_config('local_signout', 'on_campus_refresh_rate');
if ($refresh) {
    $PAGE->set_url(new moodle_url($PAGE->url, (array) $filter));
    $PAGE->set_periodic_refresh_delay((int) $refresh);
}

$locations = get_on_campus_location_list() + array(-1 => get_string('duty_report_select_location_other', 'local_signout'));
if ($filter->location && !isset($locations[$filter->location])) {
    unset($filter->location);
    redirect(new moodle_url($PAGE->url, (array) $filter));
}

$activeoptions = array(
    1 => get_string('duty_report_select_active_true', 'local_signout'),
    0 => get_string('duty_report_select_active_false', 'local_signout')
);
$pictureoptions = array(
    1 => get_string('duty_report_select_pictures_on', 'local_signout'),
    0 => get_string('duty_report_select_pictures_off', 'local_signout')
);

$table = new local_signout\local\on_campus\duty_table($filter);
$dropdowns = array(
    new local_mxschool\output\dropdown('active', $activeoptions, $filter->active),
    new local_mxschool\output\dropdown('pictures', $pictureoptions, $filter->pictures),
    new local_mxschool\output\dropdown(
        'location', $locations, $filter->location, get_string('on_campus_report_select_location_all', 'local_signout')
    )
);

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\report($table, $filter->search, $dropdowns);

echo $output->header();
echo $output->heading(
    $filter->location ? get_string('duty_report_title', 'local_signout', $locations[$filter->location]) : $PAGE->title
);
echo $output->render($renderable);
echo $output->footer();
