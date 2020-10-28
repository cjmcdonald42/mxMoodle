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
 * Page for faculty to edit events for deans permission for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  deans_permission
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
$isstudent = user_is_student();
require_capability('local/mxschool:manage_deans_permission', context_system::instance());

$id = optional_param('id', 0, PARAM_INT);

setup_mxschool_page('event_edit', 'deans_permission');

$queryfields = array(
    'local_mxschool_dp_event' => array(
        'abbreviation' => 'dpe',
        'fields' => array(
            'id', 'name' => 'event_name'
        )
    )
);

if ($id) { // Updating an existing record.
    if (!$DB->record_exists('local_mxschool_dp_event', array('id' => $id))) {
        redirect_to_fallback();
    }
    $data = get_record($queryfields, "dpe.id = ?", array($id));
}
else {
	$data = new stdClass();
	$data->id = $id;
}

$form = new local_mxschool\local\deans_permission\event_edit_form();
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    $id = update_record($queryfields, $data);
    logged_redirect(
        $form->get_redirect(), get_string('deans_permission:event_edit:success', 'local_mxschool'), $data->id ? 'update' : 'create'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
