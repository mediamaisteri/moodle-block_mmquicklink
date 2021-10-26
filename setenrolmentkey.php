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

// Course id & key from url variable.
$courseid = optional_param('courseid', '', PARAM_TEXT);
$enrolmentkey = optional_param('enrolmentkey', '', PARAM_TEXT);
$urltogo = new moodle_url(get_local_referer(), array("id" => $courseid));
$uniquekey = get_config('mmquicklink', 'config_unique_enrolmentkey');


// Check if user has permission to edit course enrolment methods.
if (has_capability('moodle/course:enrolconfig', context_course::instance($courseid))) {

    // Update field to either set or disable enrolment key.
    require_once($CFG->dirroot . "/blocks/mmquicklink/classes/block_mmquicklink.php");
    if ($enrolmentkey) {
        if ($uniquekey === '1') {
            if (!$DB->record_exists_sql('SELECT * FROM {enrol} WHERE enrol = ? AND password = ? AND courseid != ?', array('self', $enrolmentkey, $courseid))) {
                $setkey = \block_mmquicklink\mmquicklink::set_enrolmentkey($courseid, $enrolmentkey);
                // Redirect user back to course page with proper string.
                redirect($urltogo, get_string('password', 'enrol_self') . " " . strtolower(get_string('saved', 'core_completion')), 5);
            } else {
                // Redirect user back to course page with errormessage.
                redirect($urltogo, get_string('enrolmentkey_reserved', 'block_mmquicklink'), 5, NOTIFY_ERROR);
            }
        } else {
            $setkey = \block_mmquicklink\mmquicklink::set_enrolmentkey($courseid, $enrolmentkey);
            // Redirect user back to course page with proper string.
            redirect($urltogo, get_string('password', 'enrol_self') . " " . strtolower(get_string('saved', 'core_completion')), 5);
        }
    } else {
        $disablekey = \block_mmquicklink\mmquicklink::set_enrolmentkey($courseid);
        // Redirect user back to course page with proper string.
        redirect($urltogo, get_string('password', 'enrol_self') . " " . strtolower(get_string('deleted', 'core')), 5);
    }

} else {

    // Redirect user back to course page with an error message.
    redirect($urltogo, get_string('error') . " (" . get_string('manageroles') . ")", 5);

}
