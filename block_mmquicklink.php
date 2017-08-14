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

class block_mmquicklink extends block_base {
    
    // Tell block to use global settings.
    function has_config() {
        return true;
    }

    // Function to check if user is admin, manager or teacher.
    private function hasAccess() {
            global $USER, $DB;

            // Admin has access always.
            if (is_siteadmin()) {
                return true;
            }
            
            // Load config from global settings.
            $roles = get_config('mmquicklink', 'config_roles');
            $roles = explode(",", $roles);
            foreach($roles as $role) {
                // Check user's role assignment.
                if (user_has_role_assignment($USER->id, $role, context_system::instance()->id)) {
                    // Return true if user has role assignment and the role has access.
                    return true;
                }
            }

            // Return false if user has no access granted earlier.
            return false;
            
    }

    public function init() {
            $this->title = get_string('title', 'block_mmquicklink');
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
    function user_can_edit() {
        if (is_siteadmin()) {
            return true;
        }
        return false;
    }

    // Show empty content if user has no access.
    function is_empty() {
        if ($this->hasAccess() == true) {
            return false;
        }
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
       if ($this->page->user_is_editing() && $this->hasAccess() == true) {
           $bc->controls = $this->page->blocks->edit_controls($this);
       } else {
           // we must not use is_empty on hidden blocks
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

        // Set variable.
        $this->content = new stdClass;
        $this->content->text = "";

        // Theme settings -link.
        if (empty(get_config('mmquicklink', 'config_hide_themesettings'))) {
            if (is_siteadmin()) {
                $adminurl = new moodle_url('/admin/settings.php?section=themesetting' . $PAGE->theme->name);
                $this->content->text.= "<li class='list'><a class='btn btn-secondary' href='" . $adminurl . "'>" . get_string('themesettings', 'core_admin') . "</a></li>";
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
        if (empty(get_config('mmquicklink', 'config_hide_langcust')) && has_capability('tool/customlang:edit', context_system::instance())) {
            $custlangurl = new moodle_url('/admin/tool/customlang/index.php');
            $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" . $custlangurl . "'>" . get_string('pluginname', 'tool_customlang') . "</a></li>";
        }
        

        // Links to show on course pages.
        if ($PAGE->pagelayout == 'course' || $PAGE->pagelayout == 'incourse' || $PAGE->pagelayout == 'report' ||
        $PAGE->pagetype == 'course-view-topics') {

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

            // Show/hide course visibility link.
            if (has_capability('moodle/course:visibility', context_course::instance($COURSE->id))) {
                if ($COURSE->visible == "1") {
                    $this->content->text .= "<li class='list'><a  class='btn btn-secondary' href='" .
                        new moodle_url($CFG->wwwroot . "/blocks/mmquicklink/changevisibility.php?hide=1&sesskey=" .
                        $USER->sesskey . "&id=" . $COURSE->id) . "'>" . get_string('hide') . " " .
                        strtolower(get_string('course')) . "</a></li>";
                } else {
                    $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" .
                        new moodle_url($CFG->wwwroot . "/blocks/mmquicklink/changevisibility.php?hide=0&sesskey=" .
                        $USER->sesskey . "&id=" . $COURSE->id) . "'>" . get_string('show') . " " .
                        strtolower(get_string('course')) . "</a></li>";
                }
            }

            // Check if 'hide course delete button' is checked.
            if (empty(get_config('mmquicklink', 'config_hide_delcourse'))) {
                // Show link if user has capability to delete course.
                if (has_capability('moodle/course:delete', context_course::instance($COURSE->id))) {
                    $delurl = new moodle_url($CFG->wwwroot . "/course/delete.php?id=" . $COURSE->id);
                    $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" . $delurl . "'>" .
                        get_string('delete') . " " . strtolower(get_string('course')) . "</a></li>";
                }
            }

            // Add a "completion progress" block.
            $plugins = core_plugin_manager::instance()->get_plugins_of_type('block');
            // Check if module is installed.
            if (!empty($plugins["completion_progress"]->name)) {
                // Check if user has capability.
                if (has_capability('block/completion_progress:addinstance', context_course::instance($COURSE->id))) {
                        $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" .
                        $CFG->wwwroot . "/course/view.php?id=" . $COURSE->id . "&sesskey=" . $USER->sesskey .
                        "&bui_addblock=completion_progress'>" . get_string('add') . " " .
                        strtolower(get_string('pluginname', 'block_completion_progress')) . "</a></li>";
                }
            }

            // Show enrolment key add button.
            if (has_capability('moodle/course:update', context_course::instance($COURSE->id))) {
                global $DB;
                $oldkey = $DB->get_record('enrol', array('courseid'=>$COURSE->id, 'enrol'=>'self', 'status'=>0), 'password');
                if(!empty($oldkey->password)) {
                    $realoldkey = $oldkey->password;
                } else {
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
                    </div>";
            }

            // Course participants.
            if (empty(get_config('mmquicklink', 'config_hide_participants'))) {
                if (has_capability('moodle/course:viewparticipants', context_course::instance($COURSE->id))) {
                    $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" . new moodle_url($CFG->wwwroot .
                    "/user/index.php?id=" . $PAGE->course->id) . "'>" .
                        get_string('show') . " " . strtolower(get_string('participants')) . "</a></li>";
                }
            }

            // Course grading.
            if (empty(get_config('mmquicklink', 'config_hide_course_grades'))) {
                if (has_capability('mod/assign:grade', context_system::instance())) {
                    $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" . new moodle_url($CFG->wwwroot .
                    "/grade/report/grader/index.php?id=" . $PAGE->course->id) . "'>" . get_string('coursegrades') . "</a></li>";
                }
            }

        } else {
            // Links on other pages.

            // Editing mode on/off link.
            if ($this->hasAccess() == true) {
                if ($PAGE->user_is_editing()) {
                    $editingmode = "off";
                    $editingmodestring = get_string("turneditingoff");
                } else {
                    $editingmode = "on";
                    $editingmodestring = get_string("turneditingon");
                }

                // Check if user has capability to edit frontpage.
                if ($PAGE->pagelayout == "frontpage" && has_capability('moodle/course:update', context_course::instance($COURSE->id))) {
                    $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" .
                        new moodle_url($CFG->wwwroot . "/course/view.php?id=1&edit=" . $editingmode .
                        "&sesskey=" . $USER->sesskey) . "'>" . $editingmodestring . "</a></li>";
                }
                
                if ($PAGE->pagelayout == "mydashboard" OR $PAGE->pagelayout == "admin") {
                    $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" .
                        new moodle_url($PAGE->url . "?edit=" . $editingmode .
                        "&sesskey=" . $USER->sesskey) . "'>" . $editingmodestring . "</a></li>";
                }

            }

            // Show "add a course" button.
            if (has_capability('moodle/course:create', context_course::instance($COURSE->id))) {
                $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" .
                    new moodle_url($CFG->wwwroot . "/course/edit.php?category=1") . "'>".
                    get_string('addnewcourse') . "</a></li>";
            }

            // Show course management button.
            if (has_capability('moodle/category:manage', context_course::instance($COURSE->id))) {
                $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" .
                    new moodle_url($CFG->wwwroot . "/course/management.php") . "'>".
                    get_string('coursemgmt', 'core_admin') . "</a></li>";
            }

            // Frontpage settings link only on frontpage.
            if ($this->hasAccess() == true && has_capability('moodle/course:update', context_course::instance($COURSE->id))) {
                if ($PAGE->pagelayout == 'frontpage') {
                    $this->content->text .= "<li class='list'><a class='btn btn-secondary' href='" .
                        new moodle_url($CFG->wwwroot . "/admin/settings.php?section=frontpagesettings") . "'>" .
                        get_string('frontpagesettings') . "</a></li>";
                }
            }

        }

        // Return data to block.
        return $this->content;

    }

}
