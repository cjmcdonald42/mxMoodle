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
 * Middlesex's Dashboard Block for the Deans.
 *
 * @package    block_mxschool_dash_dean
 * @author     Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'block_mxschool_dash_dean';
$plugin->version = 2020072300;
$plugin->release = 'v3.1';
$plugin->requires = 2019052000; // Moodle 3.7.
$plugin->maturity = MATURITY_STABLE;
$plugin->dependencies = array(
    'local_mxschool' => 2020072200, // MXSchool v3.2.
    'local_signout' => 2019081400 // eSignout v3.1.
);
