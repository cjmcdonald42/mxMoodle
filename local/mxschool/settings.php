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
 * Admin settings for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $mxschool = new admin_category('mxschool', new lang_string('mxschool_category', 'local_mxschool'));

    $indexes = new admin_category('indexes', new lang_string('indexes', 'local_mxschool'));
    $indexes->add('indexes', new admin_externalpage(
        'main_index', new lang_string('main_index', 'local_mxschool'),
        "$CFG->wwwroot/local/mxschool/index.php"
    ));
    $indexes->add('indexes', new admin_externalpage(
        'user_management_index', new lang_string('user_management_index', 'local_mxschool'),
        "$CFG->wwwroot/local/mxschool/user_management/index.php"
    ));
    $indexes->add('indexes', new admin_externalpage(
        'checkin_index', new lang_string('checkin_index', 'local_mxschool'),
        "$CFG->wwwroot/local/mxschool/checkin/index.php"
    ));
    $indexes->add('indexes', new admin_externalpage(
        'driving_index', new lang_string('driving_index', 'local_mxschool'),
        "$CFG->wwwroot/local/mxschool/driving/index.php"
    ));
    $indexes->add('indexes', new admin_externalpage(
        'advisor_selection_index', new lang_string('advisor_selection_index', 'local_mxschool'),
        "$CFG->wwwroot/local/mxschool/advisor_selection/index.php"
    ));
    $indexes->add('indexes', new admin_externalpage(
        'rooming_index', new lang_string('rooming_index', 'local_mxschool'),
        "$CFG->wwwroot/local/mxschool/rooming/index.php"
    ));
    $indexes->add('indexes', new admin_externalpage(
        'vacation_travel_index', new lang_string('vacation_travel_index', 'local_mxschool'),
        "$CFG->wwwroot/local/mxschool/vacation_travel/index.php"
    ));
    $mxschool->add('mxschool', $indexes);

    $emailsettings = new admin_settingpage('email_settings', new lang_string('email_settings', 'local_mxschool'));
    $emailsettings->add(new admin_setting_configtext(
        'local_mxschool/email_deans', new lang_string('deans_email', 'local_mxschool'),
        new lang_string('deans_email_description', 'local_mxschool'), 'deans@mxschool.edu'
    ));
    $emailsettings->add(new admin_setting_configtext(
        'local_mxschool/email_transportationmanager', new lang_string('transportationmanager_email', 'local_mxschool'),
        new lang_string('transportationmanager_email_description', 'local_mxschool'), 'ptorres@mxschool.edu'
    ));
    $mxschool->add('mxschool', $emailsettings);

    $othersettings = new admin_settingpage('other_settings', new lang_string('other_settings', 'local_mxschool'));
    $othersettings->add(new admin_setting_configtext(
        'local_mxschool/table_size', new lang_string('table_size', 'local_mxschool'),
        new lang_string('table_size_description', 'local_mxschool'), 50, PARAM_INT
    ));
    $othersettings->add(new admin_setting_configtext(
        'local_mxschool/school_ip', new lang_string('school_ip', 'local_mxschool'),
        new lang_string('school_ip_description', 'local_mxschool'), '63.138.153.62'
    ));
    $mxschool->add('mxschool', $othersettings);

    $ADMIN->add('root', $mxschool);

}
