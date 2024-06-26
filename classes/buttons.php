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
require_once($CFG->dirroot . '/course/lib.php');
require_once("{$CFG->libdir}/completionlib.php");

class buttons {

    /**
     * @var string Editing mode to be on or off / 0 or 1.
     */
    private $editingmode;

    /**
     * @var string Editing mode button's text string.
     */
    private $editingmodestring;

    /**
     * @var string Editing mode button's identifier.
     */
    private $editbuttonid;

    /**
     * Put global variables into these in construct.
     */
    private $cfg;
    private $page;
    private $user;
    private $course;
    private $db;
    private $output;

    /**
     * Construct.
     * Set needed variables.
     *
     * @param object $CFG
     * @param object $PAGE
     */
    public function __construct($CFG, $PAGE, $USER, $COURSE, $DB, $OUTPUT) {
        // Global variables.
        $this->cfg = $CFG;
        $this->output = $OUTPUT;
        $this->user = $USER;
        $this->course = $COURSE;
        $this->db = $DB;
        $this->page = $PAGE;

        // Editingmode variables.
        $this->editbuttonid = "turneditingon list-turneditingon";
        if ($this->page->user_is_editing()) {
            $this->editingmode = "off";
            $this->editingmodestring = get_string("turneditingoff");
        } else {
            $this->editingmode = "on";
            $this->editingmodestring = get_string("turneditingon");
        }
    }

    /**
     * Render default link element.
     *
     * @param string $url URL to be linked to.
     * @param string $str Lang string to be shown on link.
     * @param string $buttonid Button's identifier (for sorting).
     * @return html list-item element rendered via templates.
     */
    public function default_element($url, $str, $buttonid, $expandbtn = false) {
        global $OUTPUT;
        $html = $this->output->render_from_template("block_mmquicklink/li",
            array(
                "url" => $url,
                "str" => $str,
                "buttonid" => $buttonid,
                "expandbtn" => $expandbtn
            )
        );
        return (object) ['html' => $html, 'buttonid' => $buttonid, 'str' => $str, 'expandbtn' => $expandbtn];
    }

    public function coursecompletionsettings() {
        // Edit course completion settings.
        if (empty(get_config('mmquicklink', 'config_hide_coursecompletionsettings'))) {
            if (has_capability('moodle/course:update', context_course::instance($this->course->id))) {

                if ($this->page->pagelayout != "course") {
                    return false;
                }

                $url = new moodle_url($this->cfg->wwwroot . "/course/completion.php",
                    array("id" => $this->course->id));
                return $this->default_element($url->out(),
                    get_string('setting_coursecompletionsettings', 'block_mmquicklink'), 'coursecompletionsettings');
            }
        }
        return false;
    }

    public function coursebgimagechanger($html = "") {
        if (empty(get_config('mmquicklink', 'config_hide_coursebgimagechanger'))) {
            if ($this->page->theme->name == "maisteriboost") {
                if (file_exists($this->cfg->dirroot . "/theme/maisteriboost/classes/coursebgimagechanger.php")) {
                    if (has_capability('moodle/course:update', context_course::instance($this->course->id))) {
                        return $this->default_element($this->cfg->wwwroot .
                        "/theme/maisteriboost/classes/coursebgimagechanger.php?id=" . $this->course->id,
                        get_string('coursebgimagechanger', 'block_mmquicklink'), 'coursebgimagechanger');
                    }
                }
            }
        }
    }

    /**
     * Get LMS's first course.
     *
     * @return object $course Course object.
     */
    private function firstcourse() {
        $course = $this->db->get_record_sql("SELECT id FROM {course} WHERE id > 1 LIMIT 1");
        return $course->id;
    }

    /**
     * Renders the editing mode button.
     *
     * @return html ediginmode button element.
     */
    public function editingmode() {
        // Dashboard editing mode.
        $indexsys = explode("/", $this->page->url);
        if (has_capability('moodle/my:manageblocks', context_system::instance()) &&
        $this->page->pagelayout == "mydashboard" && $indexsys[count($indexsys) - 1] !== "indexsys.php") {
            $url = new moodle_url($this->page->url, array(
                "edit" => $this->editingmode,
                "sesskey" => $this->user->sesskey,
            ));
            return $this->default_element($url->out(),
            $this->editingmodestring, $this->editbuttonid);
        }

        // Frontpage.
        if ($this->page->pagelayout == "frontpage" &&
        has_capability('moodle/course:update', context_course::instance($this->course->id))) {
            $url = new moodle_url($this->cfg->wwwroot . "/course/view.php", array(
                "edit" => $this->editingmode,
                "sesskey" => $this->user->sesskey,
                "id" => $this->course->id,
            ));
            return $this->default_element($url->out(),
            $this->editingmodestring, $this->editbuttonid);
        }

        // Grader requires a specialized editmode link.
        if ($this->page->pagetype == "grade-report-grader-index") {
            if ($this->user->gradeediting[$this->course->id]) {
                $this->editingmode = 0;
                $this->editingmodestring = get_string('turneditingoff');
            } else {
                $this->editingmode = 1;
                $this->editingmodestring = get_string('turneditingon');
            }
            $adminurl = new moodle_url($this->page->url, array(
                "edit" => $this->editingmode,
                "sesskey" => $this->user->sesskey,
            ));
            return $this->default_element($adminurl->out(),
            $this->editingmodestring, $this->editbuttonid);

        }

        // Admin page editing mode.
        if ($this->page->pagelayout == "admin" || $indexsys[count($indexsys) - 1] == "indexsys.php") {
            $adminurl = new moodle_url($this->page->url, array(
                "adminedit" => $this->editingmode,
                "sesskey" => $this->user->sesskey,
            ));
            return $this->default_element($adminurl->out(),
            $this->editingmodestring, $this->editbuttonid);
        }

        if ($this->page->user_allowed_editing()) {
            // Editing mode on/off link.

            if (has_capability('moodle/course:update', context_course::instance($this->course->id))) {
                if ($this->page->pagelayout == "coursecategory") {
                    $categoryid = optional_param("categoryid", null, PARAM_INT);
                    if (isset($categoryid)) {
                        $categoryurl = "/course/?categoryid=" . $categoryid;
                    } else {
                        $categoryurl = "/course";
                    }
                    $url = new moodle_url($this->cfg->wwwroot . "/course/view.php", array(
                        "id" => $this->firstcourse(),
                        "edit" => $this->editingmode,
                        "sesskey" => $this->user->sesskey,
                        "return" => $categoryurl,
                    ));
                } else {
                    $url = new moodle_url($this->cfg->wwwroot . "/course/view.php", array(
                        "id" => $this->course->id,
                        "edit" => $this->editingmode,
                        "sesskey" => $this->user->sesskey,
                    ));
                }
                return $this->default_element($url->out(), $this->editingmodestring, $this->editbuttonid);
            }
        }
        return false;
    }

    /**
     * Edit course/mod button.
     *
     * @return html rendered element.
     */
    public function editcourse() {
        // Edit course or mod settings.
        if (empty(get_config('mmquicklink', 'config_hide_editsettings'))) {
            if (has_capability('moodle/course:update', context_course::instance($this->course->id))) {
                if ($this->page->pagelayout == "course") {
                    $url = new moodle_url($this->cfg->wwwroot . "/course/edit.php",
                        array("id" => $this->course->id));
                    return $this->default_element($url->out(), get_string('editcoursesettings', 'core'), 'editcoursesettings');
                } else {
                    if (!empty($this->page->cm->id)) {
                        $url = new moodle_url($this->cfg->wwwroot . "/course/modedit.php?update=",
                            array("update" => $this->page->cm->id));
                        return $this->default_element($url->out(), get_string('editsettings', 'core'), 'editsettings');
                    }
                }
            }
        }
        return false;
    }

    /**
     * Show/hide course button.
     *
     * @return html rendered element.
     */
    public function showhide() {
        // Show/hide course visibility link.
        if (empty(get_config('mmquicklink', 'config_hide_hidecourse'))) {
            if (has_capability('moodle/course:visibility', context_course::instance($this->course->id))) {
                if ($this->course->visible == "1") {
                    $url = new moodle_url($this->cfg->wwwroot . "/blocks/mmquicklink/changevisibility.php", array(
                        "hide" => 1,
                        "id" => $this->course->id,
                        "sesskey" => $this->user->sesskey,
                    ));
                    return $this->default_element($url->out(),
                    get_string('hide_course', 'block_mmquicklink'), 'hidecourse');
                } else {
                    $url = new moodle_url($this->cfg->wwwroot . "/blocks/mmquicklink/changevisibility.php", array(
                        "hide" => 0,
                        "id" => $this->course->id,
                        "sesskey" => $this->user->sesskey,
                    ));
                    return $this->default_element($url->out(),
                    get_string('show_course', 'block_mmquicklink'), 'hidecourse');
                }
            }
        } else {
            return null;
        }
    }

    /**
     * Show easylink button.
     *
     * @return html rendered element.
     */
    public function easylink($authplugins) {
        if (empty(get_config('mmquicklink', 'config_hide_easylink'))) {
            // Get course parent category.
            $categorypath = explode("/", $this->page->category->path);
            // Get allowed categories were to display easylink button.
            $allowedcategories = explode(",", get_config('mmquicklink', 'config_allowedcategories'));
            // Check if parent category exist on allowedcategory array or if allowedcategory config is empty.
            if (in_array($categorypath[1], $allowedcategories) || empty(get_config('mmquicklink', 'config_allowedcategories'))) {
                if (!empty($authplugins["easylink"]->name) && is_enabled_auth('easylink')) {
                    if (has_capability('auth/easylink:manage', context_course::instance($this->course->id))) {
                        $url = new moodle_url($this->cfg->wwwroot . "/auth/easylink/manager.php",
                            array(
                                "course" => $this->course->id,
                            )
                        );
                        return $this->default_element($url->out(),
                        get_string('pluginname', 'auth_easylink'), 'easylink');
                    }
                }
            }
        } else {
            return null;
        }
    }

    /**
     * Render 'archive course' element.
     *
     * @return html rendered element.
     */
    public function archivecourse() {
        // Archive course button.
        $coursearchiveconf = get_config('local_course_archive');
        if (!empty($coursearchiveconf->plugin_enabled) && empty(get_config('mmquicklink', 'config_hide_archive'))) {
            if (has_capability('moodle/course:update', context_course::instance($this->course->id))) {
                $archcat = $coursearchiveconf->archivecategory;
                $delcat = $coursearchiveconf->deletecategory;
                if ($this->course->category != $archcat && $this->course->category != $delcat) {
                    $url = new moodle_url($this->cfg->wwwroot . "/blocks/mmquicklink/confirm.php", array(
                        "id" => $this->course->id,
                        "categoryid" => $this->course->category,
                    ));
                    return $this->default_element($url->out(),
                    get_string('archive_course', 'block_mmquicklink'), 'archivecourse');
                }
            }
        }
    }

    /**
     * Render 'restore course' element.
     *
     * @return html rendered element.
     */
    public function restorecourse() {
        // Restore course button.
        $coursearchiveconf = get_config('local_course_archive');
        $isarchived = false;
        $archcat = isset($coursearchiveconf->archivecategory) ? $coursearchiveconf->archivecategory : false;
        $delcat = isset($coursearchiveconf->deletecategory) ? $coursearchiveconf->deletecategory : false;

        if ($this->course->category == $archcat || $this->course->category == $delcat) {
            $isarchived = true;
        }

        if (!empty($coursearchiveconf->plugin_enabled) && empty(get_config('mmquicklink', 'config_hide_restore')) && $isarchived) {
            if (has_capability('moodle/course:update', context_course::instance($this->course->id))) {
                    $url = new moodle_url($this->cfg->wwwroot . "/blocks/mmquicklink/confirm_restore.php",
                    array("id" => $this->course->id));
                    return $this->default_element($url->out(),
                    get_string('restorecourse', 'block_mmquicklink'), 'restorecourse');
            }
        }
    }

    /**
     * Render 'enrolment key' element.
     *
     * @return html rendered element.
     */
    public function enrolmentkey($selfenrolments) {

        $extratxt = '';
        if (count($selfenrolments) > 1) {
            $url = new \moodle_url($this->cfg->wwwroot . "/enrol/instances.php", array('id' => $this->course->id));
            $extratxt = get_string('toomanyselfenrolments', 'block_mmquicklink', $url->out());
        }

        $conf = get_config('enrol_self');
        $defaultenrol = $conf->defaultenrol;
        // Show enrolment key add button.
        if (has_capability('moodle/course:update', context_course::instance($this->course->id)) && $defaultenrol == 1) {
            $oldkey = $this->db->get_records('enrol', array(
                'courseid' => $this->course->id,
                'enrol' => 'self',
                'status' => 0), 'password');
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
            $html = $this->output->render_from_template("block_mmquicklink/li-enrolmentkey", array(
                "keyclass" => $keyclass,
                "setstring" => $setstring,
                "courseid" => $this->course->id,
                "realoldkey" => $realoldkey,
                "extratxt" => $extratxt,
            ));
            return (object) ['html' => $html, 'buttonid' => 'enrolmentkey', 'str' => $setstring];
        }
    }

    /**
     * Render 'switch role' element.
     *
     * @return html rendered element.
     */
    public function switchrole() {
        // View as other role.
        if (!is_role_switched($this->course->id) &&
        has_capability('moodle/role:switchroles', context_course::instance($this->course->id))) {
            if (!is_role_switched($this->course->id)) {
                $otherrole = get_config('mmquicklink', 'config_otherrole_select');
                if (empty($otherrole)) {
                    return;
                }
                $otherrolename = $this->db->get_record('role', array('id' => $otherrole));

                // Prioritize custom full name, if set in role configuration.
                if (strlen($otherrolename->name) > 0) {
                    $otherroleshowname = format_string($otherrolename->name);
                } else {
                    // Use Moodle's core function to retrieve localized role name.
                    if (is_object($otherrolename)) {
                        $otherroleshowname = role_get_name($otherrolename, context_system::instance(), ROLENAME_ALIAS);
                    }
                }

                // Render from template.
                $html = $this->output->render_from_template("block_mmquicklink/li-otherrole", array(
                    "courseid" => $this->course->id,
                    "otherrole" => $otherrole,
                    "sesskey" => $this->user->sesskey,
                    "url" => $this->page->url,
                    "otherroleshortname" => $otherrolename->shortname,
                    "otherroleshowname" => $otherroleshowname,
                ));
                return (object) ['html' => $html, 'buttonid' => 'otherrole'];
            } else {
                $url = new moodle_url($this->cfg->wwwroot . "/course/switchrole.php", array(
                    "id" => $this->course->id,
                    "sesskey" => $this->user->sesskey,
                    "returnurl" => (new moodle_url($this->cfg->wwwroot . "/course/view.php?id=" . $this->course->id))->out(),
                ));
                return $this->default_element($url->out(),
                get_string('switchrolereturn', 'core'), 'switchrolereturn');
            }
        }
    }

    /**
     * Render 'HRD' element.
     *
     * @return html rendered element.
     */
    public function hrd() {
        if (isset($this->cfg->drupal_url) && has_capability('moodle/user:create', context_system::instance())) {
            $hrdurl = "{$this->cfg->drupal_url}/training_by_moodle_id/{$this->page->course->id}";
            return $this->default_element($hrdurl,
            get_string('trainingmanagement', 'block_mmquicklink'), 'hrd');
        }
    }

    /**
     * Render 'mreports summary' element.
     *
     * @param object $localplugins
     * @return html rendered element.
     */
    public function mreports($localplugins) {
        // MRaportointi summary report.
        if (!empty($localplugins["reports"]->name)) {
            if (empty(get_config('mmquicklink', 'config_hide_local_reports_summary'))) {
                $courseid = $this->course->id;
                if (has_capability('local/reports:viewall', context_system::instance()) && $this->course->enablecompletion = 1) {
                    $getcriteria = $this->db->get_records_sql("SELECT * FROM {course_completion_criteria} WHERE course=$courseid");
                    if (!empty($getcriteria)) {
                        $url = new moodle_url($this->cfg->wwwroot . "/local/reports/summary.php", array(
                            "id" => $this->course->id,
                            "groupby" => 0,
                            "includesuspended" => 0,
                            "submitbutton" => "View+summary",
                            "sesskey" => $this->user->sesskey,
                            "_qf__local_reports_summary_form" => 1,
                        ));
                        return $this->default_element($url->out(),
                        get_string('local_reports_summary', 'block_mmquicklink'), 'localreportssummary');
                    }
                }
            }
        }
    }

    /**
     * Render 'grading' element.
     *
     * @return html rendered element.
     */
    public function grading() {
        // Course grading.
        if (empty(get_config('mmquicklink', 'config_hide_course_grades'))) {
            if (has_capability('mod/assign:grade', context_course::instance($this->course->id))) {
                $url = new moodle_url($this->cfg->wwwroot . "/grade/report/grader/index.php", array(
                    "id" => $this->page->course->id,
                ));
                return $this->default_element($url->out(),
                get_string('coursegrades', 'block_mmquicklink'), 'grades');
            }
        }
    }

    /**
     * Render 'participants' element.
     *
     * @return html rendered element.
     */
    public function participants() {
        // Course participants.
        if (empty(get_config('mmquicklink', 'config_hide_participants'))) {
            if (has_capability('moodle/course:viewparticipants', context_course::instance($this->course->id))) {
                if (get_config('mmquicklink', 'config_participants_select') == 0 || $this->cfg->version >= 2018051700.00) {
                    $participanturl = new moodle_url($this->cfg->wwwroot . "/user/index.php", array(
                        "id" => $this->page->course->id,
                    ));
                } else {
                    $participanturl = new moodle_url($this->cfg->wwwroot . "/enrol/users.php", array(
                        "id" => $this->page->course->id,
                    ));
                }
                return $this->default_element($participanturl->out(),
                get_string('participants'), 'participants');
            }
        }
    }

    /**
     * Render 'participants' element.
     *
     * @return html rendered element.
     */
    public function activityprogress() {

        if (!empty(get_config('mmquicklink', 'config_hide_activityprogress'))) {
            return false;
        }

        // Get criteria for course.
        $completion = new completion_info($this->course);

        if (!$completion->has_criteria()) {
            return false;
        }

        if (has_capability('moodle/course:update', context_course::instance($this->course->id))) {
            return $this->default_element($this->cfg->wwwroot .
            "/report/completion/index.php?course=" . $this->course->id, get_string('pluginname', 'report_progress'),
            "activityprogress");
        }
    }

    /**
     * Render 'add completion progress block' element.
     *
     * @param object $plugins
     * @return html rendered element.
     */
    public function completionprogressblock($plugins) {
        // Add a "completion progress" block.
        // Check if module is installed.
        if (!empty($plugins["completion_progress"]->name)) {
            if ($this->page->blocks->is_block_present('completion_progress') == false) {
                if ($this->page->user_is_editing()) {
                    // Check if user has capability.
                    if (has_capability('block/completion_progress:addinstance', context_course::instance($this->course->id))) {
                        $url = new moodle_url($this->cfg->wwwroot . "/course/view.php", array(
                            "id" => $this->course->id,
                            "sesskey" => $this->user->sesskey,
                            "categoryid" => $this->course->category,
                            "bui_addblock" => "completion_progress",
                        ));
                        return $this->default_element($url->out(), get_string('add') . " " .
                        strtolower(get_string('pluginname', 'block_completion_progress')), 'completionprogress');
                    }
                }
            }
        }
    }

    /**
     * Delete course button.
     *
     * @return html rendered element.
     */
    public function deletecourse() {
        // Check if 'hide course delete button' is checked.
        if (empty(get_config('mmquicklink', 'config_hide_delcourse'))) {
            // Show link if user has capability to delete course.
            if (has_capability('moodle/course:delete', context_course::instance($this->course->id))) {
                $url = new moodle_url($this->cfg->wwwroot . "/course/delete.php", array(
                    "id" => $this->course->id
                ));
                return $this->default_element($url->out(),
                get_string('delete_course', 'block_mmquicklink'), 'deletecourse');
            }
        }
    }

    /**
     * Render the 'add course' button.
     *
     * @param object $coursetemplates
     * @return html Default element of 'add course'.
     */
    public function addcourse($coursetemplates) {
        // Show "add a course" button.
        if (optional_param('categoryid', '', PARAM_INT)) {
            // Check if user can add course to current category.
            try {
                $ctcheck = has_capability('moodle/course:create', context_coursecat::instance(optional_param('categoryid', '',
                PARAM_INT)));
            } catch (Exception $e) {
                // Do nothing.
                $ctcheck = false;
            }
            if ($ctcheck) {
                if (!empty($coursetemplates)) {
                    // Render dropdown menu from templates if course_templates is installed.
                    $html = $this->output->render_from_template('block_mmquicklink/addnewcourse',
                            array("categoryid" => optional_param('categoryid', '', PARAM_INT)));
                    return (object) ['html' => $html,
                                    'buttonid' => 'addnewcourse',
                                    'str' => get_string('addnewcourse')
                                    ];
                } else {
                    $url = new moodle_url($this->cfg->wwwroot . "/course/edit.php", array(
                        "category" => optional_param('categoryid', '', PARAM_INT),
                    ));
                    return $this->default_element($url->out(),
                        get_string('addnewcourse'), 'addnewcourse');
                }
            }
        } else {
            if ($this->user->id) {

                // Check capability to add a new course to default category first.
                $defok = 0;
                $defaultcategory = get_config('mmquicklink', 'config_defaultcategory');
                if (!empty($defaultcategory)) {
                    if ($defaultcategory > 0) {
                        if (has_capability('moodle/course:create', context_coursecat::instance($defaultcategory))) {
                            if (!empty($coursetemplates)) {
                                // Render dropdown menu from templates if course_templates is installed.
                                $html = $this->output->render_from_template('block_mmquicklink/addnewcourse',
                                        array("categoryid" => $defaultcategory));
                                return (object) ['html' => $html,
                                                'buttonid' => 'addnewcourse',
                                                'str' => get_string('addnewcourse')
                                                ];
                            } else {
                                return $this->default_element($this->cfg->wwwroot .
                                "/course/edit.php?category=" . $defaultcategory, get_string('addnewcourse'), 'addnewcourse');
                            }
                            $defok = 1;
                        }
                    }
                }

                // Check if user has capability to add a course to at least one category & default category didn't work.
                if ($defok == 0) {
                    $categories = $this->db->get_records('course_categories');
                    foreach ($categories as $category) {
                        if (has_capability('moodle/course:create', context_coursecat::instance($category->id))) {
                            if (!empty($coursetemplates)) {
                                // Render dropdown menu from templates if course_templates is installed.
                                $html = $this->output->render_from_template('block_mmquicklink/addnewcourse',
                                        array("categoryid" => $category->id));
                                return (object) ['html' => $html,
                                                'buttonid' => 'addnewcourse',
                                                'str' => get_string('addnewcourse')
                                                ];
                                break;
                            } else {
                                return $this->default_element($this->cfg->wwwroot .
                                "/course/edit.php?category=" . $category->id, get_string('addnewcourse'), 'addnewcourse');
                                break;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Render 'theme settings' link.
     *
     * @return html Default element.
     */
    public function themesettings() {
        // Theme settings -link.
        // If local_extrasettings is installed & user has proper capability, show link to it.
        if (empty(get_config('mmquicklink', 'config_hide_themesettings'))) {
            if (!empty(core_plugin_manager::instance()->get_plugins_of_type('local')["extrasettings"]->name)) {
                if (has_capability('local/extrasettings:accesssettings', context_system::instance())) {
                    $extrasettingsurl = $this->cfg->wwwroot . "/local/extrasettings/";
                    return $this->default_element($extrasettingsurl, get_string('themesettings', 'core_admin'),
                    'themesettings');
                }
            }
        }
    }

    /**
     * Render mReports navigation.
     *
     * @param array $localplugins Array of locally installed plugins.
     * @return html mmQuicklink mReports tree.
     */
    public function mreportsnav($localplugins) {
        // Render local_reports navigation.
        if (!empty($localplugins["reports"]->name)) {
            if (empty(get_config('mmquicklink', 'config_hide_reports')) &&
            !empty(core_plugin_manager::instance()->get_plugins_of_type('local')["reports"]->name)) {
                $categorymanager = 0;
                if (!has_capability('local/reports:viewall', context_system::instance())) {
                    if (isset($CFG->local_reports_allowcategorymanagers)) {
                        if ($CFG->local_reports_allowcategorymanagers > 0) {
                            // Check if user has manager's right somewhere.
                            $role = $this->db->get_records_sql("SELECT * FROM {role_assignments}
                            WHERE roleid = :roleid && userid = :userid",
                            array('roleid' => 1, 'userid' => $USER->id));

                            if (count($role) > 0) {
                                $categorymanager = 1;
                            }
                        }
                    }
                }

                $reports = $this->page->navigation->find('local_reports', navigation_node::TYPE_CUSTOM);
                if ($reports) {
                    if (!empty($localplugins["learninghistory"]->name)
                        && empty(get_config('mmquicklink', 'config_hide_competencereport'))
                        && (has_capability('local/learninghistory:viewreport', context_system::instance()) ||
                            has_capability('local/learninghistory:viewsubordinatereport', context_system::instance()))) {
                            $reports->add(get_string('competencesreport', 'local_learninghistory'),
                                '/local/learninghistory/competences.php');
                    }
                    return (object) ['buttonid' => 'reports', 'html' => "<li class='list list-reports mmquicklink-reports-button'>
                    <a href='#' class='btn btn-secondary btn-reports'>" .
                    get_string('pluginname', 'local_reports') . "</a></li><li class='list list-reports m-0'>" .
                    $this->page->get_renderer('block_mmquicklink')->mmquicklink_tree($reports) . "</li>"];
                }
            }
        }
    }

    /**
     * Render 'course management' button.
     *
     * @return html Default element.
     */
    public function coursemanagement() {
        if ($this->page->bodyid == 'page-course-index-category') {
            if (can_edit_in_category(optional_param('categoryid', '', PARAM_INT))) {
                return $this->default_element($this->cfg->wwwroot .
                "/course/management.php", get_string('coursemgmt', 'core_admin'), 'coursemgmt');
            }
        } else {
            if (has_capability('moodle/category:manage', context_course::instance($this->course->id))) {
                return $this->default_element($this->cfg->wwwroot .
                "/course/management.php", get_string('coursemgmt', 'block_mmquicklink'), 'coursemgmt');
            }
        }
    }

    /**
     * Render language customization link.
     *
     * @return html Default element.
     */
    public function lang() {
        if (empty(get_config('mmquicklink', 'config_hide_langcust')) &&
        has_capability('tool/customlang:view', context_system::instance())) {
            $custlangurl = $this->cfg->wwwroot . '/admin/tool/customlang/index.php';
            return $this->default_element($custlangurl, get_string('pluginname', 'tool_customlang'),
            'customlang');
        }
    }

    /**
     * Render 'frontpage settings' button.
     * Link visible only on frontpage.
     *
     * @return html Default element.
     */
    public function frontpage() {
        if (has_capability('moodle/course:update', context_course::instance($this->course->id))) {
            if ($this->page->pagelayout == 'frontpage') {
                return $this->default_element($this->cfg->wwwroot .
                "/admin/settings.php?section=frontpagesettings", get_string('frontpagesettings'), 'frontpagesettings');
            }
        }
    }

    public function questionbank() {
        if (empty(get_config('mmquicklink', 'config_hide_questionbank'))) {
            // Tarvitaanko oikeustarkistelu?
            return $this->default_element($this->cfg->wwwroot .
            "/question/edit.php?courseid=" . $this->course->id, get_string('questionbank', 'question'), 'questionbank');
        }
    }

    public function questioncategory() {
        if (empty(get_config('mmquicklink', 'config_hide_questioncategory'))) {
            // Tarvitaanko oikeustarkistelu?
            return $this->default_element($this->cfg->wwwroot .
            "/question/category.php?courseid=" . $this->course->id,
            get_string('questioncategory', 'block_mmquicklink'), 'questioncategory');
        }
    }

    public function backupbutton() {
        if (empty(get_config('mmquicklink', 'config_hide_backup'))) {
            if (has_capability('moodle/backup:backupcourse', context_course::instance($this->course->id))) {
                return $this->default_element($this->cfg->wwwroot . "/backup/backup.php?id=" . $this->course->id,
                get_string('backup'), 'backup');
            }
        }
    }

    public function participantsparent() {
        return $this->default_element(null, get_string('setting_participantsparent', 'block_mmquicklink'),
            'participantsparent', true);
    }
    public function courseparent() {
        return $this->default_element(null, get_string('setting_courseparent', 'block_mmquicklink'), 'courseparent', true);
    }

    public function pageparent() {
        return $this->default_element(null, get_string('setting_pageparent', 'block_mmquicklink'), 'pageparent', true);
    }

    public function completionparent() {
        return $this->default_element(null, get_string('setting_completionparent', 'block_mmquicklink'), 'completionparent', true);
    }

}
