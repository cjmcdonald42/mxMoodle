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
 * @author      AUTHOR
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('PATH_TO_PLUGIN_HOME/../../config.php');
require_once('PATH_TO_PLUGIN_HOME/locallib.php');

require_login();
require_capability('CAPABILITY', context_system::instance());

$filter = new stdClass();
// TODO: Save URL parameters.

setup_mxschool_page('PAGE', 'SUBPACKAGE', 'PACKAGE');

// TODO: Any static querying.

$table = new PACKAGE\local\SUBPACKAGE\TABLE_CLASS($filter);
$dropdowns = array(
    new local_mxschool\output\dropdown('NAME', /* array of options */, 'SELECTED', /* default option (optional) */)
    // ETC.
);
$buttons = array(
    new local_mxschool\output\redirect_button('TEXT', new moodle_url('URL')),
    new local_mxschool\output\email_button('TEXT', 'EMAIL_CLASS')
    // ETC.
);

$output = $PAGE->get_renderer('PACKAGE');
$renderable = new \local_mxschool\output\report(
    $table, 'SEARCH', $dropdowns, $buttons, /* true or false for printbutton */, /* array or false for extra row of headers */
);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
