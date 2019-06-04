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
 * @package    PACKAGE
 * @subpackage SUBPACKAGE
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('PATH_TO_PLUGIN_HOME/../../config.php');
require_once('PATH_TO_PLUGIN_HOME/locallib.php');
require_once('PATH_TO_PLUGIN_HOME/classes/output/renderable.php');
require_once('PATH_TO_FORM_FILE');

require_login();
require_capability('CAPABILITY', context_system::instance());

$id = optional_param('id', 0, PARAM_INT);

// $parents = TODO: array of parent pages;
$redirect = get_redirect($parents);
$url = 'PATH_TO_THIS_FILE';
$title = get_string('NAME_edit', 'PACKAGE');

setup_mxschool_page($url, $title, $parents);

$queryfields = array(
    'TABLE' => array('abbreviation' => 'ABBREVIATION', 'fields' => array(
        'DATABASE_FIELD' => 'FORM_FIELD' // ETC.
    )) // ETC.
);

if (/* $id && */ !$DB->record_exists('TABLE', array('id' => $id))) {
    redirect($redirect);
}

$data = get_record($queryfields, "WHERE_STRING", array(/* Query parameters. */));
// TODO: Any other static querying.

$form = new FORM_CLASS(array('id' => $id, /* TODO: Other parameters. */));
$form->set_redirect($redirect);
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    // TODO: Data transformations.
    update_record($queryfields, $data);
    logged_redirect(
        $form->get_redirect(), get_string('SUCCESS_STRING', 'PACKAGE'), 'update' /* $data->id ? 'update' : 'create' */
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($title);
echo $output->render($renderable);
echo $output->footer();
