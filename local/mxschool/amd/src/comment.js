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
 * Edits a comment in the Healthpass report table.
 *
 * @module      local_mxschool/comment
 * @package     local_mxschool
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification'], function($, ajax, notification) {
    function edit_comment(event) {
	   var userid = $(event.target).attr('name');
	   $('.mx-comment-text'+userid).hide();
	   $('.mx-comment-edit-button'+userid).hide();
	   $('.mx-comment-edit-area'+userid).val($('.mx-comment-text'+userid).text());
	   $('.mx-comment-edit-area'+userid).show();
	   $('.mx-comment-save-button'+userid).show();
    }
    function update_comment() {
	    var userid = $(event.target).attr('name');
	    var new_comment = $('.mx-comment-edit-area'+userid).val();
	    var table = $('.mx-comment-edit-area'+userid).attr('name');
	    var promises = ajax.call([{
		    methodname: 'local_mxschool_update_comment',
		    args: {
			    id: userid,
			    text: new_comment,
			    table: table
		    }
	    }]);
	    promises[0].done().fail(notification.exception);
	    $('.mx-comment-text'+userid).text(new_comment);
	    $('.mx-comment-text'+userid).show();
	    $('.mx-comment-edit-button'+userid).show();
	    $('.mx-comment-edit-area'+userid).hide();
	    $('.mx-comment-save-button'+userid).hide();
    }
    return function(userid, original_comment_text) {
        var edit_button = $('.mx-comment-edit-button'+userid);
	   var comment_edit_area = $('.mx-comment-edit-area'+userid);
	   var save_button = $('.mx-comment-save-button'+userid);
	   var comment_text = $('.mx-comment-text'+userid);
	   comment_text.show();
	   comment_edit_area.hide();
	   save_button.hide();
        edit_button.click(edit_comment);
	   save_button.click(update_comment);
    };
});
