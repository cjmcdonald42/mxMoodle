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
 * Provides renderable classes for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * Renderable class for indexes.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class index implements renderable, templatable {

    /** @var array $links Array of links [displaytext => url] to be passed to the template.*/
    private $links;
    /** @var string|bool $heading String to display as a subheading or false.*/
    private $heading;

    /**
     * @param array $links Array of links [displaytext => url] to be passed to the template.
     * @param string|bool $heading String to display as a subheading or false.
     */
    public function __construct($links, $heading = false) {
        $this->links = $links;
        $this->heading = $heading;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @return stdClass Object with property links which is an array of stdClass with properties text and url.
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;
        $data = new stdClass();
        $data->heading = $this->heading;
        $data->links = array();
        foreach ($this->links as $text => $url) {
            $data->links[] = array('text' => $text, 'url' => $CFG->wwwroot.$url);
        }
        return $data;
    }
}

/**
 * Renderable class for reports.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report implements renderable, templatable {

    /** @var report_table $table The table for the report.*/
    private $table;
    /** @var report_filter $filter The filter for the report.*/
    private $filter;

    /**
     * @param local_mxschool_table $table The table object to output to the template.
     * @param string $search Default search text, null if there is no search option.
     * @param array $dropdowns Array of local_mxschool_dropdown objects.
     * @param bool $printbutton Whether to display a print button.
     * @param stdClass|bool $addbutton Object with text and url properties for an add button or false.
     * @param array|bool Array of objects with properties text, value, and emailclass or false.
     * @param array|bool $headers Array of headers as ['text', 'length'] to prepend or false.
     */
    public function __construct(
        $table, $search = null, $dropdowns = array(), $printbutton = false, $addbutton = false, $emailbuttons = false,
        $headers = false
    ) {
        $this->table = new report_table($table, $headers);
        $this->filter = new report_filter($search, $dropdowns, $printbutton, $addbutton, $emailbuttons);
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @return stdClass Object with properties filter and table.
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->filter = $output->render($this->filter);
        $data->table = $output->render($this->table);
        return $data;
    }

}

/**
 * Renderable class for report tables.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_table implements renderable, templatable {

    /** @var local_mxschool_table $table The table object to output to the template.*/
    private $table;
    /** @var array|bool $headers Array of headers as ['text', 'length'] to prepend or false.*/
    private $headers;

    /**
     * @param local_mxschool_table $table The table object to output to the template.
     * @param array|bool $headers Array of headers as ['text', 'length'] to prepend or false.
     */
    public function __construct($table, $headers = false) {
        $this->table = $table;
        $this->headers = $headers;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @return stdClass Object with properties table and headers.
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        ob_start();
        $this->table->out(get_config('local_mxschool', 'table_size'), true);
        $data->table = ob_get_clean();
        $data->headers = $this->headers ? json_encode($this->headers) : $this->headers;
        return $data;
    }

}

/**
 * Renderable class for report filters.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_filter implements renderable, templatable {

    /** @var string $search Default search text, null if there is no search option.*/
    private $search;
    /** @param array $dropdowns Array of local_mxschool_dropdown objects.*/
    private $dropdowns;
    /** @var bool $printbutton Whether to display a print button.*/
    private $printbutton;
    /** @var stdClass|bool $addbutton Object with text and url properties for an add button or false.*/
    private $addbutton;
    /** @var array $emailbuttons Array of email_button objects.*/
    private $emailbuttons;

    /**
     * @param string $search Default search text, null if there is no search option.
     * @param array $dropdowns Array of local_mxschool_dropdown objects.
     * @param bool $printbutton Whether to display a print button.
     * @param stdClass|bool $addbutton Object with text and url properties for an add button or false.
     * @param array|bool $emailbuttons Array of objects with properties text, value, and emailclass or false.
     */
    public function __construct($search, $dropdowns, $printbutton, $addbutton, $emailbuttons) {
        $this->search = $search;
        $this->dropdowns = $dropdowns;
        $this->printbutton = $printbutton;
        $this->addbutton = $addbutton;
        $this->emailbuttons = array();
        if ($emailbuttons) {
            foreach ($emailbuttons as $emailbutton) {
                $this->emailbuttons[] = new email_button(
                    $emailbutton->text, isset($emailbutton->value) ? $emailbutton->value : 0, $emailbutton->emailclass
                );
            }
        }
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @return stdClass Object with properties url, dropdowns, searchable, search, printable, addbutton, and emailbuttons.
     */
    public function export_for_template(renderer_base $output) {
        global $PAGE;
        $data = new stdClass();
        $data->url = $PAGE->url;
        $data->dropdowns = array();
        foreach ($this->dropdowns as $dropdown) {
            $data->dropdowns[] = \html_writer::select($dropdown->options, $dropdown->name, $dropdown->selected, $dropdown->nothing);
        }
        $data->searchable = $this->search !== null;
        $data->search = $this->search;
        $data->filterable = $data->searchable || count($data->dropdowns);
        $data->printable = $this->printbutton;
        if ($this->addbutton) {
            $data->addbutton = new stdClass();
            $data->addbutton->text = $this->addbutton->text;
            $data->addbutton->url = $this->addbutton->url->out();
        }
        $data->emailbuttons = array();
        foreach ($this->emailbuttons as $emailbutton) {
            $data->emailbuttons[] = $output->render($emailbutton);
        }
        return $data;
    }

}

/**
 * Renderable class for moodle forms.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class form implements renderable, templatable {

    /** @var local_mxschool_form $form The form object to render.*/
    private $form;
    /** @var string|bool $topdescription A description for the top of the form or false.*/
    private $descrption;
    /** @var string|bool $bottomdescription A description for the bottom of the form or false.*/
    private $bottomdescription;

    /**
     * @param local_mxschool_form $form The form object to render.
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
     * @return stdClass Object with properties form, topdescription, and bottomdescription.
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        ob_start();
        $this->form->display();
        $data->form = ob_get_clean();
        $data->topdescription = $this->topdescription;
        $data->bottomdescription = $this->bottomdescription;
        return $data;
    }
}

/**
 * Renderable class for amd modules.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class amd_module implements renderable, templatable {

    /** @var string $module The name of the amd module.*/
    private $module;

    /**
     * @param string $module The name of the amd module.
     */
    public function __construct($module) {
        $this->module = $module;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @return stdClass Object with property name.
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->module = $this->module;
        return $data;
    }

}

/**
 * Renderable class for checkboxes.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class checkbox implements renderable, templatable {

    /** @var string $value The value attribute of the checkbox.*/
    private $value;
    /** @var string $table The table in the database which the checkbox corresponds to.*/
    private $table;
    /** @var string $field The field in the database which the checkbox corresponds to.*/
    private $field;
    /** @var bool $checked Whether the checkbox should be checked by default.*/
    private $checked;

    /**
     * @param string $value The value attribute of the checkbox.
     * @param string $table The table in the database which the checkbox corresponds to.
     * @param string $field The field in the database which the checkbox corresponds to.
     * @param bool $checked Whether the checkbox should be checked by default.
     */
    public function __construct($value, $table, $field, $checked) {
        $this->value = $value;
        $this->table = $table;
        $this->field = $field;
        $this->checked = $checked;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @return stdClass Object with properties value, name, checked, and table.
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->value = $this->value;
        $data->table = $this->table;
        $data->field = $this->field;
        $data->checked = $this->checked;
        return $data;
    }

}

/**
 * Renderable class for tables which serve as legends.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class legend_table implements renderable, templatable {

    /** @var array $rows $rows The rows of the table as arrays with keys leftclass, lefttext, rightclass, and righttext.*/
    private $rows;

    /**
     * @param array $rows The rows of the table as arrays with keys leftclass, lefttext, rightclass, and righttext.
     */
    public function __construct($rows) {
        $this->rows = $rows;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @return stdClass Object with properties value and emailclass.
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->rows = $this->rows;
        return $data;
    }

}

/**
 * Renderable class for email buttons.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class email_button implements renderable, templatable {

    /** @var string The text to display on the button.*/
    private $text;
    /** @var int The value attribute of the button.*/
    private $value;
    /** @var string The string identifier for the email.*/
    private $emailclass;
    /** @var bool Whether the button should be hidden by default and should have show and hide functionality.*/
    private $hidden;

    /**
     * @param string $text The text to display on the button.
     * @param int $value The value attribute of the button.
     * @param string $emailclass The string identifier for the email.
     * @param bool $hidden Whether the button should be hidden by default and should have show and hide functionality.
     */
    public function __construct($text, $value, $emailclass, $hidden = false) {
        $this->text = $text;
        $this->value = $value;
        $this->emailclass = $emailclass;
        $this->hidden = $hidden;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @return stdClass Object with properties value and emailclass.
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->text = $this->text;
        $data->value = $this->value;
        $data->emailclass = $this->emailclass;
        $data->hidden = $this->hidden;
        return $data;
    }

}

/**
 * Renderable class for sign in buttons.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class signin_button implements renderable, templatable {

    /** @var int The value attribute of the button.*/
    private $value;

    /**
     * @param int $value The value attribute of the button.
     */
    public function __construct($value) {
        $this->value = $value;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @return stdClass Object with property value.
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->value = $this->value;
        return $data;
    }

}

/**
 * Renderable class for selection buttons.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class selection_button implements renderable, templatable {

    /** @var int $student The user id of the affected student.*/
    private $student;
    /** @var int $option The user id of the selected option.*/
    private $option;
    /** @var string $displaytext The text to display on the button.*/
    private $displaytext;

    /**
     * @param int $student The user id of the affected student.
     * @param int $option The user id of the selected option.
     * @param string $displaytext The text to display on the button.
     */
    public function __construct($student, $option, $displaytext) {
        $this->student = $student;
        $this->option = $option;
        $this->displaytext = $displaytext;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @return stdClass Object with properties value and display text.
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $value = new stdClass();
        $value->student = $this->student;
        $value->choice = $this->option;
        $data->value = json_encode($value);
        $data->displaytext = $this->displaytext;
        return $data;
    }

}
