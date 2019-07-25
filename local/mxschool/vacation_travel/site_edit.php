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
 * Edit page for vacation travel site records for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  vacation_travel
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:manage_vacation_travel_preferences', context_system::instance());

$id = optional_param('id', 0, PARAM_INT);

setup_edit_page('site_edit', 'preferences', 'vacation_travel');

$queryfields = array('local_mxschool_vt_site' => array('abbreviation' => 's', 'fields' => array(
    'id', 'name', 'type', 'enabled_departure' => 'departureenabled', 'enabled_return' => 'returnenabled',
    'default_departure_time' => 'defaultdeparturetime', 'default_return_time' => 'defaultreturntime'
)));

if ($id) { // Updating an existing record.
    if (!$DB->record_exists('local_mxschool_vt_site', array('id' => $id, 'deleted' => 0))) {
        redirect_to_fallback();
    }
    $data = get_record($queryfields, 's.id = ?', array($id));
} else { // Creating a new record.
    $data = new stdClass();
    $data->id = $id;
}

$form = new local_mxschool\local\vacation_travel\site_edit_form();
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    if (!$data->defaultdeparturetime) {
        unset($data->defaultdeparturetime);
    }
    if (!$data->defaultreturntime) {
        unset($data->defaultreturntime);
    }
    update_record($queryfields, $data);
    logged_redirect(
        $form->get_redirect(),
        get_string($data->id ? 'vacation_travel_site_edit_success' : 'vacation_travel_site_create_success', 'local_mxschool'),
        $data->id ? 'update' : 'create'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
