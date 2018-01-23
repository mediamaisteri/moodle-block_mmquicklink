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
 * Functionality to enable enrolment
 * with key a.k.a. "setenrolmentkey".
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

require_login();

global $DB, $USER, $COURSE;

// Course id & key from url variable.
$courseid = optional_param('courseid', '', PARAM_TEXT);
$enrolmentkey = optional_param('enrolmentkey', '', PARAM_TEXT);
$urltogo = $_SERVER['HTTP_REFERER'];

// Check if user has permission to edit course enrolment methods.
if (has_capability('moodle/course:enrolconfig', context_course::instance($courseid))) {

    // Check how many self-enrolment instances are in use in the course.
    $self = $DB->get_records('enrol', array('courseid' => $courseid, 'enrol' => 'self', 'status' => 0), 'password');
    if (count($self) > 1) {
        redirect($urltogo, get_string('toomanyselfenrolments', 'block_mmquicklink') . "<a href='" . $CFG->wwwroot . "/enrol/instances.php?id=" . $courseid . "'>" . $CFG->wwwroot . "/enrol/instances.php?id=" . $courseid . "</a>.", null, 'error');
    }

    // Update field to either set or disable enrolment key.
    if ($enrolmentkey) {
        // DB queries to set enrolment key.
        $DB->set_field('enrol', 'status', '0', array('courseid' => $courseid, 'enrol' => 'self'));
        $DB->set_field('enrol', 'password', $enrolmentkey, array('courseid' => $courseid, 'enrol' => 'self'));

        // Redirect user back to course page with proper string.
        redirect("$urltogo", get_string('password', 'enrol_self') . " " . strtolower(get_string('saved', 'core_completion')), 5);

    } else {
        // DB queries to disable enrolment key.
        $DB->set_field('enrol', 'status', '1', array('courseid' => $courseid, 'enrol' => 'self'));
        $DB->set_field('enrol', 'password', '', array('courseid' => $courseid, 'enrol' => 'self'));

        // Redirect user back to course page with proper string.
        redirect("$urltogo", get_string('password', 'enrol_self') . " " . strtolower(get_string('deleted', 'core')), 5);

    }

} else {

    // Redirect user back to course page with an error message.
    $urltogo = $_SERVER['HTTP_REFERER'];
    redirect("$urltogo", get_string('error') . " (" . get_string('manageroles') . ")", 5);

}
