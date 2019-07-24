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
 * Provides renderable classes for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_signout\output;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../../mxschool/classes/output/renderable.php');

use renderable;
use renderer_base;
use templatable;
use local_mxschool\output\button;
use stdClass;

/**
 * Renderable class for sign-in buttons.
 *
 * @package     local_signout
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class signin_button extends button {
    // Data is exact same as generic button.
}

/**
 * Renderable class for confirmation buttons.
 *
 * @package     local_signout
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class confirmation_button extends button {

    /** @var int The id of the on-campus signout record which the button interacts with.*/
    private $value;

    /**
     * @param int $value The id of the on-campus signout record which the button interacts with.
     */
    public function __construct($value) {
        parent::__construct(get_string('confirmation_button', 'local_signout'));
        $this->value = $value;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @return stdClass Object with properties text and value.
     */
    public function export_for_template(renderer_base $output) {
        $data = parent::export_for_template($output);
        $data->value = $this->value;
        return $data;
    }

}
