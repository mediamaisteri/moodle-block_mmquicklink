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
 * Move course to archive category.
 *
 * @package    block_mmquicklink
 * @author     Mikko Haikonen <mikko.haikonen@mediamaisteri.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/my/lib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->libdir.'/filelib.php');

require_login();

global $DB, $USER, $COURSE;

// Course id & key from url variable.
$courseid = optional_param('courseid', '', PARAM_INT);
$categoryid = optional_param('categoryid', '', PARAM_INT);
$urltogo = $_SERVER['HTTP_REFERER'];

// Check if user has permission to edit course enrolment methods.
if (has_capability('moodle/course:delete', context_course::instance($courseid))) {
    $time = new DateTime("now");
    $timestamp = $time->getTimestamp();
    $move = $DB->execute("UPDATE {course} SET category=$categoryid WHERE id=$courseid LIMIT 1");
    $add = $DB->execute("INSERT INTO {local_course_archive} VALUES(null, $courseid, $categoryid, $timestamp)");
    redirect($urltogo, get_string('archived', 'block_mmquicklink'), null, 'success');
} else {
    print_error('noaccess');
}