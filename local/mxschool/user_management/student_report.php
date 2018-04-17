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
 * Student management report for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once('student_table.php');

require_login();
require_capability('local/mxschool:view_users', context_system::instance());

$url = '/local/mxschool/user_management/student_report.php';
$title = get_string('student_report', 'local_mxschool');

$PAGE->set_url(new moodle_url($url));
$PAGE->set_context(context_system::instance());
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('incourse');
$PAGE->navbar->add(get_string('pluginname', 'local_mxschool'), new moodle_url('/local/mxschool/index.php'));
$PAGE->navbar->add(get_string('user_management', 'local_mxschool'), new moodle_url('/local/mxschool/user_management/index.php'));
$PAGE->navbar->add($title);

$table = new student_table('student_table');
