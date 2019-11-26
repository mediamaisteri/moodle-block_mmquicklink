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
require_once($CFG->dirroot.'/blocks/mmquicklink/lib.php');

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
     * @todo Check if custom sort is set (to prevent returning redundant <style></style>).
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
            return "<style>$style</style>";
        }
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
     * @return html list-item element rendered via templates.
     */
    private function default_element($url, $str, $buttonid = "null") {
        global $OUTPUT;
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
     * @todo find out what the output param really is ":D".
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
     * @return html Data to be printed in the block.
     */
    public function get_content() {
        // Load required globals.
        global $PAGE, $CFG, $USER, $COURSE, $DB, $OUTPUT;

        // Prevents 'double output'.
        if ($this->content !== null) {
            return $this->content;
        }

        // Load custom JS required for enrolment div toggling.
        $this->page->requires->js_call_amd('block_mmquicklink/enrolmentdiv', 'init', []);

        // Get block and local plugins.
        $plugins = core_plugin_manager::instance()->get_plugins_of_type('block');
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

        // Global editingmode variables.
        if ($PAGE->user_is_editing()) {
            $editingmode = "off";
            $editingmodestring = get_string("turneditingoff");
            $editbuttonid = "turneditingon";
        } else {
            $editingmode = "on";
            $editingmodestring = get_string("turneditingon");
            $editbuttonid = "turneditingon";
        }

        // Set variable.
        $this->content = new stdClass;
        $this->content->text = $this->get_sort();

        // Links to show on course pages.
        if ($PAGE->pagelayout == 'course' || $PAGE->pagelayout == "incourse" || $PAGE->pagetype == 'course-view-topics') {

            if ($PAGE->user_allowed_editing()) {
                // Editing mode on/off link.
                if (has_capability('moodle/course:update', context_course::instance($COURSE->id))) {
                    $url = new moodle_url($CFG->wwwroot . "/course/view.php", array(
                        "id" => $COURSE->id,
                        "edit" => $editingmode,
                        "sesskey" => $USER->sesskey,
                    ));
                    $this->content->text .= $this->default_element($url->out(), $editingmodestring, $editbuttonid);
                }
            }

            // Edit course or mod settings.
            if (empty(get_config('mmquicklink', 'config_hide_editsettings'))) {
                if (has_capability('moodle/course:update', context_course::instance($COURSE->id))) {
                    if ($PAGE->pagelayout == "course") {
                        $this->content->text .= $this->default_element($CFG->wwwroot . "/course/edit.php?id=" .
                        $COURSE->id, get_string('editsettings', 'core'), 'editsettings');
                    } else {
                        if (!empty($PAGE->cm->id)) {
                            $this->content->text .= $this->default_element($CFG->wwwroot . "/course/modedit.php?update=" .
                            $PAGE->cm->id, get_string('editsettings', 'core'), 'editsettings');
                        }
                    }
                }
            }

            // Show/hide course visibility link.
            if (empty(get_config('mmquicklink', 'config_hide_hidecourse'))) {
                if (has_capability('moodle/course:visibility', context_course::instance($COURSE->id))) {
                    if ($COURSE->visible == "1") {
                        $url = new moodle_url($CFG->wwwroot . "/blocks/mmquicklink/changevisibility.php", array(
                            "hide" => 1,
                            "id" => $COURSE->id,
                            "sesskey" => $USER->sesskey,
                        ));
                        $this->content->text .= $this->default_element($url->out(),
                        get_string('hide_course', 'block_mmquicklink'), 'hidecourse');
                    } else {
                        $url = new moodle_url($CFG->wwwroot . "/blocks/mmquicklink/changevisibility.php", array(
                            "hide" => 0,
                            "id" => $COURSE->id,
                            "sesskey" => $USER->sesskey,
                        ));
                        $this->content->text .= $this->default_element($url->out(),
                        get_string('show_course', 'block_mmquicklink'), 'showcourse');
                    }
                }
            }

            // Check if 'hide course delete button' is checked.
            if (empty(get_config('mmquicklink', 'config_hide_delcourse'))) {
                // Show link if user has capability to delete course.
                if (has_capability('moodle/course:delete', context_course::instance($COURSE->id))) {
                    $url = new moodle_url($CFG->wwwroot . "/course/delete.php", array(
                        "id" => $COURSE->id
                    ));
                    $this->content->text .= $this->default_element($url->out(),
                    get_string('delete_course', 'block_mmquicklink'), 'deletecourse');
                }
            }

            // Archive course button.
            $coursearchiveconf = get_config('local_course_archive');
            if (!empty($coursearchiveconf->plugin_enabled) && empty(get_config('mmquicklink', 'config_hide_archive'))) {
                if (has_capability('moodle/course:update', context_course::instance($COURSE->id))) {
                    $archcat = $coursearchiveconf->archivecategory;
                    $delcat = $coursearchiveconf->deletecategory;
                    if ($COURSE->category != $archcat && $COURSE->category != $delcat) {
                        $url = new moodle_url($CFG->wwwroot . "/blocks/mmquicklink/confirm.php", array(
                            "id" => $COURSE->id,
                            "categoryid" => $COURSE->category,
                        ));
                        $this->content->text .= $this->default_element($url->out(),
                        get_string('archive_course', 'block_mmquicklink', 'archivecourse'));
                    }
                }
            }

            // Add a "completion progress" block.
            // Check if module is installed.
            if (!empty($plugins["completion_progress"]->name)) {
                if ($PAGE->blocks->is_block_present('completion_progress') == false) {
                    if ($PAGE->user_is_editing()) {
                        // Check if user has capability.
                        if (has_capability('block/completion_progress:addinstance', context_course::instance($COURSE->id))) {
                            $url = new moodle_url($CFG->wwwroot . "/course/view.php", array(
                                "id" => $COURSE->id,
                                "sesskey" => $USER->sesskey,
                                "categoryid" => $COURSE->category,
                                "bui_addblock" => "completion_progress",
                            ));
                            $this->content->text .= $this->default_element($url->out(), get_string('add') . " " .
                            strtolower(get_string('pluginname', 'block_completion_progress')), 'completionprogress');
                        }
                    }
                }
            }

            // Show enrolment key add button.
            if (has_capability('moodle/course:update', context_course::instance($COURSE->id))) {
                $oldkey = $DB->get_records('enrol', array('courseid' => $COURSE->id, 'enrol' => 'self', 'status' => 0), 'password');
                foreach ($oldkey as $oneoldkey) {
                    $realoldkey = $oneoldkey->password;
                    $setstring = get_string('check', 'core');
                    $keyclass = "mmquicklink-enrolmentkey-set";
                    // Stop looping when the first key is found. That's all we need.
                    break;
                }
                if (empty($realoldkey)) {
                    $realoldkey = "";
                    $setstring = get_string('set', 'portfolio_flickr');
                    $keyclass = "mmquicklink-enrolmentkey-unset";
                }
                $setstring .= " " . strtolower(get_string('password', 'enrol_self'));
                $this->content->text .= $OUTPUT->render_from_template("block_mmquicklink/li-enrolmentkey", array(
                    "keyclass" => $keyclass,
                    "setstring" => $setstring,
                    "courseid" => $COURSE->id,
                    "realoldkey" => $realoldkey
                ));
            }

            // Course participants.
            if (empty(get_config('mmquicklink', 'config_hide_participants'))) {
                if (has_capability('moodle/course:viewparticipants', context_course::instance($COURSE->id))) {
                    if (get_config('mmquicklink', 'config_participants_select') == 0 OR $CFG->version >= 2018051700.00) {
                        $participanturl = new moodle_url($CFG->wwwroot . "/user/index.php", array(
                            "id" => $PAGE->course->id,
                        ));
                    } else {
                        $participanturl = new moodle_url($CFG->wwwroot . "/enrol/users.php", array(
                            "id" => $PAGE->course->id,
                        ));
                    }
                    $this->content->text .= $this->default_element($participanturl->out(),
                    get_string('participants'), 'participants');
                }
            }

            // Course grading.
            if (empty(get_config('mmquicklink', 'config_hide_course_grades'))) {
                if (has_capability('mod/assign:grade', context_course::instance($COURSE->id))) {
                    $url = new moodle_url($CFG->wwwroot . "/grade/report/grader/index.php", array(
                        "id" => $PAGE->course->id,
                    ));
                    $this->content->text .= $this->default_element($url->out(),
                    get_string('coursegrades', 'block_mmquicklink'), 'grades');
                }
            }

            // MRaportointi summary report.
            if (!empty($localplugins["reports"]->name)) {
                if (empty(get_config('mmquicklink', 'config_hide_local_reports_summary'))) {
                    if (has_capability('local/reports:viewall', context_system::instance()) && $COURSE->enablecompletion = 1) {
                        $getcriteria = $DB->get_records_sql("SELECT * FROM {course_completion_criteria} WHERE course=$COURSE->id");
                        if (!empty($getcriteria)) {
                            $url = new moodle_url($CFG->wwwroot . "/local/reports/summary.php", array(
                                "id" => $COURSE->id,
                                "groupby" => 0,
                                "includesuspended" => 0,
                                "submitbutton" => "View+summary",
                                "sesskey" => $USER->sesskey,
                                "_qf__local_reports_summary_form" => 1,
                            ));
                            $this->content->text .= $this->default_element($url->out(),
                            get_string('local_reports_summary', 'block_mmquicklink', 'localreportssummary'));
                        }
                    }
                }
            }

            // View as other role.
            if (!is_role_switched($COURSE->id) &&
            has_capability('moodle/role:switchroles', context_course::instance($COURSE->id))) {
                if (!is_role_switched($COURSE->id)) {
                    $otherrole = get_config('mmquicklink', 'config_otherrole_select');
                    $otherrolename = $DB->get_record('role', array('id' => $otherrole));

                    // Prioritize custom full name, if set in role configuration.
                    if (strlen($otherrolename->name) > 0) {
                        $otherroleshowname = $otherrolename->name;
                    } else {
                        // Use Moodle's core function to retrieve localized role name.
                        $otherroleshowname = role_get_name($otherrolename, context_system::instance(), ROLENAME_ALIAS);
                    }

                    // Render from template.
                    $this->content->text .= $OUTPUT->render_from_template("block_mmquicklink/li-otherrole", array(
                        "courseid" => $COURSE->id,
                        "otherrole" => $otherrole,
                        "sesskey" => $USER->sesskey,
                        "url" => $PAGE->url,
                        "otherroleshortname" => $otherrolename->shortname,
                        "otherroleshowname" => $otherroleshowname,
                    ));
                } else {
                    $url = new moodle_url($CFG->wwwroot . "/course/switchrole.php", array(
                        "id" => $COURSE->id,
                        "sesskey" => $USER->sesskey,
                        "returnurl" => (new moodle_url($CFG->wwwroot . "/course/view.php?id=" . $COURSE->id))->out(),
                    ));
                    $this->content->text .= $this->default_element($url->out(),
                    get_string('switchrolereturn', 'core'), 'switchrolereturn');
                }
            }

            if (isset($CFG->drupal_url) && has_capability('moodle/user:create', context_system::instance())) {
                $hrdurl = "{$CFG->drupal_url}/training_by_moodle_id/{$this->page->course->id}";
                $this->content->text .= $this->default_element($hrdurl,
                get_string('trainingmanagement', 'block_mmquicklink', 'hrd'));
            }

        } else {
            // Links shown on other pagelayouts and types.
            if ($PAGE->user_allowed_editing()) {

                // Editing mode on/off link.
                $editmodebuttonid = "editmodebuttonid";
                if ($this->hasaccess() == true) {
                    // Check if user has capability to edit frontpage.
                    if ($PAGE->pagelayout == "frontpage" &&
                    has_capability('moodle/course:update', context_course::instance($COURSE->id))) {
                        $url = new moodle_url($CFG->wwwroot . "/course/view.php", array(
                            "id" => 1,
                            "edit" => $editingmode,
                            "sesskey" => $USER->sesskey,
                        ));
                        $this->content->text .= $this->default_element($url->out(),
                        $editingmodestring, $editmodebuttonid . " list-turneditingon");
                    }

                    // Dashboard editing mode.
                    $indexsys = explode("/", $PAGE->url);
                    if ($PAGE->pagelayout == "mydashboard" && $indexsys[count($indexsys) - 1] !== "indexsys.php") {
                        $url = new moodle_url($PAGE->url, array(
                            "edit" => $editingmode,
                            "sesskey" => $USER->sesskey,
                        ));
                        $this->content->text .= $this->default_element($url->out(),
                        $editingmodestring, $editmodebuttonid . " list-turneditingon");
                    }

                    // Admin page editing mode.
                    if ($PAGE->pagelayout == "admin" OR $indexsys[count($indexsys) - 1] == "indexsys.php") {
                        $adminurl = new moodle_url($PAGE->url, array(
                            "adminedit" => $editingmode,
                            "sesskey" => $USER->sesskey,
                        ));
                        $this->content->text .= $this->default_element($adminurl->out(),
                        $editingmodestring, $editmodebuttonid . " list-turneditingon");
                    }

                    // Grader requires a specialized editmode link.
                    if ($PAGE->pagetype == "grade-report-grader-index") {
                        if ($USER->gradeediting[$COURSE->id]) {
                            $editingmode = 0;
                            $editingmodestring = get_string('turneditingoff');
                        } else {
                            $editingmode = 1;
                            $editingmodestring = get_string('turneditingon');
                        }
                        $adminurl = new moodle_url($PAGE->url, array(
                            "edit" => $editingmode,
                            "sesskey" => $USER->sesskey,
                        ));
                        $this->content->text .= $this->default_element($adminurl->out(),
                        $editingmodestring, $editmodebuttonid . " list-turneditingon");
                    }

                }

            }

            // Check if local_course_templates is installed.
            $coursetemplates = $this->coursetemplates();

            // Show "add a course" button.
            if (optional_param('categoryid', '', PARAM_INT)) {
                // Check if user can add course to current category.
                if (has_capability('moodle/course:create', context_coursecat::instance(optional_param('categoryid', '',
                PARAM_INT)))) {
                    if (!empty($coursetemplates)) {
                        // Render dropdown menu from templates if course_templates is installed.
                        global $OUTPUT;
                        $this->content->text .= $OUTPUT->render_from_template('block_mmquicklink/addnewcourse',
                            array("categoryid" => optional_param('categoryid', '', PARAM_INT)));
                    } else {
                        $url = new moodle_url($CFG->wwwroot . "/course/edit.php", array(
                            "category" => optional_param('categoryid', '', PARAM_INT),
                        ));
                        $this->content->text .= $this->default_element($url->out(),
                            get_string('addnewcourse'), 'addnewcourse');
                    }
                }
            } else {
                if ($USER->id) {

                    // Check capability to add a new course to default category first.
                    $defok = 0;
                    $defaultcategory = get_config('mmquicklink', 'config_defaultcategory');
                    if (!empty($defaultcategory)) {
                        if ($defaultcategory > 0) {
                            if (has_capability('moodle/course:create', context_coursecat::instance($defaultcategory))) {
                                if (!empty($coursetemplates)) {
                                    // Render dropdown menu from templates if course_templates is installed.
                                    $this->content->text .= $OUTPUT->render_from_template('block_mmquicklink/addnewcourse',
                                        array("categoryid" => $defaultcategory));
                                } else {
                                    $this->content->text .= $this->default_element($CFG->wwwroot .
                                    "/course/edit.php?category=" . $defaultcategory, get_string('addnewcourse'), 'addnewcourse');
                                }
                                $defok = 1;
                            }
                        }
                    }

                    // Check if user has capability to add a course to at least one category & default category didn't work.
                    if ($defok == 0) {
                        $categories = $DB->get_records('course_categories');
                        foreach ($categories as $category) {
                            if (has_capability('moodle/course:create', context_coursecat::instance($category->id))) {
                                if (!empty($coursetemplates)) {
                                    // Render dropdown menu from templates if course_templates is installed.
                                    $this->content->text .= $OUTPUT->render_from_template('block_mmquicklink/addnewcourse',
                                        array("categoryid" => $category->id));
                                    break;
                                } else {
                                    $this->content->text .= $this->default_element($CFG->wwwroot .
                                    "/course/edit.php?category=" . $category->id, get_string('addnewcourse'), 'addnewcourse');
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            // Show course management button.
            if ($PAGE->bodyid == 'page-course-index-category') {
                if (can_edit_in_category(optional_param('categoryid', '', PARAM_INT))) {
                    $this->content->text .= $this->default_element($CFG->wwwroot .
                    "/course/management.php", get_string('coursemgmt', 'core_admin'), 'coursemgmt');
                }
            } else {
                if (has_capability('moodle/category:manage', context_course::instance($COURSE->id))) {
                    $this->content->text .= $this->default_element($CFG->wwwroot .
                    "/course/management.php", get_string('coursemgmt', 'block_mmquicklink'), 'coursemgmt');
                }
            }

            // Theme settings -link.
            // If local_extrasettings is installed & user has proper capability, show link to it.
            if (empty(get_config('mmquicklink', 'config_hide_themesettings'))) {
                if (is_siteadmin()) {
                    $adminurl = ($CFG->wwwroot . '/admin/settings.php?section=themesetting' . $PAGE->theme->name);
                    $this->content->text .= $this->default_element($adminurl, get_string('themesettings', 'core_admin'),
                    'themesettings');
                } else if (!empty(core_plugin_manager::instance()->get_plugins_of_type('local')["extrasettings"]->name)) {
                    if (has_capability('local/extrasettings:accesssettings', context_system::instance())) {
                        $extrasettingsurl = $CFG->wwwroot . "/local/extrasettings/";
                        $this->content->text .= $this->default_element($extrasettingsurl, get_string('themesettings', 'core_admin'),
                        'themesettings');
                    }
                }
            }

            // Render local_reports navigation.
            if (!empty($localplugins["reports"]->name)) {
                if (empty(get_config('mmquicklink', 'config_hide_reports')) &&
                !empty(core_plugin_manager::instance()->get_plugins_of_type('local')["reports"]->name)) {
                    $categorymanager = 0;
                    if (!has_capability('local/reports:viewall', context_system::instance())) {
                        if (isset($CFG->local_reports_allowcategorymanagers)) {
                            if ($CFG->local_reports_allowcategorymanagers == 1) {
                                // Check if user has manager's right somewhere.
                                $role = $DB->get_records_sql("SELECT * FROM {role_assignments}
                                WHERE roleid='1' && userid='$USER->id'");

                                if (count($role) > 0) {
                                    $categorymanager = 1;
                                }
                            }
                        }
                    }

                    $reports = $PAGE->navigation->find('local_reports', navigation_node::TYPE_CUSTOM);
                    if (has_capability('local/reports:viewall', context_system::instance()) OR $categorymanager == 1) {
                        if ($reports) {
                            $this->content->text .= "<li class='list list-reports mmquicklink-reports-button'>
                            <a href='#' class='btn btn-secondary btn-reports'>" .
                            get_string('pluginname', 'local_reports') . "</a></li>";
                            $this->content->text .= "<li class='list list-reports m-0'>" .
                            $PAGE->get_renderer('block_mmquicklink')->mmquicklink_tree($reports) . "</li>";
                        }
                    }
                }
            }

            // Language customization link.
            if (empty(get_config('mmquicklink', 'config_hide_langcust')) &&
            has_capability('tool/customlang:view', context_system::instance())) {
                $custlangurl = $CFG->wwwroot . '/admin/tool/customlang/index.php';
                $this->content->text .= $this->default_element($custlangurl, get_string('pluginname', 'tool_customlang'),
                'customlang');
            }

            // Frontpage settings link only on frontpage.
            if ($this->hasaccess() == true &&
            has_capability('moodle/course:update', context_course::instance($COURSE->id))) {
                if ($PAGE->pagelayout == 'frontpage') {
                    $this->content->text .= $this->default_element($CFG->wwwroot .
                    "/admin/settings.php?section=frontpagesettings", get_string('frontpagesettings'), 'frontpagesettings');
                }
            }

        }

        // Show placeholder text if block has no content.
        if (strlen($this->content->text) < 10) {
            $this->content->text .= $OUTPUT->render_from_template("block_mmquicklink/empty", array());
            // Force hiding with JS.
            $this->page->requires->js_call_amd('block_mmquicklink/blockhider', 'init', []);
            // Stop executing the script.
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
