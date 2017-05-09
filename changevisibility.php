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
 * Change course visibility.
 *
 *
 * @package    block_mmquicklink
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/my/lib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->libdir.'/filelib.php');

global $DB, $USER, $COURSE;
$courseid = strip_tags($_GET["id"]);

// Check if user has permission to edit visibility.
if (has_capability('moodle/course:visibility', context_system::instance())) {

    if (strip_tags($_GET["hide"]) == 1) {
        // Update DB to hide course.
        $DB->set_field('course', 'visible', '0', array('id' => $courseid));

        // Redirect user back to course page with proper string.
        $urltogo = $_SERVER['HTTP_REFERER'];
        redirect("$urltogo", get_string('course') . " " . strtolower(get_string('hidden', 'core_grades')), 5);
    }

    if (strip_tags($_GET["hide"]) == 0) {
        // Update DB to show course..
        $DB->set_field('course', 'visible', '1', array('id' => $courseid));

        // Redirect user back to course page with proper string.
        $urltogo = $_SERVER['HTTP_REFERER'];
        redirect("$urltogo", get_string('course') . " " . strtolower(get_string('shown', 'core_calendar')), 5);
    }


} else {

    // Redirect user back to course page with an error message.
    $urltogo = $_SERVER['HTTP_REFERER'];
    redirect("$urltogo", get_string('error') . "(" . get_string('manageroles') . ")", 5);

}
