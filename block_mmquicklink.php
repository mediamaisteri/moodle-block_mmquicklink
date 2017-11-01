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
 * @package   block_mmquicklink
 * @copyright 2017 Mediamaisteri Oy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/************************
Quick Link -block to help admins, managers
and teachers to navigate through Moodle.

2017
Mediamaisteri Oy
************************/

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/blocks/mmquicklink/lib.php');

class block_mmquicklink extends block_base {

    // Tell block to use global settings.
    public function has_config() {
        return true;
    }

    // Function to check if user is admin, manager or teacher.
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
            $roles = explode(",", $roles);
            foreach ($roles as $role) {
                // Check user's role assignment.
                if (user_has_role_assignment($USER->id, $role, context_system::instance()->id)) {
                    // Return true if user has role assignment and the role has access.
                    return true;
                }

                if (user_has_role_assignment($USER->id, $role)) {
                    return true;
                }
            }

            // Return false if user has no access granted earlier.
            return false;
        }

    }

    // Function to hide the block on specific pagetypes.
    private function hidetypes() {
        global $PAGE;

        $pagelayouts = get_config('mmquicklink', 'config_pagelayouts');
        $pagelayouts = explode(",", $pagelayouts);

        $pagelayoutlist = ['base',
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
        'report'];

        $found = 0;
        if (count($pagelayouts) > 0 && strlen($pagelayouts[0]) > 0) {
            foreach ($pagelayouts as $pagelayout) {
                if ($PAGE->pagelayout == $pagelayoutlist[$pagelayout]) {
                    $found = 1;
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

    public function init() {
        if (empty(get_config('mmquicklink', 'config_blocktitle'))) {
            $this->title = get_string('title', 'block_mmquicklink');
        } else {
            $this->title = get_config('mmquicklink', 'config_blocktitle');
        }
    }

    // Don't allow multiple instances.
    public function instance_allow_multiple() {
        return false;
    }

    // Dock block on default.
    public function html_attributes() {
        $attributes = parent::html_attributes();
        if ($this->instance_can_be_docked() && get_user_preferences('docked_block_instance_'.$this->instance->id, 1)) {
            $attributes['class'] .= ' dock_on_load';
        }
        return $attributes;
    }

    // User can edit only is the user has access.
    public function user_can_edit() {
        if (is_siteadmin()) {
            return true;
        }
        return false;
    }

    // Show empty content if user has no access.
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
            $bc->add_class('invisible');
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

    public function get_content() {
        // Load required globals.
        global $PAGE, $CFG, $USER, $COURSE;

        if ($this->content !== null) {
            return $this->content;
        }

        // Load custom JS.
        $this->page->requires->js_call_amd('block_mmquicklink/enrolmentdiv', 'init', []);

        // Check if visibility if wanted, because is_empty is not checked when user is in editing mode.
        if ($PAGE->user_is_editing()) {
            if ($this->hidetypes() == true) {
                // Force hiding with JS.
                $this->page->requires->js_call_amd('block_mmquicklink/blockhider', 'init', []);
                // Stop executing the script.
                return $this->content;
            }
        }

        // Set variable.
        $this->content = new stdClass;
        $this->content->text = "";

        // Links to show on course pages.
        if ($PAGE->pagelayout == 'course' || $PAGE->pagelayout == "incourse" || $PAGE->pagetype == 'course-view-topics') {

            if ($PAGE->user_allowed_editing()) {
                // Editing mode on/off link.
                if (has_capability('moodle/course:update', context_course::instance($COURSE->id))) {
                    if ($PAGE->user_is_editing()) {
                        $editingmode = "off";
                        $editingmodestring = get_string("turneditingoff");
                    } else {
                        $editingmode = "on";
                        $editingmodestring = get_string("turneditingon");
                    }
                    $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" .
                        new moodle_url($CFG->wwwroot . "/course/view.php?id=" . $COURSE->id . "&edit=" . $editingmode .
                        "&sesskey=" . $USER->sesskey) . "'>" . $editingmodestring . "</a></li>";
                }
            }

            // Edit course or mod settings.
            if (empty(get_config('mmquicklink', 'config_hide_editsettings') &&
            has_capability('moodle/course:update', context_course::instance($COURSE->id)))) {
                if ($PAGE->pagelayout == "course") {
                    $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" .
                    new moodle_url($CFG->wwwroot . "/course/edit.php?id=" . $COURSE->id) . "'>" .
                    get_string('editsettings', 'core') . "</a></li>";
                } else {
                    if (!empty($PAGE->cm->id)) {
                        $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" .
                        new moodle_url($CFG->wwwroot . "/course/modedit.php?update=" . $PAGE->cm->id) . "'>" .
                        get_string('editsettings', 'core') . "</a></li>";
                    }
                }
            }

            // Show/hide course visibility link.
            if (has_capability('moodle/course:visibility', context_course::instance($COURSE->id))) {
                if ($COURSE->visible == "1") {
                    $this->content->text .= "<li class='list'><a  class='btn btn-secondary' href='" .
                        new moodle_url($CFG->wwwroot . "/blocks/mmquicklink/changevisibility.php?hide=1&sesskey=" .
                        $USER->sesskey . "&id=" . $COURSE->id) . "'>" . get_string('hide_course', 'block_mmquicklink') .
                         "</a></li>";
                } else {
                    $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" .
                        new moodle_url($CFG->wwwroot . "/blocks/mmquicklink/changevisibility.php?hide=0&sesskey=" .
                        $USER->sesskey . "&id=" . $COURSE->id) . "'>" . get_string('show_course', 'block_mmquicklink') .
                        "</a></li>";
                }
            }

            // Check if 'hide course delete button' is checked.
            if (empty(get_config('mmquicklink', 'config_hide_delcourse'))) {
                // Show link if user has capability to delete course.
                if (has_capability('moodle/course:delete', context_course::instance($COURSE->id))) {
                    $delurl = new moodle_url($CFG->wwwroot . "/course/delete.php?id=" . $COURSE->id);
                    $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" . $delurl . "'>" .
                        get_string('delete_course', 'block_mmquicklink') . "</a></li>";
                }
            }

            // Add a "completion progress" block.
            $plugins = core_plugin_manager::instance()->get_plugins_of_type('block');
            // Check if module is installed.
            if (!empty($plugins["completion_progress"]->name)) {
                if ($PAGE->blocks->is_block_present('completion_progress') == false) {
                    if ($PAGE->user_is_editing()) {
                        // Check if user has capability.
                        if (has_capability('block/completion_progress:addinstance', context_course::instance($COURSE->id))) {
                                $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" .
                                $CFG->wwwroot . "/course/view.php?id=" . $COURSE->id . "&sesskey=" . $USER->sesskey .
                                "&bui_addblock=completion_progress'>" . get_string('add') . " " .
                                strtolower(get_string('pluginname', 'block_completion_progress')) . "</a></li>";
                        }
                    }
                }
            }

            // Show enrolment key add button.
            if (has_capability('moodle/course:update', context_course::instance($COURSE->id))) {
                global $DB;
                $oldkey = $DB->get_records('enrol', array('courseid' => $COURSE->id, 'enrol' => 'self', 'status' => 0), 'password');
                foreach ($oldkey as $oneoldkey) {
                    $realoldkey = $oneoldkey->password;
                    break;
                }
                if (empty($realoldkey)) {
                    $realoldkey = "";
                }
                $this->content->text .= "
                    <li class='list mmquicklink-enrolmentkey'><a class='btn btn-secondary' href=''>"
                     . get_string('set', 'portfolio_flickr') . " " .
                    strtolower(get_string('password', 'enrol_self')) . "</a></li>
                    <div class='mmquicklink-enrolmentkey-div'>
                        <form method='get' action='" . $CFG->wwwroot . "/blocks/mmquicklink/setenrolmentkey.php'>
                        <input type='hidden' name='courseid' value='" . $COURSE->id . "'>
                        <input class='form-control' type='text' name='enrolmentkey' value='" . $realoldkey . "'>
                        <input class='btn btn-primary' type='submit' value='" . get_string('save', 'core_admin') . "'>
                        </form>
                    </div>";
            }

            // Course participants.
            if (empty(get_config('mmquicklink', 'config_hide_participants'))) {
                if (has_capability('moodle/course:viewparticipants', context_course::instance($COURSE->id))) {
                    if (get_config('mmquicklink', 'config_participants_select') == 0) {
                        $participanturl = "/user/index.php?id=" . $PAGE->course->id;
                    } else {
                        $participanturl = "/enrol/users.php?id=" . $PAGE->course->id;
                    }
                    $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" .
                    new moodle_url($CFG->wwwroot . $participanturl) . "'>" . get_string('participants') . "</a></li>";
                }
            }

            // Course grading.
            if (empty(get_config('mmquicklink', 'config_hide_course_grades'))) {
                if (has_capability('mod/assign:grade', context_course::instance($COURSE->id))) {
                    $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" . new moodle_url($CFG->wwwroot .
                    "/grade/report/grader/index.php?id=" . $PAGE->course->id) . "'>" .
                    get_string('coursegrades', 'block_mmquicklink') . "</a></li>";
                }
            }

            // View as other role.
            if (empty(get_config('mmquicklink', 'config_hide_otherrole'))) {
                $otherrole = get_config('mmquicklink', 'config_otherrole_select');
                $otherrole_name = $DB->get_record('role', array('id'=>$otherrole));
                $this->content->text .= "
                    <li class='list mmquicklink-otherrole'>
                    <div class='mmquicklink-otherrole-div'>
                        <form method='get' action='" . $CFG->wwwroot . "/course/switchrole.php'>
                        <input type='hidden' name='id' value='" . $COURSE->id . "'>
                        <input type='hidden' name='switchrole' value='" . $otherrole ."'>
                        <input type='hidden' name='sesskey' value='" . $USER->sesskey . "'>
                        <input type='hidden' name='returnurl' value='" . $CFG->wwwroot . "/course/view.php?id=" . $COURSE->id . "'>
                        <input class='mmquicklink-btn btn btn-secondary' type='submit' value='" . get_string('switchrole', 'block_mmquicklink') . " " . $otherrole_name->name . "'>
                        </form>
                    </div>
                    </li>";                
            }

        } else {
            // Links on other pages.

            if ($PAGE->user_allowed_editing()) {
                
                // Editing mode on/off link.
                if ($this->hasaccess() == true) {
                    if ($PAGE->user_is_editing()) {
                        $editingmode = "off";
                        $editingmodestring = get_string("turneditingoff");
                    } else {
                        $editingmode = "on";
                        $editingmodestring = get_string("turneditingon");
                    }

                    // Check if user has capability to edit frontpage.
                    if ($PAGE->pagelayout == "frontpage" &&
                    has_capability('moodle/course:update', context_course::instance($COURSE->id))) {
                        $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" .
                            new moodle_url($CFG->wwwroot . "/course/view.php?id=1&edit=" . $editingmode .
                            "&sesskey=" . $USER->sesskey) . "'>" . $editingmodestring . "</a></li>";
                    }

                    // Dashboard editing mode.
                    if ($PAGE->pagelayout == "mydashboard") {
                        $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" .
                            new moodle_url($PAGE->url . "?edit=" . $editingmode .
                            "&sesskey=" . $USER->sesskey) . "'>" . $editingmodestring . "</a></li>";
                    }
                    
                    // Admin page editing mode.
                    if ($PAGE->pagelayout == "admin") {
                        $adminurl = str_replace("query", "", $PAGE->url);
                        var_dump($adminurl);
                        if (stripos($adminurl, "?") === false) {
                            $adminurl .= "?";
                        } else {
                            $adminurl .= "&";
                        }
                        $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" .
                            new moodle_url($adminurl . "adminedit=" . $editingmode .
                            "&sesskey=" . $USER->sesskey) . "'>" . $editingmodestring . "</a></li>";
                    }

                }

            }

            // Show "add a course" button.
            if (optional_param('categoryid', '', PARAM_INT)) {
                // Check if user can add course to current category.
                if (has_capability('moodle/course:create', context_coursecat::instance(optional_param('categoryid', '',
                PARAM_INT)))) {
                    $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" .
                        new moodle_url($CFG->wwwroot . "/course/edit.php?category=" . optional_param('categoryid', '', PARAM_INT)) .
                        "'>".
                        get_string('addnewcourse') . "</a></li>";
                }
            } else {
                if ($USER->id) {
                    // Check if user has capability to add a course to at least one category.
                    global $DB;
                    $categories = $DB->get_records('course_categories');
                    foreach ($categories as $category) {
                        if (has_capability('moodle/course:create', context_coursecat::instance($category->id))) {
                            $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" .
                                new moodle_url($CFG->wwwroot . "/course/edit.php?category=" . $category->id) . "'>".
                                get_string('addnewcourse') . "</a></li>";
                            break;
                        }
                    }
                }
            }

            // Show course management button.
            if (has_capability('moodle/category:manage', context_course::instance($COURSE->id))) {
                $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" .
                    new moodle_url($CFG->wwwroot . "/course/management.php") . "'>".
                    get_string('coursemgmt', 'core_admin') . "</a></li>";
            }

            // Theme settings -link.
            if (empty(get_config('mmquicklink', 'config_hide_themesettings'))) {
                if (is_siteadmin()) {
                    $adminurl = new moodle_url('/admin/settings.php?section=themesetting' . $PAGE->theme->name);
                    $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" . $adminurl . "'>" .
                    get_string('themesettings', 'core_admin') . "</a></li>";
                }
            }

            // Render local_reports navigation.
            if (empty(get_config('mmquicklink', 'config_hide_reports'))) {
                $reports = $PAGE->navigation->find('local_reports', navigation_node::TYPE_CUSTOM);
                if ($reports) {
                    $this->content->text .= "<li class='list mmquicklink-reports-button'><a class='btn btn-secondary'>"
                     . get_string('pluginname', 'local_reports') . "</a></li>";
                    $this->content->text .= $PAGE->get_renderer('block_mmquicklink')->mmquicklink_tree($reports);
                }
            }

            // Language customization link.
            if (empty(get_config('mmquicklink', 'config_hide_langcust')) &&
            is_siteadmin()) {
                $custlangurl = new moodle_url('/admin/tool/customlang/index.php');
                $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" .
                $custlangurl . "'>" . get_string('pluginname', 'tool_customlang') . "</a></li>";
            }

            // Frontpage settings link only on frontpage.
            if ($this->hasaccess() == true &&
            has_capability('moodle/course:update', context_course::instance($COURSE->id))) {
                if ($PAGE->pagelayout == 'frontpage') {
                    $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" .
                        new moodle_url($CFG->wwwroot . "/admin/settings.php?section=frontpagesettings") . "'>" .
                        get_string('frontpagesettings') . "</a></li>";
                }
            }

        }

        if (strlen($this->content->text) < 10) {
            $this->content->text .= get_string('emptyblock', 'block_mmquicklink');
        }

        // Return data to block.
        return $this->content;

    }
}