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
 * Student registered vehicles report for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage driving
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once('vehicle_table.php');

require_login();
require_capability('local/mxschool:manage_vehicles', context_system::instance());

$search = optional_param('search', '', PARAM_RAW);
$action = optional_param('action', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('driving', 'local_mxschool') => '/local/mxschool/driving/index.php'
);
$url = '/local/mxschool/driving/vehicle_report.php';
$title = get_string('vehicle_report', 'local_mxschool');

setup_mxschool_page($url, $title, $parents);

if ($action === 'delete' && $id) {
    $record = $DB->get_record('local_mxschool_vehicle', array('id' => $id));
    if ($record) {
        $record->deleted = 1;
        $DB->update_record('local_mxschool_vehicle', $record);
        redirect(
            new moodle_url($url, array('search' => $search)), get_string('vehicle_delete_success', 'local_mxschool'), null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } else {
        redirect(
            new moodle_url($url, array('search' => $search)), get_string('vehicle_delete_failure', 'local_mxschool'), null,
            \core\output\notification::NOTIFY_WARNING
        );
    }
}

$table = new vehicle_table($search);

$addbutton = new stdClass();
$addbutton->text = get_string('vehicle_report_add', 'local_mxschool');
$addbutton->url = new moodle_url('/local/mxschool/driving/vehicle_edit.php');

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\report($table, 50, $search, array(), false, $addbutton);

echo $output->header();
echo $output->heading($title);
echo $output->render($renderable);
echo $output->footer();
