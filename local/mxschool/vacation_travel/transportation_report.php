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
 * Vacation travel transportation report for Middlesex School's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage vacation_travel
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/mx_dropdown.php');
require_once(__DIR__.'/transportation_table.php');

require_login();
require_capability('local/mxschool:manage_vacation_travel_transportation', context_system::instance());

$view = optional_param('view', 'departure', PARAM_RAW);
if (!get_config('local_mxschool', 'vacation_form_returnenabled')) {
    $view = 'departure';
}
$filter = new stdClass();
$filter->mxtransportation = optional_param('mxtransportation', '', PARAM_RAW);
$filter->type = optional_param('type', '', PARAM_RAW);
$filter->search = optional_param('search', '', PARAM_RAW);
$download = optional_param('download', '', PARAM_ALPHA);

setup_mxschool_page('transportation_report', 'vacation_travel');

$views = array(
    'departure' => get_string('vacation_travel_transportation_report_select_view_departure', 'local_mxschool'),
    'return' => get_string('vacation_travel_transportation_report_select_view_return', 'local_mxschool')
);
if (!isset($views[$view])) {
    redirect(new moodle_url($PAGE->url, array(
        'view' => 'departure', 'mxtransportation' => $filter->mxtransportation, 'type' => $filter->type, 'search' => $filter->search
    )));
}
$mxtransportationoptions = array(
    '1' => get_string('vacation_travel_transportation_report_select_mxtransportation_true', 'local_mxschool'),
    '0' => get_string('vacation_travel_transportation_report_select_mxtransportation_false', 'local_mxschool')
);
$types = array(
    'Car' => get_string('vacation_travel_transportation_report_select_type_Car', 'local_mxschool'),
    'Plane' => get_string('vacation_travel_transportation_report_select_type_Plane', 'local_mxschool'),
    'Bus' => get_string('vacation_travel_transportation_report_select_type_Bus', 'local_mxschool'),
    'Train' => get_string('vacation_travel_transportation_report_select_type_Train', 'local_mxschool'),
    'NYC Direct' => get_string('vacation_travel_transportation_report_select_type_NYCDirect', 'local_mxschool'),
    'Non-MX Bus' => get_string('vacation_travel_transportation_report_select_type_Non-MXBus', 'local_mxschool')
);

$table = new transportation_table($view, $filter, $download);

$dropdowns = array(
    new local_mxschool_dropdown(
        'mxtransportation', $mxtransportationoptions, $filter->mxtransportation,
        get_string('report_select_default', 'local_mxschool')
    ),
    new local_mxschool_dropdown(
        'type', $types, $filter->type, get_string('vacation_travel_transportation_report_select_type_all', 'local_mxschool')
    )
);
if (get_config('local_mxschool', 'vacation_form_returnenabled')) {
    array_unshift($dropdowns, new local_mxschool_dropdown('view', $views, $view));
}

$addbutton = new stdClass();
$addbutton->text = get_string('vacation_travel_transportation_report_add', 'local_mxschool');
$addbutton->url = new moodle_url('/local/mxschool/vacation_travel/vacation_enter.php');

$output = $PAGE->get_renderer('local_mxschool');
if ($table->is_downloading()) {
    $renderable = new \local_mxschool\output\report_table($table);
    echo $output->render($renderable);
    die();
}
$renderable = new \local_mxschool\output\report($table, $filter->search, $dropdowns, false, $addbutton);

echo $output->header();
echo $output->heading(get_string("vacation_travel_transportation_report_view_{$view}", 'local_mxschool'));
echo $output->render($renderable);
echo $output->footer();
