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
 * Generic moodleform with desired defaults to be used for Middlesex School's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

abstract class local_mxschool_form extends moodleform {

    protected const ELEMENT_HIDDEN_INT = array('element' => 'hidden', 'name' => null, 'type' => PARAM_INT);
    protected const ELEMENT_TEXT = array('element' => 'text', 'type' => PARAM_TEXT, 'attributes' => array('size' => 20));
    protected const ELEMENT_TEXT_REQUIRED = array(
        'element' => 'text', 'type' => PARAM_TEXT, 'attributes' => array('size' => 20), 'rules' => array('required')
    );
    protected const ELEMENT_LONG_TEXT = array('element' => 'text', 'type' => PARAM_TEXT, 'attributes' => array('size' => 100));
    protected const ELEMENT_LONG_TEXT_REQUIRED = array(
        'element' => 'text', 'type' => PARAM_TEXT, 'attributes' => array('size' => 100), 'rules' => array('required')
    );
    protected const ELEMENT_YES_NO = array('element' => 'radio', 'options' => array('Yes', 'No'));
    protected const ELEMENT_YES_NO_REQUIRED = array(
        'element' => 'radio', 'options' => array('Yes', 'No'), 'rules' => array('required')
    );
    protected const ELEMENT_BOOLEAN = array('element' => 'radio', 'options' => array(1, 0));
    protected const ELEMENT_BOOLEAN_REQUIRED = array('element' => 'radio', 'options' => array(1, 0), 'rules' => array('required'));
    protected const ELEMENT_EMAIL = array(
        'element' => 'text', 'type' => PARAM_TEXT, 'attributes' => array('size' => 40), 'rules' => array('email')
    );
    protected const ELEMENT_EMAIL_REQUIRED = array(
        'element' => 'text', 'type' => PARAM_TEXT, 'attributes' => array('size' => 40), 'rules' => array('email', 'required')
    );
    protected const ELEMENT_TEXT_AREA = array(
        'element' => 'textarea', 'type' => PARAM_TEXT, 'attributes' => array('rows' => 3, 'cols' => 40)
    );
    protected const ELEMENT_TEXT_AREA_REQUIRED = array(
        'element' => 'textarea', 'type' => PARAM_TEXT, 'attributes' => array('rows' => 3, 'cols' => 40),
        'rules' => array('required')
    );
    protected const ELEMENT_FORMATED_TEXT = array('element' => 'editor');
    protected const ELEMENT_FORMATED_TEXT_REQUIRED = array('element' => 'editor', 'rules' => array('required'));

    /**
     * Generates the field array for a 12-hour time selector with a particular minute step.
     *
     * @param int $step The number of minutes between options.
     * @return array The array to be used as a field code.
     */
    protected static function time_selector($step = 1) {
        if ($step < 1 || $step > 60) {
            $step = 1;
        }
        $hours = array();
        for ($i = 1; $i <= 12; $i++) {
            $hours[$i] = $i;
        }
        $minutes = array();
        for ($i = 0; $i < 60; $i += $step) {
            $minutes[$i] = sprintf("%02d", $i);
        }
        $ampm = array(get_string('am', 'local_mxschool'), get_string('pm', 'local_mxschool'));
        return array('element' => 'group', 'children' => array(
            'hour' => array('element' => 'select', 'options' => $hours),
            'minute' => array('element' => 'select', 'options' => $minutes),
            'ampm' => array('element' => 'select', 'options' => $ampm)
        ));
    }

    /**
     * Generates the parameter array for a standard date selector between the school opening and closing dates.
     *
     * @param bool $optional The value of the 'optional' parameter.
     * @return array Associative array that specifies the parameters to the date_selector.
     */
    protected static function date_parameters_school_year($optional = false) {
        return array(
            'startyear' => format_date('Y', get_config('local_mxschool', 'dorms_open_date')),
            'stopyear' => format_date('Y', get_config('local_mxschool', 'dorms_close_date')),
            'timezone' => core_date::get_user_timezone_object(),
            'optional' => $optional
        );
    }

    /**
     * Generates the field array for an email tag list.
     *
     * @param notification $notification A notification object to query the tags from.
     * @return array The array to be used as a field code.
     */
    protected static function email_tags($notification) {
        $tags = implode(', ', array_map(function($tag) {
            return "{{$tag}}";
        }, $notification->get_tags()));
        return array('element' => 'static', 'text' => $tags);
    }

    /**
     * Creates a new moodleform with custom data.
     * Sets the fallback url to be the server's HTTP_REFERER if it is set, otherwise uses the default fallback.
     */
    public function __construct($customdata = null) {
        parent::__construct(null, $customdata);
        $this->_form->setDefault('redirect', $_SERVER['HTTP_REFERER'] ?? get_fallback_url()->out());
    }

    /**
     * Sets all the fields for the form.
     *
     * @param array $fields Array of fields as category => [name => [properties]].
     * @param string $stringprefix A prefix for any necessary language strings.
     * @param bool $actionstop Whether the submit and cancel buttons should appear at the top of the form as well as at the bottom.
     * @param string $component The component to get language strings from.
     */
    protected function set_fields($fields, $stringprefix, $actionstop = false, $component = 'local_mxschool') {
        if ($actionstop) {
            $this->add_action_buttons();
        }
        $mform = $this->_form;
        $mform->addElement('hidden', 'redirect', null);
        $mform->setType('redirect', PARAM_TEXT);
        foreach ($fields as $category => $categoryfields) {
            if ($category) {
                $mform->addElement('header', $category, get_string("{$stringprefix}_header_{$category}", $component));
                $mform->setExpanded($category);
                $category = "_{$category}";
            }
            foreach ($categoryfields as $name => $properties) {
                $mform->addElement($this->create_element($name, $properties, $stringprefix.$category, $component));
                if (isset($properties['type'])) {
                    $mform->setType($name, $properties['type']);
                }
                if ($properties['element'] === 'group') {
                    foreach ($properties['children'] as $childname => $childproperties) {
                        if (isset($childproperties['type'])) {
                            $mform->setType("{$name}_{$childname}", $childproperties['type']);
                        }
                    }
                }
                if (isset($properties['rules'])) {
                    if (in_array('required', $properties['rules'])) {
                        $mform->addRule($name, null, 'required', null, 'client');
                    }
                    if (in_array('email', $properties['rules'])) {
                        $mform->addRule($name, null, 'email');
                    }
                }
            }
        }
        $this->add_action_buttons();
    }

    /**
     * Creates and returns an element for the form. Has different behavior for different elements.
     * Can be used recursively for grouped elements which will appear on the same line.
     *
     * @param string $name The name of the element (what appears in the html).
     * @param array $properties Variable properties depeding upon element type.
     *        Must include an 'element' key and may optionsally include 'name', 'nameparam', 'options', 'text', and 'children' keys.
     * @param string $stringprefix A prefix for the language string.
     * @param string $component The component to get language strings from.
     * @return HTML_QuickForm_element The newly created element.
     */
    private function create_element($name, $properties, $stringprefix, $component) {
        $mform = $this->_form;
        $tag = array_key_exists('name', $properties) ? $properties['name'] : $name;
        $param = $properties['nameparam'] ?? null;
        $displayname = $properties['displayname'] ?? (
            !isset($properties['ingroup']) && $tag ? get_string("{$stringprefix}_{$tag}", $component, $param) : ''
        );
        $attributes = $properties['attributes'] ?? array();
        $text = $properties['text'] ?? '';
        $useradioindex = $properties['useradioindex'] ?? false;

        switch ($properties['element']) {
            case 'hidden':
                $result = $mform->createElement($properties['element'], $name, null);
                break;
            case 'submit':
            case 'cancel':
                $result = $mform->createElement($properties['element'], $name, $text);
                break;
            case 'editor':
                $result = $mform->createElement($properties['element'], $name, $displayname);
                break;
            case 'text':
            case 'textarea':
                $result = $mform->createElement($properties['element'], $name, $displayname, $attributes);
                break;
            case 'static':
                $result = $mform->createElement($properties['element'], $name, $displayname, $text);
                break;
            case 'checkbox':
            case 'advcheckbox':
                $result = $mform->createElement($properties['element'], $name, $displayname, $text, $attributes);
                break;
            case 'date_selector':
            case 'date_time_selector':
                $result = $mform->createElement(
                    $properties['element'], $name, $displayname, $properties['parameters'], $attributes
                );
                break;
            case 'select':
                $result = $mform->createElement($properties['element'], $name, $displayname, $properties['options'], $attributes);
                break;
            case 'autocomplete':
                $result = $mform->createElement(
                    $properties['element'], $name, $displayname, $properties['options'], $properties['parameters']
                );
                break;
            case 'radio':
                $buttons = array();
                foreach ($properties['options'] as $index => $option) {
                    if ($useradioindex) {
                        $radiodisplay = $option;
                    } else {
                        $optiontext = is_string($option) ? str_replace(' ', '', $option) : $option;
                        $radiodisplay = $optiontext === 'Yes' || $option === 1 ? get_string('yes') : (
                            $optiontext === 'No' || $option === 0 ? get_string('no') : (
                            $tag ? get_string("{$stringprefix}_{$tag}_{$optiontext}", $component, $param) : ''
                        ));
                    }
                    $buttons[] = $mform->createElement(
                        $properties['element'], $name, '', $radiodisplay, $useradioindex ? $index : $option, $attributes
                    );
                }
                $result = $mform->createElement('group', $name, $displayname, $buttons, '&nbsp;', false);
                break;
            case 'group':
                $childelements = array();
                foreach ($properties['children'] as $childname => $childproperties) {
                    $childproperties['ingroup'] = true;
                    $childelements[] = $this->create_element("{$name}_{$childname}", $childproperties, $stringprefix, $component);
                }
                $result = $mform->createElement('group', $name, $displayname, $childelements, '&nbsp;', false);
                break;
            default:
                debugging("unsupported element type: {$properties['element']}", DEBUG_DEVELOPER);
        }
        return $result;
    }

    /**
     * Overrights the default fallback url so long as the form has been neither cancelled nor submitted.
     *
     * @param moodle_url $fallback The url to fall back to after the form is submitted.
     */
    public function set_fallback($fallback) {
        if (!$this->is_submitted()) {
            $this->_form->setDefault('redirect', $fallback->out());
        }
    }

    /**
     * Retrieves the redirect url to be used after the form is submitted or cancelled.
     *
     * @return string url to redirect to.
     */
    public function get_redirect() {
        return $this->_form->exportValue('redirect');
    }

}
