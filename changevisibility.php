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
 * @package   block_mmquicklink
 * @copyright 2019 Mediamaisteri Oy
 * @author    Mikko Haikonen <mikko.haikonen@mediamaisteri.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/my/lib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->libdir.'/datalib.php');
require_once($CFG->dirroot.'/course/format/lib.php');

global $DB, $USER, $COURSE;
$courseid = optional_param('id', '', PARAM_INT);
$course = $DB->get_record("course", array("id" => $courseid));

$PAGE->set_context(context_system::instance());
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_url(new moodle_url('/blocks/mmquicklink/changevisibility.php'));
$PAGE->set_pagelayout('standard');

require_login();

$hide = required_param('hide', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);

// Check if user has permission to edit visibility in current course context.
if (has_capability('moodle/course:visibility', context_course::instance($courseid))) {

    if ($confirm == 0) {

        echo $OUTPUT->header();

        $message = get_string('areyousurehide' . $hide, 'block_mmquicklink') . ": " . format_string($course->fullname) . "?";
        $continueurl = new moodle_url($CFG->wwwroot . "/blocks/mmquicklink/changevisibility.php",
        array("id" => $course->id, "confirm" => 1, "hide" => $hide));
        $continuebutton = new single_button($continueurl, get_string('hide' . $hide, 'block_mmquicklink'));
        $cancelurl = new moodle_url($CFG->wwwroot . "/course/view.php", array("id" => $course->id));
        echo $OUTPUT->confirm($message, $continuebutton, $cancelurl);

        echo $OUTPUT->footer();

    }

    if ($hide == 1 && $confirm == 1) {
        // Update DB to hide course.
        $DB->set_field('course', 'visible', '0', array('id' => $course->id));

        // Trigger a course updated event.
        $event = \core\event\course_updated::create(array(
            'objectid' => $COURSE->id,
            'context' => context_course::instance($course->id),
            'other' => array('shortname' => $course->shortname,
                             'fullname' => $course->fullname,
                             'updatedfields' => array('category' => $course->category))
        ));
        $event->trigger();

        // Redirect user back to course page with proper string.
        $urltogo = new moodle_url($CFG->wwwroot . "/course/view.php", array("id" => $courseid));

        redirect("$urltogo", get_string('course') . " " . strtolower(get_string('hidden', 'core_grades')) . ".", 5);
    }

    if ($hide == 0 && $confirm == 1) {
        // Update DB to show course..
        $DB->set_field('course', 'visible', '1', array('id' => $courseid));

        // Trigger a course updated event.
        $event = \core\event\course_updated::create(array(
            'objectid' => $COURSE->id,
            'context' => context_course::instance($course->id),
            'other' => array('shortname' => $course->shortname,
                             'fullname' => $course->fullname,
                             'updatedfields' => array('category' => $course->category))
        ));
        $event->trigger();

        // Redirect user back to course page with proper string.
        $urltogo = new moodle_url($CFG->wwwroot . "/course/view.php", array("id" => $courseid));
        redirect("$urltogo", get_string('course') . " " . strtolower(get_string('shown', 'core_calendar')) . ".", 5);
    }


} else {

    // Redirect user back to course page with an error message.
    $urltogo = $_SERVER['HTTP_REFERER'];
    redirect("$urltogo", get_string('error') . "(" . get_string('manageroles') . ")", 5);

}
