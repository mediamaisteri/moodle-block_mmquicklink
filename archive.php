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
 * Move selected course to 'archive' category.
 *
 * @package   block_mmquicklink
 * @copyright 2019 Mediamaisteri Oy
 * @author    Mikko Haikonen <mikko.haikonen@mediamaisteri.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/my/lib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->libdir.'/filelib.php');

require_login();

// Course id & key from url variable.
$courseid = required_param('courseid', PARAM_INT);
$categoryid = required_param('categoryid', PARAM_INT);

// Check if user has the capability to update the course.
if (has_capability('moodle/course:update', context_course::instance($courseid))) {
    $coursearchiveconf = get_config('local_course_archive');
    $archcat = $coursearchiveconf->archivecategory;
    $time = new DateTime("now");
    $timestamp = $time->getTimestamp();

    // Move course to the archive category.
    move_courses((array) $courseid, $archcat);

    // Trigger event.
    $event = \block_mmquicklink\event\course_archived::create(array(
        'objectid' => $courseid,
        'userid' => $USER->id,
        'context' => context_course::instance($courseid),
    ));
    $event->trigger();

    // Define data object for the database query.
    $data = new stdClass();
    $data->courseid = $courseid;
    $data->categoryid = $categoryid;
    $data->timemodified = $timestamp;

    // Insert the record.
    $add = $DB->insert_record('local_course_archive', $data);

    // Redirect user back to the course.
    redirect($CFG->wwwroot . "/course/view.php?id=$courseid", get_string('archived', 'block_mmquicklink'), null, 'success');
} else {
    // If user doesn't have required capabilities, show a general error.
    print_error('noaccess');
}
