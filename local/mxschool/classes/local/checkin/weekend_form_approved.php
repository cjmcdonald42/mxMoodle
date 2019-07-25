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
 * Email notification for when a weekend form is approved for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  checkin
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\checkin;

defined('MOODLE_INTERNAL') || die();

/**
 * Email notification for when a weekend form is approved for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  checkin
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class weekend_form_approved extends weekend_form_notification {

    /**
     * @param int $id The id of the weekend form which has been submitted.
     *                The default value of 0 indicates a template email that should not be sent.
     */
    public function __construct($id = 0) {
        parent::__construct('weekend_form_approved', $id);
    }

}
