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
 * TODO: Description.
 *
 * @package     PACKAGE
 * @subpackage  SUBPACKAGE
 * @author      PRIMARY AUTHOR
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('PATH_TO_PLUGIN_ROOT/../../config.php');
require_once('PATH_TO_PLUGIN_ROOT/locallib.php');

require_login();
require_capability('CAPABILITY', context_system::instance());

$id = optional_param('id', 0, PARAM_INT);

setup_edit_page('PAGE', 'PARENT', 'SUBPACKAGE', 'PACKAGE');

$queryfields = array(
    'TABLE' => array(
        'abbreviation' => 'ABBREVIATION',
        'fields' => array(
            'DATABASE_FIELD' => 'FORM_FIELD'
            // ETC.
        )
    )
    // ETC.
);

if ($id) { // Updating an existing record.
    if (!$DB->record_exists('TABLE', array('id' => $id, 'deleted' => 0))) {
        redirect_to_fallback();
    }
    $data = get_record($queryfields, 'WHERE_STRING', array(/* Query parameters. */));
    // TODO: Data transformations.
} else { // Creating a new record.
    $data = new stdClass();
    $data->id = $id;
    // TODO: Default data.
}
// TODO: Any other static querying.

$form = new PACKAGE\local\SUBPACKAGE\FORM_CLASS(array(/* TODO: Parameters. */));
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    // TODO: Data transformations.
    update_record($queryfields, $data);
    $action = $data->id ? 'update' : 'create';
    logged_redirect($form->get_redirect(), get_string("SUBPACKAGE:FORM_PREFIX:{$action}:success", 'PACKAGE'), $action);
}

$output = $PAGE->get_renderer('PACKAGE');
$renderable = new \local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
