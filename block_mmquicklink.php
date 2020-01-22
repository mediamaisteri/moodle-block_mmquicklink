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

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/blocks/mmquicklink/lib.php');
require_once($CFG->dirroot . '/blocks/mmquicklink/classes/buttons.php');

class block_mmquicklink extends block_base {

    /**
     * Use global configuration.
     *
     * @return boolean true, since we use global configurationl.
     */
    public function has_config() {
        return true;
    }

    /**
     * Check if current user has access to block.
     * Private, to be used only in this class.
     *
     * @return boolean should user see the block.
     */
    private function hasaccess() {
        global $USER, $DB, $COURSE, $PAGE;

        // If user has switched role, check access against that role.
        if (is_role_switched($COURSE->id)) {

            // Get switched role's role id.
            $opts = mmquicklink_get_switched_role($USER, $PAGE);
            if (!empty($opts->metadata['asotherrole'])) {
                $roleid = $opts->metadata['roleid'];

                // Get role config from block.
                $roles = get_config('mmquicklink', 'config_roles');
                $roles = explode(",", $roles);

                // Check if the switched role has access.
                if (in_array($roleid, $roles)) {
                    return true;
                } else {
                    return false;
                }

            }

            // Return false is access hasn't been granted before.
            return false;

        } else {

            // Admin has access always.
            if (is_siteadmin()) {
                return true;
            }

            // Load config from global settings.
            $roles = get_config('mmquicklink', 'config_roles');
            $rolesarray = explode(",", $roles);

            // Check role assignment in course context.
            if ($COURSE->id > 1) {
                $ccontext = context_course::instance($COURSE->id);
                $userroles = get_user_roles($ccontext, $USER->id);
                foreach ($userroles as $ur) {
                    if (in_array($ur->roleid, $rolesarray)) {
                        return true;
                    }
                }
            } else {
                // Check if user has an appropriate role anywhere in the system. If not, we don't have to do anything else.
                $getroleoverview = $DB->get_record_sql("SELECT id,roleid,userid,contextid FROM {role_assignments}
                WHERE roleid IN ($roles) AND userid=$USER->id LIMIT 1");
                if ($getroleoverview != false) {
                    return true;
                }
            }

            // Return false if user has no access granted earlier.
            return false;
        }

    }

    /**
     * Check if block is suppoed to be shown on current pagelayout.
     *
     * @todo improve pagelayout array search.
     * @return boolean true/false depending if block is to be shown on current pagelayout.
     */
    private function hidetypes($found = 0) {
        global $PAGE;

        // Get selected pagelayouts from configuration.
        $pagelayouts = get_config('mmquicklink', 'config_pagelayouts');
        $pagelayouts = explode(",", $pagelayouts);

        $pagelayoutlist = [
            'base',
            'standard',
            'course',
            'coursecategory',
            'incourse',
            'frontpage',
            'admin',
            'mydashboard',
            'mypublic',
            'login',
            'popup',
            'frametop',
            'embedded',
            'maintenance',
            'print',
            'redirect',
            'report'
        ];

        // Loop through the pagelayouts.
        // TODO: Make the array search better.
        if (count($pagelayouts) > 0 && strlen($pagelayouts[0]) > 0) {
            foreach ($pagelayouts as $pagelayout) {
                if ($PAGE->pagelayout == $pagelayoutlist[$pagelayout]) {
                    return false;
                }
            }
        }

        // If current pagelayout is not found in approved layoutlist, hide the block.
        if ($found == 0) {
            return true;
        }

        return false;
    }

    /**
     * Block initialization.
     * Sets block title.
     */
    public function init() {
        if (empty(get_config('mmquicklink', 'config_blocktitle'))) {
            $this->title = get_string('title', 'block_mmquicklink');
        } else {
            $this->title = get_config('mmquicklink', 'config_blocktitle');
        }
    }

    /**
     * Do not allow multiple instances.
     */
    public function instance_allow_multiple() {
        return false;
    }

    /**
     * Dock block on default.
     * Usable only on clean based themes.
     *
     * @todo remove function after all the clean based themes are removed from customers.
     * @return array $attributes block content attributes array.
     */
    public function html_attributes() {
        $attributes = parent::html_attributes();
        if ($this->instance_can_be_docked() && get_user_preferences('docked_block_instance_'.$this->instance->id, 1)) {
            $attributes['class'] .= ' dock_on_load';
        }
        return $attributes;
    }

    /**
     * Use custom button sorting.
     * Retrieve sorting from database.
     *
     * @return string inline <style> for sorting the block links.
     */
    private function get_sort() {
        global $DB;
        $dbman = $DB->get_manager();
        $style = "";
        if ($dbman->table_exists("block_mmquicklink_sorting")) {
            $getsort = $DB->get_records_sql("SELECT * FROM {block_mmquicklink_sorting}");
            foreach ($getsort as $element) {
                $style .= ".list-$element->button {order: $element->order} ";
            }
            if (!empty($style)) {
                return "<style>$style</style>";
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * Can user edit the block settings or not?
     *
     * @todo allow managers to to edit block settings - maybe?
     * @return boolean true if user can edit.
     */
    public function user_can_edit() {
        if (is_siteadmin()) {
            return true;
        }
        return false;
    }

    /**
     * Do not show content, if user doesn't have access to block.
     *
     * @return boolean
     */
    public function is_empty() {
        global $PAGE;

        // Check if block is wanted on this pagetype.
        if ($this->hidetypes() == true) {
            return true;
        }

        // Check if user has access.
        if ($this->hasaccess() == true) {
            return false;
        }

        // Return empty if not otherwise stated.
        return true;
    }

    /**
     * Render default link element.
     *
     * @param string $url URL to be linked to.
     * @param string $str Lang string to be shown on link.
     * @param string $buttonid Button's identifier (for sorting).
     * @return string html list-item element rendered via templates.
     */
    private function default_element($url, $str, $buttonid = "null") {
        global $OUTPUT;
        // Render element from template.
        $html = $OUTPUT->render_from_template("block_mmquicklink/li",
            array(
                "url" => $url,
                "str" => $str,
                "buttonid" => $buttonid
            )
        );
        return $html;
    }

    /**
     * Check if local_course_templates plugin is installed on the system.
     *
     * @return core_plugin_manager instance if local_course_templates plugin is installed.
     */
    private function coursetemplates() {
        // Check if local_course_templates -plugin is installed.
        if (isset(core_plugin_manager::instance()->get_plugins_of_type('local')["course_templates"]->name)) {
            $coursetemplates = core_plugin_manager::instance()->get_plugins_of_type('local')["course_templates"]->name;
            return $coursetemplates;
        } else {
            return null;
        }
    }

    /**
     * Get content for output.
     *
     * @param $output
     * @return block_contents $bc
     */
    public function get_content_for_output($output) {
        global $CFG;
        $bc = new block_contents($this->html_attributes());
        $bc->attributes['data-block'] = $this->name();
        $bc->blockinstanceid = $this->instance->id;
        $bc->blockpositionid = $this->instance->blockpositionid;
        if ($this->instance->visible) {
            $bc->content = $this->formatted_contents($output);
            if (!empty($this->content->footer)) {
                $bc->footer = $this->content->footer;
            }
        } else {
            $bc->add_class('invisibleblock');
        }
        if (!$this->hide_header()) {
            $bc->title = $this->title;
        }
        if (empty($bc->title)) {
            $bc->arialabel = new lang_string('pluginname', get_class($this));
            $this->arialabel = $bc->arialabel;
        }
        // Show controls if user has access and is editing.
        if ($this->page->user_is_editing() && $this->hasaccess() == true) {
            $bc->controls = $this->page->blocks->edit_controls($this);
        } else {
            // We must not use is_empty on hidden blocks.
            if ($this->is_empty() && !$bc->controls) {
                return null;
            }
        }
        if (empty($CFG->allowuserblockhiding)
               || (empty($bc->content) && empty($bc->footer))
               || !$this->instance_can_be_collapsed()) {
            $bc->collapsible = block_contents::NOT_HIDEABLE;
        } else if (get_user_preferences('block' . $bc->blockinstanceid . 'hidden', false)) {
            $bc->collapsible = block_contents::HIDDEN;
        } else {
            $bc->collapsible = block_contents::VISIBLE;
        }
        if ($this->instance_can_be_docked() && !$this->hide_header()) {
            $bc->dockable = true;
        }
        $bc->annotation = '';
        return $bc;
    }

    /**
     * Get block content.
     *
     * @todo Delete course: use modal confirmation instead of a page.
     * @return html Data to be printed in the block.
     */
    public function get_content() {
        // Load required globals.
        global $PAGE, $CFG, $USER, $COURSE, $DB, $OUTPUT;
        require_once($CFG->dirroot . '/blocks/mmquicklink/classes/buttons.php');
        $buttons = new buttons($CFG, $PAGE, $USER, $COURSE, $DB, $OUTPUT);

        // Prevents 'double output'.
        if ($this->content !== null) {
            return $this->content;
        }

        // Stop executing, if user doesn't have access.
        if ($this->hasaccess() == false) {
            return false;
        }

        // Load custom JS required for enrolment div toggling.
        $this->page->requires->js_call_amd('block_mmquicklink/enrolmentdiv', 'init', []);

        // Get block and local plugins.
        $plugins = core_plugin_manager::instance()->get_plugins_of_type('block');
        $authplugins = core_plugin_manager::instance()->get_plugins_of_type('auth');
        $localplugins = core_plugin_manager::instance()->get_plugins_of_type('local');

        // Check if visibility if wanted, because is_empty is not checked when user is in editing mode.
        if ($PAGE->user_is_editing()) {
            if ($this->hidetypes() == true) {
                // Force hiding with JS.
                $this->page->requires->js_call_amd('block_mmquicklink/blockhider', 'init', []);
                // Stop executing the script.
                return $this->content;
            }
        }

        // Set content variable.
        $this->content = new stdClass;
        $this->content->text = $this->get_sort();

        // Editing mode button.
        $this->content->text .= $buttons->editingmode();

        // Links to show on course pages.
        if ($PAGE->pagelayout == 'course' || $PAGE->pagelayout == "incourse" || $PAGE->pagetype == 'course-view-topics') {

            // Require confirm modal js (archive, show/hide).
            $PAGE->requires->js_call_amd("block_mmquicklink/confirm", "init", array("courseid" => $COURSE->id,
            "hide" => $COURSE->visible, "coursename" => $COURSE->fullname, "category" => $COURSE->category));

            // Render buttons needed on course pages.
            $this->content->text .= $buttons->editcourse();
            $this->content->text .= $buttons->showhide();
            $this->content->text .= $buttons->deletecourse();
            $this->content->text .= $buttons->archivecourse();
            $this->content->text .= $buttons->completionprogressblock($plugins);
            $this->content->text .= $buttons->enrolmentkey();
            $this->content->text .= $buttons->participants();
            $this->content->text .= $buttons->easylink($authplugins);
            $this->content->text .= $buttons->grading();
            $this->content->text .= $buttons->mreports($localplugins);
            $this->content->text .= $buttons->switchrole();
            $this->content->text .= $buttons->hrd();

        } else {

            // Check if local_course_templates is installed.
            $this->content->text .= $buttons->addcourse($this->coursetemplates());
            $this->content->text .= $buttons->coursemanagement();
            $this->content->text .= $buttons->themesettings();
            $this->content->text .= $buttons->mreportsnav($localplugins);
            $this->content->text .= $buttons->lang();
            $this->content->text .= $buttons->frontpage();

        }

        // Show placeholder text if block has no content.
        if (strlen($this->content->text) < 10) {
            $this->content->text .= $OUTPUT->render_from_template("block_mmquicklink/empty", array());
            $this->page->requires->js_call_amd('block_mmquicklink/blockhider', 'init', []);
            return $this->content;
        } else {
            // Wrap everything into one unsorted list.
            $this->content->text = $OUTPUT->render_from_template("block_mmquicklink/wrap",
                array("content" => $this->content->text)
            );
        }

        // Return data to block. Finally!
        return $this->content;

    }
}
