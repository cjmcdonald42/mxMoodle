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
 * Preferences page for Middlesex School's Peer Tutoring Subplugin.
 *
 * @package    local_peertutoring
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once('department_table.php');
require_once('course_table.php');
require_once('type_table.php');
require_once('rating_table.php');
require_once(__DIR__.'/../mxschool/classes/output/renderable.php');
require_once(__DIR__.'/../mxschool/classes/events/page_visited.php');
require_once(__DIR__.'/../mxschool/locallib.php');

require_login();
require_capability('local/peertutoring:manage_preferences', context_system::instance());

$action = optional_param('action', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);
$table = optional_param('table', '', PARAM_RAW);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('pluginname', 'local_peertutoring') => '/local/peertutoring/index.php'
);
$url = '/local/peertutoring/preferences.php';
$title = get_string('preferences', 'local_peertutoring');

if ($action === 'delete' && $id && $table) {
    switch($table) {
        case 'department':
            $dbtable = 'local_peertutoring_dept';
            break;
        case 'course':
            $dbtable = 'local_peertutoring_course';
            break;
        case 'type':
            $dbtable = 'local_peertutoring_type';
            break;
        case 'rating':
            $dbtable = 'local_peertutoring_rating';
            break;
        default:
            redirect(
                new moodle_url($url, array('search' => $search)), get_string('table_delete_failure', 'local_peertutoring'), null,
                \core\output\notification::NOTIFY_WARNING
            );
    }
    $record = $DB->get_record($dbtable, array('id' => $id));
    if ($record) {
        $record->deleted = 1;
        $DB->update_record($dbtable, $record);
        redirect(
            new moodle_url($url, array('search' => $search)), get_string("{$table}_delete_success", 'local_peertutoring'), null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } else {
        redirect(
            new moodle_url($url, array('search' => $search)), get_string("{$table}_delete_failure", 'local_peertutoring'), null,
            \core\output\notification::NOTIFY_WARNING
        );
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

$departmenttable = new department_table('department_table');
$coursetable = new course_table('course_table');
$typetable = new type_table('type_table');
$ratingtable = new rating_table('rating_table');

$departmentadd = new stdClass();
$departmentadd->text = get_string('department_report_add', 'local_peertutoring');
$departmentadd->url = new moodle_url('/local/peertutoring/department_edit.php');
$courseadd = new stdClass();
$courseadd->text = get_string('course_report_add', 'local_peertutoring');
$courseadd->url = new moodle_url('/local/peertutoring/course_edit.php');
$typeadd = new stdClass();
$typeadd->text = get_string('type_report_add', 'local_peertutoring');
$typeadd->url = new moodle_url('/local/peertutoring/type_edit.php');
$ratingadd = new stdClass();
$ratingadd->text = get_string('rating_report_add', 'local_peertutoring');
$ratingadd->url = new moodle_url('/local/peertutoring/rating_edit.php');

$output = $PAGE->get_renderer('local_mxschool');
$departmentrenderable = new \local_mxschool\output\report_page(
    'department-table', $departmenttable, 50, null, array(), false, $departmentadd
);
$courserenderable = new \local_mxschool\output\report_page(
    'course-table', $coursetable, 50, null, array(), false, $courseadd
);
$typerenderable = new \local_mxschool\output\report_page(
    'type-table', $typetable, 50, null, array(), false, $typeadd
);
$ratingrenderable = new \local_mxschool\output\report_page(
    'rating-table', $ratingtable, 50, null, array(), false, $ratingadd
);

echo $output->header();
echo $output->heading(get_string('department_report', 'local_peertutoring'));
echo $output->render($departmentrenderable);
echo $output->heading(get_string('course_report', 'local_peertutoring'));
echo $output->render($courserenderable);
echo $output->heading(get_string('type_report', 'local_peertutoring'));
echo $output->render($typerenderable);
echo $output->heading(get_string('rating_report', 'local_peertutoring'));
echo $output->render($ratingrenderable);
echo $output->footer();
