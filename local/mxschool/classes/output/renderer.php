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
 * Renderer for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\output;

defined('MOODLE_INTERNAL') || die();

class renderer extends \plugin_renderer_base {

    /**
     * Renders an amd module according to the template.
     *
     * @param local_mxschool\output\amd_module $module.
     * @return string html for the script.
     */
    public function render_amd_module($module) {
        $data = $module->export_for_template($this);
        return parent::render_from_template('local_mxschool/amd_module', $data);
    }

    /**
     * Renders a checkbox according to the template.
     *
     * @param local_mxschool\output\checkbox $checkbox.
     * @return string html for the checkbox.
     */
    public function render_checkbox($checkbox) {
        $data = $checkbox->export_for_template($this);
        return parent::render_from_template('local_mxschool/checkbox', $data);
    }

    /**
     * Renders a email button according to the template.
     *
     * @param local_mxschool\output\email_button $button.
     * @return string html for the button.
     */
    public function render_email_button($button) {
        $data = $button->export_for_template($this);
        return parent::render_from_template("local_mxschool/email_button", $data);
    }

    /**
     * Renders a form according to the template.
     *
     * @param local_mxschool\output\form $form.
     * @return string html for the page.
     */
    public function render_form($form) {
        $data = $form->export_for_template($this);
        return parent::render_from_template('local_mxschool/form', $data);
    }

    /**
     * Renders an index according to the template.
     *
     * @param local_mxschool\output\index $index.
     * @return string html for the index.
     */
    public function render_index($index) {
        $data = $index->export_for_template($this);
        return parent::render_from_template('local_mxschool/index', $data);
    }

    /**
     * Renders a table which serves as a legend according to the template.
     *
     * @param local_mxschool\output\legend_table $legend
     * @return string html for the table.
     */
    public function render_legend_table($legend) {
        $data = $legend->export_for_template($this);
        return parent::render_from_template('local_mxschool/legend_table', $data);
    }

    /**
     * Renders a redirect button according to the template.
     *
     * @param local_mxschool\output\redirect_button $button.
     * @return string html for the button.
     */
    public function render_redirect_button($button) {
        $data = $button->export_for_template($this);
        return parent::render_from_template("local_mxschool/redirect_button", $data);
    }

    /**
     * Renders a report according to the template.
     *
     * @param local_mxschool\output\report $report.
     * @return string html for the report.
     */
    public function render_report($report) {
        $data = $report->export_for_template($this);
        return parent::render_from_template('local_mxschool/report', $data);
    }

    /**
     * Renders a report filter according to the template.
     *
     * @param local_mxschool\output\report_filter $filter.
     * @return string html for the filter.
     */
    public function render_report_filter($filter) {
        $data = $filter->export_for_template($this);
        return parent::render_from_template('local_mxschool/report_filter', $data);
    }

    /**
     * Renders a report table according to the template.
     *
     * @param local_mxschool\output\report_table $table.
     * @return string html for the table.
     */
    public function render_report_table($table) {
        $data = $table->export_for_template($this);
        return parent::render_from_template('local_mxschool/report_table', $data);
    }

    /**
     * Renders a selection button according to the template.
     *
     * @param local_mxschool\output\selection_button $button.
     * @return string html for the button.
     */
    public function render_selection_button($button) {
        $data = $button->export_for_template($this);
        return parent::render_from_template("local_mxschool/selection_button", $data);
    }

    /**
     * Renders a student picture according to the template.
     * @param local_mxschool\output\student_picture $picture.
     *
     * @return string html for the picture.
     */
    public function render_student_picture($picture) {
        $data = $picture->export_for_template($this);
        return parent::render_from_template('local_mxschool/student_picture', $data);
    }

}
