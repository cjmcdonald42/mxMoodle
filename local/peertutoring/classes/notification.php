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
 * Generic email notification which serves as a superclass for all email notifications sent
 * by Middlesex's Peer Tutoring Subplugin.
 *
 * @package     local_peertutoring
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_peertutoring;

defined('MOODLE_INTERNAL') || die();

abstract class notification extends \local_mxschool\notification {

    /**
     * Generates a user object to which emails should be sent to reach the peer tutor administrator.
     * @return stdClass The peer tutor administrator user object.
     */
    final protected static function get_peertutoradmin_user() {
        $supportuser = \core_user::get_support_user();
        $peertutoradmin = clone $supportuser;
        $peertutoradmin->email = get_config('local_peertutoring', 'email_peertutoradmin');
        $peertutoradmin->addresseename = get_config('local_peertutoring', 'addressee_peertutoradmin');
        return $peertutoradmin;
    }

}
