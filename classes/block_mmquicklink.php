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
 * @copyright 2021 Mediamaisteri Oy
 * @author    Mikko Haiku <mikko.haiku@mediamaisteri.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_mmquicklink;
defined('MOODLE_INTERNAL') || die();

class mmquicklink {

    /**
     * Define constants.
     */
    const STUDENTROLEID = 5;

    public static function get_self_enrolments($courseid) {
        global $DB;
        return $DB->get_records('enrol', array('courseid' => $courseid, 'enrol' => 'self'), 'password');
    }

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
        $self = self::get_self_enrolments($courseid);
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

    /**
     * Delete a custom button.
     *
     * @param int $id
     * @return bool
     */
    public function delete_custombutton($id) {
        global $DB;
        $delete = $DB->delete_records('block_mmquicklink_custombutt', ['id' => $id]);
        $sorting = $DB->delete_records('block_mmquicklink_sorting', ['button' => "custom_$id"]);
        return $delete;
    }

    /**
     * Render the custom button management page.
     *
     * @return html $html
     */
    public function manage_custombuttons($id = null, $action = null) {
        global $DB, $CFG, $OUTPUT;

        $data = new \stdClass();

        // Handle actions.
        if (!is_null($id) && !is_null($action)) {
            $func = $action . "_custombutton";
            $do = $this->$func($id);
            $url = new \moodle_url($CFG->wwwroot . "/blocks/mmquicklink/custombuttons.php");
            redirect($url, get_string('ok', 'block_mmquicklink'), null, \core\output\notification::NOTIFY_SUCCESS);
        }

        // Form.
        require_once($CFG->dirroot . "/blocks/mmquicklink/forms/custombuttons.php");
        $mform = new \block_mmquicklink_custombuttons_forms();
        if ($fromform = $mform->get_data()) {
            // Submit.
            $save = $this->custombuttons_save($fromform);
        }
        $mform->set_data(array());
        $mform->display();

        // Fetch customized buttons.
        $data->buttons = $this->get_custombuttons();

        // Generate HTML.
        $html = $OUTPUT->render_from_template("block_mmquicklink/custombuttons", $data);

        // Finally return the html to be displayed.
        return $html;
    }

    /**
     * Get a list of customized buttons from the database.
     * Depending on the case, this function returns all the buttons or just the ones
     * we want to be displaying on the current page.
     *
     * @param string $context Are we getting buttons for rendering or just reviewing the list?
     * @return array $buttons
     */
    public function get_custombuttons($context = null) {
        global $DB, $USER;

        // Define variables.
        $where = array();
        $ok = array();

        // Generate $where array.
        if (!is_null($context)) {
            $where['context'] = $context;
        }

        // Get the buttons.
        $buttons = $DB->get_records('block_mmquicklink_custombutt', $where);

        if (count($where) > 0) {
            $syscontext = \context_system::instance();
            foreach ($buttons as $button) {

                // Skip if the user doesn't have the required capability.
                if (!empty($button->requiredcapability)) {
                    if (!has_capability($button->requiredcapability, $syscontext)) {
                        continue;
                    }
                }

                // Skip if the user doesn't have the required role id.
                if (!empty($button->requiredroleid)) {
                    if (!$DB->get_record('role_assignments', array(
                        'contextid' => $syscontext->id,
                        'roleid' => $button->requiredroleid,
                        'userid' => $USER->id))) {
                        continue;
                    }
                }

                $button->href = format_string($this->custombutton_replace($button->href, 1));
                $button->description = format_string($this->custombutton_replace($button->description));

                // This button is OK to be displayed.
                $ok[] = $button;

            }

            // Return buttons printable for the current user.
            return $ok;
        }

        // Return all buttons for template.
        return array_values($buttons);
    }

    /**
     * Handle adding a new custom button.
     *
     * @param object $data
     * @return int DB row id.
     */
    public function custombuttons_save($data) {
        global $DB, $USER;

        // Make sure there is no whitespace at the end of cap.
        $data->requiredcapability = trim($data->requiredcapability);
        $data->requiredroleid = trim($data->requiredroleid);

        if ($data->requiredcapability == 0) {
            $data->requiredcapability = null;
        }

        if ($data->requiredroleid == 0) {
            $data->requiredroleid = null;
        }

        // Persistent columns.
        $data->usermodified = $USER->id;
        $data->timemodified = time();

        if (isset($data->id)) {
            // Update it.
            $update = $DB->update_record('block_mmquicklink_custombutt', $data);
            return $update;
        } else {
            // Insert it.
            $data->timecreated = $data->timemodified;
            $insert = $DB->insert_record('block_mmquicklink_custombutt', $data);
            return $insert;
        }
    }

    /**
     * Replace variables within string.
     *
     * @param string $href
     * @param bool Is the string URL?
     * @return string Modified string.
     */
    public function custombutton_replace($href, $url = null) {
        global $COURSE, $CFG, $PAGE, $USER;

        if (!is_null($url)) {
            if (stripos($href, "http") === false) {
                $href = $CFG->wwwroot . "/" . $href;
            }
        }

        // Replace variables.
        if (stripos($href, "{{id}}")) {
            $href = str_replace("{{id}}", $COURSE->id, $href);
        }
        if (stripos($href, "{{contextid}}")) {
            $href = str_replace("{{contextid}}", $PAGE->context->id, $href);
        }
        if (stripos($href, "{{userid}}")) {
            $href = str_replace("{{userid}}", $USER->id, $href);
        }

        // Return the beautified string.
        return $href;
    }

}
