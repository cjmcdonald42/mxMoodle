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
 * On-campus preferences page for Middlesex School's eSignout Subplugin.
 *
 * @package    local_signout
 * @subpackage on_campus
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../../mxschool/locallib.php');
require_once(__DIR__.'/../../mxschool/classes/output/renderable.php');
require_once('preferences_form.php');

require_login();
require_capability('local/signout:manage_off_campus_preferences', context_system::instance());

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('pluginname', 'local_signout') => '/local/signout/index.php',
    get_string('on_campus', 'local_signout') => '/local/signout/on_campus/index.php'
);
$redirect = get_redirect($parents);
$url = '/local/signout/on_campus/preferences.php';
$title = get_string('on_campus_preferences', 'local_signout');

setup_mxschool_page($url, $title, $parents);

$data = new stdClass();
$data->oncampusenabled = get_config('local_signout', 'on_campus_form_enabled');
$data->ipenabled = get_config('local_signout', 'on_campus_form_ipenabled');

$form = new preferences_form();
$form->set_redirect($redirect);
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    set_config('on_campus_form_enabled', $data->oncampusenabled, 'local_signout');
    set_config('on_campus_form_ipenabled', $data->ipenabled, 'local_signout');
    logged_redirect(
        $form->get_redirect(), get_string('on_campus_preferences_edit_success', 'local_signout'), 'update'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($title);
echo $output->render($renderable);
echo $output->footer();
