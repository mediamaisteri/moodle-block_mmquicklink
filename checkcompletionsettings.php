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
 * Check completion settings.
 *
 * @package   block_mmquicklink
 * @copyright 2023 Mediamaisteri Oy
 * @author    Rosa Siuruainen <rosa.siuruainen@mediamaisteri.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');

require_login();

// Course id & key from url variable.
$courseid = required_param('courseid', PARAM_INT);
$action = required_param('action', PARAM_RAW);

// Check if user has the capability to update the course.
if (has_capability('moodle/course:update', context_course::instance($courseid))) {
    global $DB;
    if ($action == "showcourse") {
        $course  = get_course($courseid);
        // Check if course completion is enabled.
        if (!$course->enablecompletion) {
            echo true;
        } else {
            // Check if course has completion criteria set correctly.
            $sql = "SELECT cm.id, ccc.criteriatype
            FROM {course_modules} cm
            JOIN {course_completion_criteria} ccc ON cm.id = ccc.moduleinstance
            WHERE cm.completion > 0 AND cm.course = ?";

            $completionok = $DB->get_records_sql($sql, [$courseid]);

            $result = ($completionok) ? true : false;

            echo $result;
        }
    } else {
        echo true;
    }
}
