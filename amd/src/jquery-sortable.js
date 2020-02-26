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
 * Quicklink items sortable.
 *
 * @package   block_mmquicklink
 * @copyright 2020 Mediamaisteri Oy
 * @author    Mikko Haikonen <mikko.haikonen@mediamaisteri.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'jqueryui'], function($, jqui) {
    return {
        init: function() {
            if ($("#quicklink-sort").length) {
                console.log("jQuery Sortable init");
                $("#quicklink-sort").sortable({
                    update: function(e, ui) {
                        var orderid = 1;
                        var len = $("#quicklink-sort li").length;
                        $("#quicklink-sort li").each(function() {
                            var $this = $(this);
                            $("#quicklink-sort").css({display: "block"});
                            var button = $(this).attr("data-button");
                            $.get("../blocks/mmquicklink/setorder.php", { button: button, order: orderid } ).done(function(data) {
                                if ($this[0] == $("#quicklink-sort li").last()[0]) {
                                    $("#quicklink-sort-alert-saving").attr("style", "display: none !important;");
                                    $("#quicklink-sort-alert").attr("style", "display: block !important;");
                                } else {
                                    $("#quicklink-sort-alert").attr("style", "display: none !important");
                                    $("#quicklink-sort-alert-saving").attr("style", "display: block !important");
                                }
                            });
                            orderid = orderid + 1;
                        });
                    }
                });
            }
        }
    }
});