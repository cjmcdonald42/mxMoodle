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
 * Student picture bulk import page for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  user_management
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/picture_import_form.php');

require_login();
require_capability('local/mxschool:manage_student_pictures', context_system::instance());

setup_mxschool_page('picture_import', 'user_management');

$data = new stdClass();
$data->pictures = file_get_submitted_draft_itemid('pictures');
file_prepare_draft_area($data->pictures, 1, 'local_mxschool', 'student_pictures', 0);

$form = new picture_import_form();
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    file_save_draft_area_files($data->pictures, 1, 'local_mxschool', 'student_pictures', 0);
    if ($data->clear) {
        clear_student_pictures();
        logged_redirect($PAGE->url, get_string('user_management_picture_delete_success', 'local_mxschool'), 'delete');
    } else {
        logged_redirect($form->get_redirect(), get_string('user_management_picture_import_success', 'local_mxschool'), 'create');
    }
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
