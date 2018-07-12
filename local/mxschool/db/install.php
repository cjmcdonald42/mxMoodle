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
 * Database installation steps for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_mxschool_install() {
    set_config(
        'weekend_form_instructions_top',
        'Please fill out the form entirely. Your form should be submitted to your Head of House no later than <b>10:30 PM on Friday</b>.<br>All relevant phone calls giving permission should also be received by Friday at 10:30 PM <i>(Voice mail messages are OK; Email messages are NOT)</i>.',
        'local_mxschool'
    );
    set_config(
        'weekend_form_instructions_bottom',
        'You may not leave for the weekend until you see your name on the \'OK\' list.<br>Permission phone calls should be addressed to <b>{hoh}</b> @ <b>{permissionsline}</b>.<br>If your plans change, you must get permission from <b>{hoh}</b>. <b>Remember to sign out.</b>',
        'local_mxschool'
    );
    set_config(
        'advisor_form_closing_warning',
        'Your current advisor\'s advisory is closing, so you must provide choices for a new advisor.',
        'local_mxschool'
    );
    set_config(
        'advisor_form_instructions',
        'Please rank you top five advisor choices in descending order. You may rank less than five if your final choice is your current advisor.',
        'local_mxschool'
    );
}
