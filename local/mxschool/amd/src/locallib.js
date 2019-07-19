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
 * Basic reusable library functions for Middlesex's Dorm and Student Functions Plugin.
 *
 * @module     local_mxschool/locallib
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {
    return {
        updateSelect: function(selector, list, prepend) {
            var selected = selector.val();
            var selectedText = selector.children('option[value=' + selected + ']').text();
            selector.empty();
            $.each(list, function(index, option) {
                selector.append($('<option></option>').attr('value', option.value).text(option.text));
            });
            if (selector.children('option[value=' + selected + ']').length) {
                selector.val(selected);
            } else if (prepend) {
                selector.prepend($('<option></option>').attr('value', selected).text(selectedText));
                selector.val(selected);
            } else {
                selector.change();
            }
        },
        updateMultiSelect: function(selector, list) {
            var selected = selector.val();
            selector.empty();
            $.each(list, function(index, option) {
                selector.append($('<option></option>').attr('value', option.value).text(option.text));
            });
            var reselected = [];
            $.each(selected, function(index, option) {
                if (selector.children('option[value=' + option + ']').length) {
                    reselected.push(option);
                } else {
                    selector.next().children('span[role="listitem"][data-value=' + option + ']').remove();
                }
            });
            if (reselected.length) {
                selector.val(reselected);
            }
            if (selected.toString() !== reselected.toString()) {
                selector.change();
            }
        },
        updateRadio: function(selector, list) {
            selector.each(function() {
                var input = $(this);
                var spanContents = input.parent().parent().contents();
                if (list.includes(input.val())) {
                    input.parent().show();
                    spanContents.eq(spanContents.index(input.parent()) + 3).get(0).nodeValue = '\u00A0';
                } else {
                    if (input.prop('checked')) {
                        input.prop('checked', false);
                        input.change();
                    }
                    input.parent().hide();
                    spanContents.eq(spanContents.index(input.parent()) + 3).get(0).nodeValue = '';
                }
            });
        }
    };
});
