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
 * Renderer for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_signout\output;

defined('MOODLE_INTERNAL') || die();

class renderer extends \plugin_renderer_base {

    /**
     * Renders a confirmation button according to the template.
     *
     * @param local\signout\output\confirmation_button $button.
     * @return string html for the button.
     */
    public function render_confirmation_button($button) {
        $data = $button->export_for_template($this);
        return parent::render_from_template('local_signout/confirmation_button', $data);
    }

    /**
     * Renders a sign-in button according to the template.
     *
     * @param local\signout\output\signin_button $button.
     * @return string html for the button.
     */
    public function render_signin_button($button) {
        $data = $button->export_for_template($this);
        return parent::render_from_template('local_signout/signin_button', $data);
    }

}
