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
 * Checkin preferences page for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once('preferences_form.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/events/page_visited.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:manage_checkin', context_system::instance());

$parents = $parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('checkin', 'local_mxschool') => '/local/mxschool/checkin/index.php'
);
$redirect = new moodle_url($parents[array_keys($parents)[count($parents) - 1]]);
$url = '/local/mxschool/checkin/preferences.php';
$title = get_string('checkin_preferences', 'local_mxschool');

$data = new stdClass();
$data->dormsopen = get_config('local_mxschool', 'dorms_open_date');
$data->secondsemester = get_config('local_mxschool', 'second_semester_start_date');
$data->dormsclose = get_config('local_mxschool', 'dorms_close_date');
$weekends = array();
if ($data->dormsopen && $data->dormsclose) {
    $weekends = $DB->get_records_sql(
        "SELECT sunday_date FROM {local_mxschool_weekend} WHERE sunday_date > ? AND sunday_date < ?",
        array($data->dormsopen, $data->dormsclose)
    );
    $sorted = array();
    foreach ($weekends as $weekend) {
        $sorted[$weekend->sunday_date] = $weekend;
    }
    $date = new DateTime("@$data->dormsopen");
    $date->modify('Sunday this week');
    while ($date->getTimestamp() < $data->dormsclose) {
        if (!isset($sorted[$date->getTimestamp()])) {
            $newweekend = new stdClass();
            $newweekend->sunday_date = $date->getTimestamp();
            $DB->insert_record('local_mxschool_weekend', $newweekend);
        }
        $date->modify('+1 week');
    }
    $weekends = $DB->get_records_sql(
        "SELECT * FROM {local_mxschool_weekend} WHERE sunday_date > ? AND sunday_date < ?",
        array($data->dormsopen, $data->dormsclose)
    );
    foreach ($weekends as $weekend) {
        $identifier = "weekend_$weekend->id";
        $data->{"{$identifier}_type"} = $weekend->type;
        $data->{"{$identifier}_startday"} = $weekend->start_day;
        $data->{"{$identifier}_endday"} = $weekend->end_day;
    }
}

$event = \local_mxschool\event\page_visited::create(array('other' => array('page' => $title)));
$event->trigger();

$PAGE->set_url(new moodle_url($url));
$PAGE->set_context(context_system::instance());
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('incourse');
foreach ($parents as $display => $url) {
    $PAGE->navbar->add($display, new moodle_url($url));
}
$PAGE->navbar->add($title);

$form = new preferences_form(null, array('weekends' => $weekends));
$form->set_redirect($redirect);
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    set_config('dorms_open_date', $data->dormsopen, 'local_mxschool');
    set_config('second_semester_start_date', $data->secondsemester, 'local_mxschool');
    set_config('dorms_close_date', $data->dormsclose, 'local_mxschool');
    foreach ($weekends as $weekend) {
        $identifier = "weekend_$weekend->id";
        $weekend->type = $data->{"{$identifier}_type"};
        $weekend->start_day = $data->{"{$identifier}_startday"};
        $weekend->end_day = $data->{"{$identifier}_endday"};
        $DB->update_record('local_mxschool_weekend', $weekend);
    }
    redirect(
        $form->get_redirect(), get_string('checkin_preferences_edit_success', 'local_mxschool'), null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\form_page($form);

echo $output->header();
echo $output->heading($title);
echo $output->render($renderable);
echo $output->footer();
