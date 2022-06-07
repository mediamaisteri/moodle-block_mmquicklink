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
 * Confirmation box for restoring course from archive.
 *
 * @package    block_mmquicklink
 * @copyright  2022 Mediamaisteri Oy
 * @author     Rosa Siuruainen <rosa.siuruainen@mediamaisteri.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_login();

$PAGE->set_context(context_system::instance());
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_url(new moodle_url('/blocks/mmquicklink/confirm_restore.php'));
$PAGE->set_pagelayout('standard');

$id = required_param("id", PARAM_INT);
$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
$coursecontext = context_course::instance($course->id);

echo $OUTPUT->header();

if (!has_capability('moodle/course:update', context_course::instance($course->id))) {
    throw new moodle_exception('noaccess', 'local_reports');
}

$coursearchiveconf = get_config('local_course_archive');
$isarchived = false;
$archcat = $coursearchiveconf->archivecategory;
$delcat = $coursearchiveconf->deletecategory;

if (!empty($coursearchiveconf->plugin_enabled)) {
    // Check that course is in archive or delete category.
    if ($course->category == $archcat || $course->category == $delcat) {
        $message = get_string('restorecourse_confirm', 'block_mmquicklink') . ": " . format_string($course->fullname) . "?";
        // Get original category from db.
        $originalcategory = $DB->get_record('local_course_archive', ['courseid' => $id], 'categoryid');
        // Get category name.
        $categoryname = $DB->get_record('course_categories', ['id' => $originalcategory->categoryid], 'name');
        $restorecat = null;

        // If original category is not known we will use the restore category.
        if (empty($originalcategory->categoryid)) {
            // First we need to check that the restore category is set, if not, throw exception.
            if (empty($coursearchiveconf->restorecategory)) {
                throw new moodle_exception('norestorecategory', 'block_mmquicklink');
            } else {
                // Let the user know which category the course will be moved to (restore category).
                $message .= ' ' . get_string('restored_restorecat', 'block_mmquicklink') .
                " " . $coursearchiveconf->restorecategory .".";
                $restorecat = $coursearchiveconf->restorecategory;
            }
        } else {
            // Let the user know which category the course will be moved to (original category).
            $message .= ' ' . get_string('restored_originalcat', 'block_mmquicklink') . $categoryname->name . ".";
            $restorecat = $originalcategory->categoryid;
        }

        $continueurl = new moodle_url($CFG->wwwroot . "/blocks/mmquicklink/restore.php",
        array("courseid" => $course->id, "restorecat" => $restorecat));
        $continuebutton = new single_button($continueurl, get_string('restorecourse', 'block_mmquicklink'));
        $cancelurl = new moodle_url($CFG->wwwroot . "/course/view.php?id=" . $id);

        echo $OUTPUT->confirm($message, $continuebutton, $cancelurl);
    } else {
        throw new moodle_exception('restored', 'block_mmquicklink');
    }
}

echo $OUTPUT->footer();
