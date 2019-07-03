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
 * eOppiva customized settings.
 *
 * @package    theme_maisteriboost
 * @copyright  2019 Mediamaisteri Oy
 * @author     Mikko Haikonen <mikko.haikonen@mediamaisteri.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_login();

global $SITE, $DB;
$PAGE->set_context(context_system::instance());
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_url(new moodle_url('/blocks/mmquicklink/confirm.php'));
$PAGE->set_pagelayout('standard');

$id = required_param("id", PARAM_INT);
$confirm = optional_param("confirm", null, PARAM_INT);
$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
$coursecontext = context_course::instance($course->id);

echo $OUTPUT->header();
if ($SITE->id == $course->id) {
    print_error('cannotdeletecourse');
}

if (!has_capability('moodle/course:update', context_course::instance($course->id))) {
    print_error('noaccess', 'local_reports');
}

$courseshortname = format_string($course->shortname, true, array('context' => $coursecontext));
$coursefullname = format_string($course->fullname, true, array('context' => $coursecontext));

// Archive course button.
$coursearchiveconf = get_config('local_course_archive');
if (!empty($coursearchiveconf->plugin_enabled)) {
    $archcat = $coursearchiveconf->archivecategory;
    $delcat = $coursearchiveconf->deletecategory;
    if ($course->category != $archcat && $course->category != $delcat) {
        $message = get_string('areyousure', 'block_mmquicklink') . ": " . format_string($course->fullname) . "?";

        $continueurl = new moodle_url($CFG->wwwroot . "/blocks/mmquicklink/archive.php",
        array("courseid" => $course->id, "categoryid" => $course->category));
        $continuebutton = new single_button($continueurl, get_string('archive_course', 'block_mmquicklink'));

        $cancelurl = new moodle_url($CFG->wwwroot . "/course/view.php?id=" . $id);

        echo $OUTPUT->confirm($message, $continuebutton, $cancelurl);
    } else {
        print_error('alreadyarchived', 'block_mmquicklink');
    }
}

echo $OUTPUT->footer();
