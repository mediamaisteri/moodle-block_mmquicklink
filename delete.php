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
 * Delete a course.
 *
 * @package    theme_maisteriboost
 * @copyright  2020 Mediamaisteri Oy
 * @author     Mikko Haikonen <mikko.haikonen@mediamaisteri.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Require needed classes and configuration.
require_once("../../config.php");
require_once($CFG->dirroot . '/course/lib.php');

// Course ID as parameter.
$id = required_param('id', PARAM_INT);

// Course object from database.
$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

// Course context.
$coursecontext = context_course::instance($course->id);

// Require login.
require_login();

// Can not delete frontpage or don't have permission to delete the course.
if ($SITE->id == $course->id || !can_delete_course($id)) {
    print_error('cannotdeletecourse');
}

// Raise PHP time limit to prevent problems.
core_php_time_limit::raise();

// Do the actual deletion.
delete_course($course);

// Course sortorder table has to be reordered to prevent errors.
fix_course_sortorder();

// Exit.
exit;