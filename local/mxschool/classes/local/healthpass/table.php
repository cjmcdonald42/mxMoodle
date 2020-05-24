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
 * Health report table for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  healthpass
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 namespace local_mxschool\local\healthpass;

 defined('MOODLE_INTERNAL') || die();

 class table extends \local_mxschool\table {

   /**
    * Creates a new table.
    *
    * @param stdClass $filter Any filtering for the table.
    */
   public function __construct($filter) {
       global $DB;
       $columns = array('userid', 'status', 'body_temperature', 'has_fever',
                        'has_sore_throat', 'has_cough', 'has_runny_nose',
                        'has_muscle_aches', 'has_loss_of_sense', 'has_short_breath');
       $headers = $this->generate_headers($columns, 'healthpass:report');
       $sortable = array('userid', 'status', 'body_temperature');
       $centered = array('userid', 'status', 'body_temperature', 'has_fever',
                        'has_sore_throat', 'has_cough', 'has_runny_nose',
                        'has_muscle_aches', 'has_loss_of_sense', 'has_short_breath');
       parent::__construct('health_table', $columns, $headers, $sortable, $centered, $filter, false);

       $fields = array("CONCAT(u.lastname, ', ', u.firstname) AS userid", 'hp.status',
                        'hp.body_temperature', 'hp.has_fever', 'hp.has_sore_throat',
                        'hp.has_cough', 'hp.has_runny_nose', 'hp.has_muscle_aches',
                         'hp.has_loss_of_sense', 'hp.has_short_breath');
       $from = array('{local_mxschool_healthpass} hp',
                     '{user} u ON hp.userid = u.id' );
       $where = array('u.deleted = 0');
       $searchable = array('u.id');
       $this->define_sql($fields, $from, $where);
   }

   /**
    * The following methods reformat boolean values to "Yes" / "No".
    */

   protected function col_has_fever($values) {
       return isset($values->has_fever) ? format_boolean($values->has_fever) : '';
   }

   protected function col_has_sore_throat($values) {
       return isset($values->has_sore_throat) ? format_boolean($values->has_sore_throat) : '';
   }

   protected function col_has_cough($values) {
       return isset($values->has_cough) ? format_boolean($values->has_cough) : '';
   }

   protected function col_has_runny_nose($values) {
       return isset($values->has_runny_nose) ? format_boolean($values->has_runny_nose) : '';
   }

   protected function col_has_muscle_aches($values) {
       return isset($values->has_muscle_aches) ? format_boolean($values->has_muscle_aches) : '';
   }

   protected function col_has_loss_of_sense($values) {
       return isset($values->has_loss_of_sense) ? format_boolean($values->has_loss_of_sense) : '';
   }

   protected function col_has_short_breath($values) {
       return isset($values->has_short_breath) ? format_boolean($values->has_short_breath) : '';
   }

}
