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
 * Content for Middlesex's Dashboard Block for Health Pass.
 *
 * @package    block_mxschool_dash_healthpass
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../local/mxschool/locallib.php');

class block_mxschool_dash_healthpass extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_mxschool_dash_healthpass');
    }

    public function get_content() {
        global $PAGE, $USER;
        if (isset($this->content)) {
            return $this->content;
        }


//


// If you haven't entered a health pass today.
//    Display the form
//
// If you have been approved
//    Display the green approval bar
//    Submit new form
//
// If you have been denied
//    Display the red denial
//    Submit new form option - time delay this by a few minutes.
