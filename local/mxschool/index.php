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
 * Main index page for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once(__DIR__.'/classes/output/renderable.php');
require_once(__DIR__.'/locallib.php');

if (!has_capability('moodle/site:config', context_system::instance())) {
    redirect(new moodle_url('/my'));
}

admin_externalpage_setup('mxschool_index');

$url = '/local/mxschool/index.php';
$title = get_string('pluginname', 'local_mxschool');

setup_generic_page($url, $title);

$subpackages = $DB->get_fieldset_select('local_mxschool_subpackage', 'id', '');

$output = $PAGE->get_renderer('local_mxschool');

echo $output->header();
echo $output->heading($title);
foreach ($subpackages as $id) {
    echo $output->render(generate_index($id, true));
}
echo $output->footer();
