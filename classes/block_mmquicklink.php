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
 * Quick Link -block to help admins, managers
 * and teachers to navigate through Moodle.
 *
 * @package   block_mmquicklink
 * @copyright 2017-2019 Mediamaisteri Oy
 * @author    Mikko Haikonen <mikko.haikonen@mediamaisteri.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_mmquicklink;
defined('MOODLE_INTERNAL') || die();

class mmquicklink {

    /**
     * Define constants.
     */
    const STUDENTROLEID = 5;

    /**
     * Enable/disable & set enrolment key.
     * Trigger an event when succesful.
     *
     * @param int $courseid
     * @param string $enrolmentkey
     * @return bool
     */
    public static function set_enrolmentkey($courseid, $enrolmentkey = '') {
        global $DB, $USER, $CFG;

        // Check how many self-enrolment instances are in use in the course. Check also disabled sef-enrolments.
        $self = $DB->get_records('enrol', array('courseid' => $courseid, 'enrol' => 'self'), 'password');
        if (count($self) > 1) {
            $url = new \moodle_url($CFG->wwwroot . "/enrol/instances.php", array('id' => $courseid));
            $urltogo = new \moodle_url(get_local_referer(), array('id' => $courseid));
            redirect($urltogo, get_string('toomanyselfenrolments', 'block_mmquicklink', $url->out()), null, 'error');
            return false;
        }

        // If self enrolment doesn't exist in the course, let's add one.
        if (!$DB->get_records('enrol', array('courseid' => $courseid, 'enrol' => 'self'))) {

            // Define the enrolment method.
            $enrol = new \stdClass();
            $enrol->courseid = $courseid;
            $enrol->enrol = 'self';
            $enrol->roleid = self::STUDENTROLEID;
            $enrol->enrolstartdate = 0;
            $enrol->customint6 = 1;
            $enrol->timemodified = time();
            $enrol->usermodified = $USER->id;

            // Insert into db.
            $insert = $DB->insert_record('enrol', $enrol);
        }

        $where = array(
            'courseid' => $courseid,
            'enrol' => 'self',
        );

        if (!empty($enrolmentkey)) {
            // DB queries to set enrolment key.
            $DB->set_field('enrol', 'status', '0', $where);
            $DB->set_field('enrol', 'password', $enrolmentkey, $where);
        } else {
            // DB queries to disable enrolment key.
            $DB->set_field('enrol', 'status', '1', $where);
            $DB->set_field('enrol', 'password', '', $where);
        }
        $DB->set_field('enrol', 'timemodified', time(), $where);

        // Trigger enrolmentkey updated event.
        $event = \block_mmquicklink\event\enrolmentkey_updated::create(array(
            'objectid' => $courseid,
            'userid' => $USER->id,
            'context' => \context_course::instance($courseid),
        ));
        $event->trigger();   

        return true;

    }

}