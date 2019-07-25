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
 * Admin settings for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $settings = new admin_settingpage('signout_settings', new lang_string('settings', 'local_signout'));
    $settings->add(new admin_setting_configtext(
        'local_signout/school_ip', new lang_string('school_ip', 'local_signout'),
        new lang_string('school_ip_description', 'local_signout'), '63.138.153.62'
    ));
    $ADMIN->add('mxschool', $settings);

    $ADMIN->add('indexes', new admin_externalpage(
        'signout_index', new lang_string('signout_index', 'local_signout'),
        "$CFG->wwwroot/local/signout/index.php")
    );
    $ADMIN->add('indexes', new admin_externalpage(
        'on_campus_index', new lang_string('on_campus_index', 'local_signout'),
        "$CFG->wwwroot/local/signout/on_campus/index.php")
    );
    $ADMIN->add('indexes', new admin_externalpage(
        'off_campus_index', new lang_string('off_campus_index', 'local_signout'),
        "$CFG->wwwroot/local/signout/off_campus/index.php")
    );

}
