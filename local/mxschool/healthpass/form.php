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
 * Middlesex's Health Pass Form.
 *
 * @package     local_mxschool
 * @subpackage  healthpass
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 require(__DIR__.'/../../../config.php');
 require_once(__DIR__.'/../locallib.php');

// All members of the community access this form.
 require_login();

 $id = optional_param('id', 0, PARAM_INT);
 setup_mxschool_page('form', 'healthpass');
 $PAGE->requires->js_call_amd('local_mxschool/healthpass_form', 'setup');

 $output = $PAGE->get_renderer('local_mxschool');
 $renderable = new local_mxschool\output\form($form);

 echo $output->header();
 echo $output->heading(
   get_string('healthpass:form', 'local_mxschool')
 );
 echo $output->render($renderable);
 echo $output->footer();
