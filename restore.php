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
 * Restore selected course to its original or restore category.
 *
 * @package   block_mmquicklink
 * @copyright 2022 Mediamaisteri Oy
 * @author    Rosa Siuruainen <rosa.siuruainen@mediamaisteri.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/my/lib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->libdir.'/filelib.php');

require_login();

// Course id and restore category id from url params.
$courseid = required_param('courseid', PARAM_INT);
$restorecat = required_param('restorecat', PARAM_INT);

// Check if user has the capability to update the course.
if (has_capability('moodle/course:update', context_course::instance($courseid))) {

    // Move course to the restore category.
    move_courses((array) $courseid, $restorecat);

    // Delete from course_archive database.
    $DB->delete_records('local_course_archive', ['courseid' => $courseid]);

    // Trigger event.
    $event = \block_mmquicklink\event\course_restored::create(array(
        'objectid' => $courseid,
        'userid' => $USER->id,
        'context' => context_course::instance($courseid),
    ));
    $event->trigger();

    // Redirect user back to the course.
    redirect($CFG->wwwroot . "/course/view.php?id=$courseid", get_string('restored', 'block_mmquicklink'), null, 'success');
} else {
    // If user doesn't have required capabilities, show a general error.
    throw new moodle_exception('noaccess');
}
