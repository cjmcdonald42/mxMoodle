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
 * Generic class to encapsulate dropdown fields for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\output;

defined('MOODLE_INTERNAL') || die();

class dropdown implements \renderable, \templatable {

    /** @var string The name of the dropdown, which serves as the url parameter from the filter.*/
    public $name;
    /** @param array The options for the dropdown.*/
    public $options;
    /** @param string The initially selected option.*/
    public $selected;
    /** @param string|bool A 'nothing' option or false if there is no such option.*/
    public $default;

    /**
     * @param string $name The name of the dropdown, which serves as the url parameter from the filter.
     * @param array $options The options for the dropdown.
     * @param string $selected The initially selected option.
     * @param string|bool $default A 'nothing' option or false if there is no such option.
     */
    public function __construct($name, $options, $selected, $default = false) {
        $this->name = $name;
        $this->options = $options;
        $this->selected = $selected;
        $this->default = $default;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output The renderer which is rendering this renderable.
     * @return stdClass Object with properties name, options, and selected.
     */
    public function export_for_template($output) {
        $data = new \stdClass();
        $data->name = $this->name;
        $options = $this->default ? array('' => $this->default) + $this->options : $this->options;
        $data->options = array_map(function($value, $text) {
            $option = new \stdClass();
            $option->value = $value;
            $option->text = $text;
            $option->selected = (string) $value === $this->selected;
            return $option;
        }, array_keys($options), $options);
        return $data;
    }

    /**
     * Generates a dropdown object for all dorms and optionally all day houses.
     *
     * @param string $selected The currently selected option.
     * @param bool $includeday Whether to include day houses or limit to boading houses.
     * @return dropdown Object with the specified properties.
     */
    public static function dorm_dropdown($selected, $includeday = true) {
        $options = get_dorm_list($includeday);
        if ($includeday) {
            $options = array(
                -2 => get_string('report_select_boarding', 'local_mxschool'),
                -1 => get_string('report_select_day', 'local_mxschool')
            ) + $options;
        }
        return new dropdown(
            'dorm', $options, $selected, get_string($includeday ? 'report_select_house' : 'report_select_dorm', 'local_mxschool')
        );
    }

}
