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
 * Alternating button JS
 *
 * @module      local_mxschool/alternating_button
 * @package     local_mxschool
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/str', 'core/notification'], function($, ajax, str, notification) {
    function update_permission(event) {
		var button = $(event.target);
		var id = button.attr('value');
		var name = button.attr('name');
		var text = $('.mx-alternating-text'+name+id);
		var userid = text.attr('name');
		var package_name = text.attr('value');
		var strings = get_strings();
		if(text.text() == strings[package_name+"_alternating_text_"+name+"_0_text"]) {
			text.text(strings[package_name+"_alternating_text_"+name+"_1_text"]);
 		     text.css('color', strings[package_name+"_alternating_text_"+name+"_1_color"]);
 		     button.text(strings[package_name+"_alternating_button_"+name+"_1_text"]);
 		     button.css('background-color', strings[package_name+"_alternating_button_"+name+"_1_color"]);
     	     button.css('border-color', strings[package_name+"_alternating_button_"+name+"_1_color"]);
			update_deans_permission_color(id, package_name);
		}
		else if(text.text() == strings[package_name+"_alternating_text_"+name+"_1_text"]) {
		     text.text(strings[package_name+"_alternating_text_"+name+"_2_text"]);
 		     text.css('color', strings[package_name+"_alternating_text_"+name+"_2_color"]);
 		     button.text(strings[package_name+"_alternating_button_"+name+"_2_text"]);
 		     button.css('background-color', strings[package_name+"_alternating_button_"+name+"_2_color"]);
     		button.css('border-color', strings[package_name+"_alternating_button_"+name+"_2_color"]);
			update_deans_permission_color(id, package_name);
		}
		else if(text.text() == strings[package_name+"_alternating_text_"+name+"_2_text"]) {
			text.text(strings[package_name+"_alternating_text_"+name+"_0_text"]);
 		     text.css('color', strings[package_name+"_alternating_text_"+name+"_0_color"]);
 		     button.text(strings[package_name+"_alternating_button_"+name+"_0_text"]);
 		     button.css('background-color', strings[package_name+"_alternating_button_"+name+"_0_color"]);
     		button.css('border-color', strings[package_name+"_alternating_button_"+name+"_0_color"]);
			update_deans_permission_color(id, package_name);
		}
		else {
			throw "Unrecognized permission text: "+text.text();
		}
		// CALL METHOD HERE
    }
    // Returns true if all permissions are checked, false otherwise.
    function deans_permission_check_approvals(id) {
	    var strings = get_strings();
	    if(
		    $('.mx-alternating-textsports'+id).text() == strings["deans_permission_alternating_text_sports_1_text"] ||
		    $('.mx-alternating-textstudyhours'+id).text() == strings["deans_permission_alternating_text_studyhours_1_text"] ||
		    $('.mx-alternating-textclass'+id).text() == strings["deans_permission_alternating_text_class_1_text"]
	    ) {
		    return false;
	    }
	    return true;
    }
    function update_deans_permission_color(id, package_name) {
	    var strings = get_strings();
	    if(package_name == 'deans_permission') {
		    if(deans_permission_check_approvals(id) && $('.mx-alternating-buttondeans'+id).text() == strings['deans_permission_alternating_button_deans_1_text']) {
			    $('.mx-alternating-buttondeans'+id).css('background-color', strings['deans_permission_alternating_button_deans_1_color_ready']);
			    $('.mx-alternating-buttondeans'+id).css('border-color', strings['deans_permission_alternating_button_deans_1_color_ready']);
		    }
		    else if($('.mx-alternating-buttondeans'+id).text() == strings['deans_permission_alternating_button_deans_1_text']) {
			    $('.mx-alternating-buttondeans'+id).css('background-color', strings['deans_permission_alternating_button_deans_1_color_waiting']);
			    $('.mx-alternating-buttondeans'+id).css('border-color', strings['deans_permission_alternating_button_deans_1_color_waiting']);
		    }
	    }
    }
    return function(id, userid, name, current_status, package_name) {
	    var button = $('.mx-alternating-button'+name+id);
	    var text = $('.mx-alternating-text'+name+id);
	    var strings = get_strings();
	    if(current_status == 0) {
		    text.text(strings[package_name+"_alternating_text_"+name+"_0_text"]);
		    text.css('color', strings[package_name+"_alternating_text_"+name+"_0_color"]);
		    button.text(strings[package_name+"_alternating_button_"+name+"_0_text"]);
		    button.css('background-color', strings[package_name+"_alternating_button_"+name+"_0_color"]);
    		    button.css('border-color', strings[package_name+"_alternating_button_"+name+"_0_color"]);
		    update_deans_permission_color(id, package_name);
	    }
	    else if(current_status == 1) {
		    text.text(strings[package_name+"_alternating_text_"+name+"_1_text"]);
		    text.css('color', strings[package_name+"_alternating_text_"+name+"_1_color"]);
		    button.text(strings[package_name+"_alternating_button_"+name+"_1_text"]);
		    button.css('background-color', strings[package_name+"_alternating_button_"+name+"_1_color"]);
    		    button.css('border-color', strings[package_name+"_alternating_button_"+name+"_1_color"]);
		    update_deans_permission_color(id, package_name);
	    }
	    else if(current_status == 2) {
		    text.text(strings[package_name+"_alternating_text_"+name+"_2_text"]);
		    text.css('color', strings[package_name+"_alternating_text_"+name+"_2_color"]);
		    button.text(strings[package_name+"_alternating_button_"+name+"_2_text"]);
		    button.css('background-color', strings[package_name+"_alternating_button_"+name+"_2_color"]);
    		    button.css('border-color', strings[package_name+"_alternating_button_"+name+"_2_color"]);
		    update_deans_permission_color(id, package_name);
	    }
	    else {
		    throw "Unrecognized permission value in database: "+current_status+" for "+name;
	    }
	    button.click(update_permission);
    };
    // Add to this if using new alternating button
    // Defined as {package_name}_alternating_{text or button}_{name}_{a value 0-2}_{text or color}
    function get_strings() {
	var strings = {
	     deans_permission_alternating_text_sports_0_text: '',
	     deans_permission_alternating_text_sports_0_color: 'white',
	     deans_permission_alternating_button_sports_0_text: 'Request Review',
	     deans_permission_alternating_button_sports_0_color: 'steelblue',
	     deans_permission_alternating_text_sports_1_text: 'Under Review',
	     deans_permission_alternating_text_sports_1_color: 'dimgray',
	     deans_permission_alternating_button_sports_1_text: 'Approve',
	     deans_permission_alternating_button_sports_1_color: 'mediumseagreen',
	     deans_permission_alternating_text_sports_2_text: 'Approved',
	     deans_permission_alternating_text_sports_2_color: 'mediumseagreen',
	     deans_permission_alternating_button_sports_2_text: 'Undo',
	     deans_permission_alternating_button_sports_2_color: 'dimgray',

	     deans_permission_alternating_text_studyhours_0_text: '',
	     deans_permission_alternating_text_studyhours_0_color: 'white',
	     deans_permission_alternating_button_studyhours_0_text: 'Request Review',
	     deans_permission_alternating_button_studyhours_0_color: 'steelblue',
	     deans_permission_alternating_text_studyhours_1_text: 'Under Review',
	     deans_permission_alternating_text_studyhours_1_color: 'dimgray',
	     deans_permission_alternating_button_studyhours_1_text: 'Approve',
	     deans_permission_alternating_button_studyhours_1_color: 'mediumseagreen',
	     deans_permission_alternating_text_studyhours_2_text: 'Approved',
	     deans_permission_alternating_text_studyhours_2_color: 'mediumseagreen',
	     deans_permission_alternating_button_studyhours_2_text: 'Undo',
	     deans_permission_alternating_button_studyhours_2_color: 'dimgray',

	     deans_permission_alternating_text_class_0_text: '',
	     deans_permission_alternating_text_class_0_color: 'white',
	     deans_permission_alternating_button_class_0_text: 'Request Review',
	     deans_permission_alternating_button_class_0_color: 'steelblue',
	     deans_permission_alternating_text_class_1_text: 'Under Review',
	     deans_permission_alternating_text_class_1_color: 'dimgray',
	     deans_permission_alternating_button_class_1_text: 'Approve',
	     deans_permission_alternating_button_class_1_color: 'mediumseagreen',
	     deans_permission_alternating_text_class_2_text: 'Approved',
	     deans_permission_alternating_text_class_2_color: 'mediumseagreen',
	     deans_permission_alternating_button_class_2_text: 'Undo',
	     deans_permission_alternating_button_class_2_color: 'dimgray',

	     deans_permission_alternating_text_deans_0_text: '',
	     deans_permission_alternating_text_deans_0_color: 'white',
	     deans_permission_alternating_button_deans_0_text: 'Review Requests',
	     deans_permission_alternating_button_deans_0_color: 'steelblue',
	     deans_permission_alternating_text_deans_1_text: 'Awaiting Approvals',
	     deans_permission_alternating_text_deans_1_color: 'dimgray',
	     deans_permission_alternating_button_deans_1_text: 'Approve',
	     deans_permission_alternating_button_deans_1_color_waiting: 'indianred',
	     deans_permission_alternating_button_deans_1_color_ready: 'mediumseagreen',
	     deans_permission_alternating_text_deans_2_text: 'Form Approved',
	     deans_permission_alternating_text_deans_2_color: 'mediumseagreen',
	     deans_permission_alternating_button_deans_2_text: 'Undo',
	     deans_permission_alternating_button_deans_2_color: 'dimgray'
	};
	return strings;
    }
});
