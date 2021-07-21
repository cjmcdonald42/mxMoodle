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
 * Healthtest Audit Report
 *
 * @package     local_mxschool_healthtest
 * @author      mxMoodle Development Team
 * @copyright   2021 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 namespace local_mxschool\local\healthtest;

 defined('MOODLE_INTERNAL') || die();

 class audit_table extends \local_mxschool\table {

   /**
    * Creates a new table.
    *
    * @param stdClass $filter Any filtering for the table.
    * @param string download, indicates if the table is downloading
    */
   public function __construct($filter, $download) {
	  $this->is_downloading($download);
 	  // Define the names of the columns. Should match up with the $fields array.
       $columns = array('name', 'testing_cycle');
 	  // Get headers from language file
       $headers = $this->generate_headers($columns, 'healthtest:audit_report');
 	  // Define sortable columns
       $sortable = array('name', 'testing_cycle');
 	  // All columns are centered
       $centered = array('name', 'testing_cycle');
       parent::__construct('healthtest_audit_table', $columns, $headers, $sortable, $centered, $filter, false);


 	  // The fields to query from the database
       $fields = array('av.id AS avid' , 'av.userid', 'u.lastname', 'u.firstname', 'u.alternatename', 'u.lastname AS name', 'tb.testing_cycle AS testing_cycle', 'tb.id AS tbid', 'tb.start_time', 'ht.attended AS attended',
					'tb.end_time', 'tb.date AS tbdate');
 	  // The tables which to query
       $from = array('{local_mxschool_audit} av', '{user} u ON u.id = av.userid', '{local_mxschool_healthtest} ht ON u.id = ht.userid', '{local_mxschool_testing_block} tb ON tb.id = ht.testing_block_id',
   );

 	  // Get everything unless there are filters
 	  $where = array('u.deleted = 0'); // ask about what $where array is takin in


/*
$users = ($fields->userid)
foreach($users as $user)
{
    $appt_info=get_all_user_appointment_info($user);
    if($appt_info['attended']==0)
    {
        $testing_cycle=$appt_info['testing_cycle'];
        return (", "+$testing_cycle);
    }
}
*/


	  if($filter->testing_cycle) {
		  $where[] = "tb.testing_cycle = '{$filter->testing_cycle}'";
	  }

        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename');
        $this->define_sql($fields, $from, $where, $searchable, $filter->search);
    }

	// The following functions edit what is displayed in individual columns


	protected function col_name($values) {
		if($values->alternatename) return "{$values->lastname}, {$values->firstname} ({$values->alternatename})";
		return "{$values->lastname}, {$values->firstname}";
	}

    protected function col_testing_cycle($values) {
        $user=($values->userid);
        $user_app_info=get_all_user_appointment_info($user);
        $output = "";
        foreach($user_app_info as $app)
        {
            if($app['attended']==1)
            {
                if(empty($output))
                {
                    $output.= $app['testing_cycle'];
                }
                else {
                    $output .= ", ";
                    $output.= $app['testing_cycle'];
                }
            }
            else {
                $today = date('Y-m-d');
                if($app['date']>$today))
                {
                    if(empty($output))
                    {
                        $output .= "(";
                        $output.= $app['testing_cycle'];
                        $output .= ")";
                    }
                    else {
                        $output .= ", (";
                        $output.= $app['testing_cycle'];
                        $output .= ")";
                    }
                }
                elseif ($app['date']==$today) {
                    $nowtime=time();
                    if($app['end_time']>$nowtime)
                    {
                        if(empty($output))
                        {
                            $output .= "(";
                            $output.= $app['testing_cycle'];
                            $output .= ")";
                        }
                        else {
                            $output .= ", (";
                            $output.= $app['testing_cycle'];
                            $output .= ")";
                        }
                    }
                }
            }
        }
        return $output;
	}
}
