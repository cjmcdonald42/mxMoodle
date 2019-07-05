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
 * Database installation steps for Middlesex School's off_campus Subplugin.
 *
 * @package    local_signout
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_signout_install() {
    global $DB;

    set_config('off_campus_edit_window', '30', 'local_signout');
    set_config('off_campus_trip_window', '30', 'local_signout');
    set_config('off_campus_form_enabled', '1', 'local_signout');
    set_config('off_campus_form_ipenabled', '1', 'local_signout');
    set_config('off_campus_form_iperror', 'You must be on Middlesex\'s network to access this form.', 'local_signout');
    set_config('off_campus_report_iperror', 'You must be on Middlesex\'s network to sign in.', 'local_signout');
    set_config('off_campus_form_instructions_passenger', 'Your driver must have submitted a form to be in the list below.', 'local_signout');
    set_config('off_campus_form_instructions_bottom', 'You will have {minutes} minutes to edit your form once you have submitted it.', 'local_signout');
    set_config('off_campus_form_warning_nopassengers', 'Your permissions indicate that you may not drive passengers.', 'local_signout');
    set_config('off_campus_form_warning_needparent', 'Your permissions indicate that you need a call from your parent.', 'local_signout');
    set_config('off_campus_form_warning_onlyspecific', 'Your permissions indicate that you may only be the passenger of the following drivers: ', 'local_signout');
    set_config('off_campus_form_confirmation', 'Have you recieved the required permissions?', 'local_signout');

    set_config('off_campus_notification_warning_irregular', '[Irregular] ', 'local_signout');
    set_config('off_campus_notification_warning_driver', 'None.', 'local_signout');
    set_config('off_campus_notification_warning_any', 'None.', 'local_signout');
    set_config('off_campus_notification_warning_parent', 'This student requires parent permission to be the passenger of another student.', 'local_signout');
    set_config('off_campus_notification_warning_specific', 'This student only has permission to the be the passenger of the following drivers: ', 'local_signout');
    set_config('off_campus_notification_warning_over21', 'This student does NOT have permission to be the passenger of anyone under 21.', 'local_signout');

    $subpackages = array(
        array('package' => 'signout', 'subpackage' => 'off_campus', 'pages' => json_encode(array(
            'preferences' => 'preferences.php', 'form' => 'off_campus_enter.php', 'report' => 'off_campus_report.php'
        )))
    );
    foreach ($subpackages as $subpackage) {
        $DB->insert_record('local_mxschool_subpackage', (object) $subpackage);
    }
}
