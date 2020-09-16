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
 * MM Quicklink lib.
 *
 * @package   block_mmquicklink
 * @copyright 2019 Mediamaisteri Oy
 * @author    Mikko Haikonen <mikko.haikonen@mediamaisteri.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Check if user is using a 'switched role' functionality.
 *
 * @param object $user USER object.
 * @param object $page $PAGE object.
 * @param array $options Options array.
 * @return object $returnobject.
 */
function mmquicklink_get_switched_role($user, $page, $options = array()) {
    global $DB, $COURSE;

    $returnobject = new stdClass();
    $returnobject->metadata = array();
    $context = context_course::instance($COURSE->id);

    if (is_role_switched($COURSE->id)) {

        if ($role = $DB->get_record('role', array('id' => $user->access['rsw'][$context->path]))) {
            $returnobject->metadata['asotherrole'] = true;
            $returnobject->metadata['roleid'] = $role->id;
            $returnobject->metadata['rolename'] = role_get_name($role, $context);
        }
    }

    return $returnobject;

}

// Create an 'admin setting' sub class for sorting.
require_once($CFG->libdir . "/adminlib.php");
class admin_setting_configquicklinksort extends admin_setting {
    private function get_sort() {
        global $DB;
        $style = "";
        $getsort = $DB->get_records_sql("SELECT * FROM {block_mmquicklink_sorting}");
        foreach ($getsort as $element) {
            $style .= "li[data-button='$element->button'] {order: $element->sortorder} ";
        }
        return "<style>$style</style>";
    }

    /**
     * Construct.
     *
     * @param string $name Element name.
     * @param string $heading Visible name.
     * @param string $information Description.
     */
    public function __construct($name, $heading, $information) {
        $this->nosave = true;
        parent::__construct($name, $heading, $information, '');
    }

    /**
     * Usually retrieves the current setting using the objects name.
     * Here return true is enough.
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Usually retrieves the default settings using the objects name.
     * Here return true is enough.
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Usually sets the value for the setting.
     * Here not needed as we use another database table for sorting.
     *
     * @param mixed $data
     * @return string empty string
     */
    public function write_setting($data) {
        return '';
    }

    /**
     * Returns the sorting field.
     *
     * @param string $data Inputted data.
     * @param string $query
     * @return string HTML to output.
     */
    public function output_html($data, $query = '') {
        global $OUTPUT;
        $context = new stdClass();
        $context->title = $this->visiblename;
        $context->description = $this->description;
        $context->descriptionformatted = highlight($query, markdown_to_html($this->description));
        return $this->get_sort() . $OUTPUT->render_from_template('block_mmquicklink/setting_quicklink', $context);
    }

}