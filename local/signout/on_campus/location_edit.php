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
 * Edit page for on-campus location records for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @subpackage  on_campus
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../../mxschool/locallib.php');
require_once(__DIR__.'/../../mxschool/classes/output/renderable.php');
require_once(__DIR__.'/location_edit_form.php');

require_login();
require_capability('local/signout:manage_on_campus_preferences', context_system::instance());

$id = optional_param('id', 0, PARAM_INT);

setup_edit_page('location_edit', 'preferences', 'on_campus', 'signout');

$queryfields = array('local_signout_location' => array('abbreviation' => 'l', 'fields' => array(
    'id', 'name', 'grade', 'enabled', 'start_date' => 'start', 'end_date' => 'end', 'warning'
)));

if ($id && !$DB->record_exists('local_signout_location', array('id' => $id, 'deleted' => 0))) {
    redirect_to_fallback();
}

$data = get_record($queryfields, 'l.id = ?', array($id));
if (!$id) {
    $data->enabled = '-1'; // Invalid default to prevent auto selection.
}
$data->warning = array('text' => $data->warning);

$form = new location_edit_form(array('id' => $id));
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    if (!$data->start) {
        unset($data->start);
    }
    if (!$data->end) {
        unset($data->end);
    }
    if ($data->warning['text']) {
        $data->warning = $data->warning['text'];
    } else {
        unset($data->warning);
    }
    update_record($queryfields, $data);
    logged_redirect(
        $form->get_redirect(),
        get_string($data->id ? 'on_campus_location_edit_success' : 'on_campus_location_create_success', 'local_signout'),
        $data->id ? 'update' : 'create'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
