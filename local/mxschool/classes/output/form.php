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
 * Renderable class for moodle forms for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\output;

defined('MOODLE_INTERNAL') || die();

class form implements \renderable, \templatable {

    /** @var local_mxschool\form The form object to render.*/
    private $form;
    /** @var string|bool A description for the top of the form or false.*/
    private $descrption;
    /** @var string|bool A description for the bottom of the form or false.*/
    private $bottomdescription;

    /**
     * @param local_mxschool\form $form The form object to render.
     * @param string|bool $topdescription A description for the top of the form or false.
     * @param string|bool $bottomdescription A description for the bottom of the form or false.
     */
    public function __construct($form, $topdescription = false, $bottomdescription = false) {
        $this->form = $form;
        $this->topdescription = $topdescription;
        $this->bottomdescription = $bottomdescription;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output The renderer which is rendering this renderable.
     * @return stdClass Object with properties form, topdescription, and bottomdescription.
     */
    public function export_for_template(\renderer_base $output) {
        $data = new \stdClass();
        ob_start();
        $this->form->display();
        $data->form = ob_get_clean();
        $data->topdescription = $this->topdescription;
        $data->bottomdescription = $this->bottomdescription;
        return $data;
    }

}
