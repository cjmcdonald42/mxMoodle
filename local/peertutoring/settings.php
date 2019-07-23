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
 * Admin settings for Middlesex's Peer Tutoring Subplugin.
 *
 * @package     local_peertutoring
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $settings = new admin_settingpage('peer_tutoring_settings', new lang_string('settings', 'local_peertutoring'));
    $settings->add(new admin_setting_configtext(
        'local_peertutoring/email_peertutoradmin', new lang_string('peertutoradmin_email', 'local_peertutoring'),
        new lang_string('peertutoradmin_email_description', 'local_peertutoring'), 'kmagee@mxschool.edu'
    ));
    $settings->add(new admin_setting_configtext(
        'local_peertutoring/addressee_peertutoradmin', new lang_string('peertutoradmin_addressee', 'local_peertutoring'),
        new lang_string('peertutoradmin_addressee_description', 'local_peertutoring'), 'peer tutoring administrator'
    ));
    $ADMIN->add('mxschool', $settings);

    $ADMIN->add('indexes', new admin_externalpage(
        'peertutoring_index', new lang_string('peertutoring_index', 'local_peertutoring'),
        "$CFG->wwwroot/local/peertutoring/index.php")
    );

}
