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
 * Javascript for fetching navigation items from an integrated Drupal site.
 *
 * @package   theme_maistericlean
 * @copyright 2017 Mediamaisteri Oy, https://www.mediamaisteri.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module theme_maistericlean/sharednavigation
 */
define(['jquery'], function($) {
    return {
        init: function(){
            $(document).on('click', '.mmquicklink-enrolmentkey', function(a) {
                a.preventDefault();
                $(".mmquicklink-enrolmentkey-div").toggle();
            });

            $(document).on('click', '.mmquicklink-reports-button', function(a) {
                a.preventDefault();
                $(".mmquicklink-reports").toggle();
            });

            $(".mmquicklink-reports > li").addClass("mb-1");

        }
    }
});