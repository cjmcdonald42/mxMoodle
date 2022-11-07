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
 * Edit page for type records for Middlesex's Peer Tutoring Subplugin.
 *
 * @package     local_peertutoring
 * @author      Middlesex Moodle Development Team
 * @copyright   2022 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/locallib.php');

require_login();
require_capability('local/peertutoring:manage', context_system::instance());

$id = optional_param('id', 0, PARAM_INT);

setup_edit_page('type_edit', 'preferences', null, 'peertutoring');

$queryfields = array(
    'local_peertutoring_type' => array(
        'abbreviation' => 't',
        'fields' => array('id', 'displaytext')
    )
);

if ($id) { // Updating an existing record.
    if (!$DB->record_exists('local_peertutoring_type', array('id' => $id, 'deleted' => 0))) {
        redirect_to_fallback();
    }
    $data = get_record($queryfields, 't.id = ?', array($id));
} else { // Creating a new record.
    $data = new stdClass();
    $data->id = $id;
}

$form = new local_peertutoring\local\type_edit_form();
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    update_record($queryfields, $data);
    $action = $data->id ? 'update' : 'create';
    logged_redirect($form->get_redirect(), get_string("type:{$action}:success", 'local_peertutoring'), $action);
}

$output = $PAGE->get_renderer('local_peertutoring');
$renderable = new local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
