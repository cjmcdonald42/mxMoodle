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
 * Renderer for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\output;

defined('MOODLE_INTERNAL') || die;

use plugin_renderer_base;

class renderer extends plugin_renderer_base {

    /**
     * Renders an index page according to the template.
     *
     * @param index_page $page.
     *
     * @return string html for the page.
     */
    public function render_index_page($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('local_mxschool/index_page', $data);
    }

    /**
     * Renders a report page according to the template.
     *
     * @param report_page $page.
     *
     * @return string html for the page.
     */
    public function render_report_page($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('local_mxschool/report_page', $data);
    }

    /**
     * Renders a report table according to the template.
     *
     * @param report_table $table.
     *
     * @return string html for the table.
     */
    public function render_report_table($table) {
        $data = $table->export_for_template($this);
        return parent::render_from_template('local_mxschool/report_table', $data);
    }

    /**
     * Renders a report filter according to the template.
     *
     * @param report_filter $filter.
     *
     * @return string html for the filter.
     */
    public function render_report_filter($filter) {
        $data = $filter->export_for_template($this);
        return parent::render_from_template('local_mxschool/report_filter', $data);
    }

    /**
     * Renders a form page according to the template.
     *
     * @param form_page $page.
     *
     * @return string html for the page.
     */
    public function render_form_page($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('local_mxschool/form_page', $data);
    }

    /**
     * Renders an amd module according to the template.
     *
     * @param js_module $module.
     *
     * @return string html for the script.
     */
    public function render_js_module($module) {
        $data = $module->export_for_template($this);
        return parent::render_from_template('local_mxschool/js_module', $data);
    }

    /**
     * Renders a checkbox according to the template.
     *
     * @param checkbox $checkbox.
     *
     * @return string html for the checkbox.
     */
    public function render_checkbox($checkbox) {
        $data = $checkbox->export_for_template($this);
        return parent::render_from_template('local_mxschool/checkbox', $data);
    }

    /**
     * Renders a table which serves as a legend according to the template.
     *
     * @param legend_table $legend
     *
     * @return string html for the table.
     */
    public function render_legend_table($legend) {
        $data = $legend->export_for_template($this);
        return parent::render_from_template('local_mxschool/legend_table', $data);
    }

    /**
     * Renders am email button according to the template.
     *
     * @param email_button $button.
     *
     * @return string html for the button.
     */
    public function render_email_button($button) {
        $data = $button->export_for_template($this);
        return parent::render_from_template('local_mxschool/email_button', $data);
    }

    /**
     * Renders a sign in button according to the template.
     *
     * @param signin_button $button.
     *
     * @return string html for the button.
     */
    public function render_signin_button($button) {
        $data = $button->export_for_template($this);
        return parent::render_from_template('local_mxschool/signin_button', $data);
    }

    /**
     * Renders a selection button according to the template.
     *
     * @param selection_button $button.
     *
     * @return string html for the button.
     */
    public function render_selection_button($button) {
        $data = $button->export_for_template($this);
        return parent::render_from_template('local_mxschool/selection_button', $data);
    }

}
