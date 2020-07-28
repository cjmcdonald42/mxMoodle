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
  * Middlesex Healthpass Dashboard Block.
  *
  * @package     block_mxschool_dash_healthpass
  * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
  * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
  * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
  * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
  */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../local/mxschool/locallib.php');

class block_mxschool_dash_healthpass extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_mxschool_dash_healthpass');
    }

    public function get_content() {
        global $PAGE, $USER;
        if (isset($this->content)) {
            return $this->content;
        }
        $this->content = new stdClass();
        if (get_config('local_mxschool', 'healthpass_enabled')=='1' and has_capability('block/mxschool_dash_healthpass:access', context_system::instance())) {
            $output = $PAGE->get_renderer('local_mxschool');
		  $info = get_todays_healthform_info($USER->id);

		  if($info->submitted_today and !has_capability('local/mxschool:manage_healthpass', context_system::instance())) {
			  if($info->status=='Approved') $renderable = new local_mxschool\output\index(array( // if submitted today and approved
				  get_string('healthpass:submit_form', 'block_mxschool_dash_healthpass') => '/local/mxschool/healthpass/form.php',
			 	   get_string('healthpass:form_approved', 'block_mxschool_dash_healthpass') => '/local/mxschool/healthpass/form_approved.php'
			  ));
			  else $renderable = new local_mxschool\output\index(array( // if submitted today and denied
				  get_string('healthpass:submit_form', 'block_mxschool_dash_healthpass') => '/local/mxschool/healthpass/form.php',
				  get_string('healthpass:form_denied', 'block_mxschool_dash_healthpass') => '/local/mxschool/healthpass/form_denied.php'
			  ));
		  }
		  else $renderable = new local_mxschool\output\index(array( // if not submitted or is admin
			  get_string('healthpass:submit_form', 'block_mxschool_dash_healthpass') => '/local/mxschool/healthpass/form.php'
		  ));

            $this->content->text = $output->render($renderable);
	  }
        return $this->content;
    }

    public function specialization() {
        $this->title = get_string('blockname', 'block_mxschool_dash_healthpass');
    }
}
