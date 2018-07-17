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
 * Vehicle edit page for Middlesex School's Dorm and Student functions plugin.
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
require_once('vehicle_edit_form.php');

require_login();
require_capability('local/mxschool:manage_vehicles', context_system::instance());

$id = optional_param('id', 0, PARAM_INT);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('driving', 'local_mxschool') => '/local/mxschool/driving/index.php'
);
$redirect = get_redirect($parents);
$url = '/local/mxschool/driving/vehicle_edit.php';
$title = get_string('vehicle_edit', 'local_mxschool');

setup_mxschool_page($url, $title, $parents);

$queryfields = array('local_mxschool_vehicle' => array('abbreviation' => 'v', 'fields' => array(
    'id', 'userid' => 'student', 'make', 'model', 'color', 'registration'
)));

if ($id && !$DB->record_exists('local_mxschool_vehicle', array('id' => $id))) {
    redirect($redirect);
}

$data = get_record($queryfields, "v.id = ?", array('id' => $id));
$drivers = get_licensed_student_list();

$form = new vehicle_edit_form(array('id' => $id, 'drivers' => $drivers));
$form->set_redirect($redirect);
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    update_record($queryfields, $data);
    redirect(
        $form->get_redirect(), get_string('vehicle_edit_success', 'local_mxschool'), null, \core\output\notification::NOTIFY_SUCCESS
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($title);
echo $output->render($renderable);
echo $output->footer();
