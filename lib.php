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

function mmquicklink_get_default_button_order() {

    // Add new buttons here! All buttons shown on button sorting needs to be here.
    $buttons = mmquicklink_default_buttons();

    // Make button data ready for template.
    $defaultorder = [];
    foreach ($buttons as $button) {
        $button->parent = 'main-list';
        $defaultorder[$button->button] = $button;
        if (!isset($button->children)) {
            continue;
        }
        foreach ($button->children as $child) {
            $child->parent = $button->button;
            $defaultorder[$child->button] = $child;
        }
        $defaultorder[$button->button] = $button;
    }

    return $defaultorder;
}

function mmquicklink_set_default_order() {
    global $DB;

    $buttons = mmquicklink_default_buttons();
    $defaultbuttons = [];
    $parentorder = 1;
    foreach ($buttons as $parent) {
        $parentbutton = (object) [
            'button' => $parent->button,
            'parent' => 'main-list',
            'sortorder' => $parentorder
        ];
        $defaultbuttons[] = $parentbutton;
        $childorder = 1;
        if (!empty($parent->children)) {
            foreach ($parent->children as $child) {
                $childbutton = (object) [
                    'button' => $child->button,
                    'parent' => $parent->button,
                    'sortorder' => $childorder
                ];
                $defaultbuttons[] = $childbutton;
                $childorder++;
            }
        }
        $parentorder++;
    }
    if (!$DB->get_records('block_mmquicklink_sorting', [])) {
        $DB->insert_records('block_mmquicklink_sorting', $defaultbuttons);
    }
}

function mmquicklink_default_buttons() {
    global $CFG;
    require_once($CFG->dirroot . "/blocks/mmquicklink/classes/block_mmquicklink.php");
    // Add new buttons here! All buttons shown on button sorting needs to be here.
    $buttons = [
        'courseparent' => (object) [
            'button' => 'courseparent',
            'string' => get_string('setting_courseparent', 'block_mmquicklink'),
            'children' => [
                'editcoursesettings' => (object) [
                    'button' => 'editcoursesettings',
                    'string' => get_string('editcoursesettings', 'core'),
                ],
                'turneditingon list-turneditingon' => (object) [
                    'button' => 'turneditingon list-turneditingon',
                    'string' => get_string('turneditingon'),
                ],
                'addnewcourse' => (object) [
                    'button' => 'addnewcourse',
                    'string' => get_string('addcourse', 'block_mmquicklink'),
                ],
                'coursemgmt' => (object) [
                    'button' => 'coursemgmt',
                    'string' => get_string('coursemgmt', 'block_mmquicklink'),
                ],
                'restorecourse' => (object) [
                    'button' => 'restorecourse',
                    'string' => get_string('restorecourse', 'block_mmquicklink'),
                ],
                'deletecourse' => (object) [
                    'button' => 'deletecourse',
                    'string' => get_string('setting_delcourse', 'block_mmquicklink'),
                ],
                'archivecourse' => (object) [
                    'button' => 'archivecourse',
                    'string' => get_string('setting_archive', 'block_mmquicklink'),
                ],
                'hidecourse' => (object) [
                    'button' => 'hidecourse',
                    'string' => get_string('setting_hidecourse', 'block_mmquicklink'),
                ],
                'enrolmentkey' => (object) [
                    'button' => 'enrolmentkey',
                    'string' => get_string('enrolmentkey', 'block_mmquicklink'),
                ],
                'coursebgimagechanger' => (object) [
                    'button' => 'coursebgimagechanger',
                    'string' => get_string('coursebgimagechanger', 'block_mmquicklink'),
                ],
                'questionbank' => (object) [
                    'button' => 'questionbank',
                    'string' => get_string('questionbank', 'block_mmquicklink'),
                ],
                'questioncategory' => (object) [
                    'button' => 'questioncategory',
                    'string' => get_string('questioncategory', 'block_mmquicklink'),
                ],
            ]
        ],
        'participantsparent' => (object) [
            'button' => 'participantsparent',
            'string' => get_string('setting_participantsparent', 'block_mmquicklink'),
            'children' => [
                'participants' => (object) [
                    'button' => 'participants',
                    'string' => get_string('setting_participants', 'block_mmquicklink'),
                ],
                'easylink' => (object) [
                    'button' => 'easylink',
                    'string' => get_string('setting_easylink', 'block_mmquicklink'),
                ],
                'grades' => (object) [
                    'button' => 'grades',
                    'string' => get_string('setting_course_grades', 'block_mmquicklink'),
                ],
                'otherrole' => (object) [
                    'button' => 'otherrole',
                    'string' => get_string('setting_otherrole_select', 'block_mmquicklink'),
                ]
            ]
        ],
        'completionparent' => (object) [
            'button' => 'completionparent',
            'string' => get_string('setting_completionparent', 'block_mmquicklink'),
            'children' => [
                'localreportssummary' => (object) [
                    'button' => 'localreportssummary',
                    'string' => get_string('local_reports_summary', 'block_mmquicklink'),
                ],
                'reports' => (object) [
                    'button' => 'reports',
                    'string' => get_string('setting_reports', 'block_mmquicklink'),
                ],
                'coursecompletionsettings' => (object) [
                    'button' => 'coursecompletionsettings',
                    'string' => get_string('setting_coursecompletionsettings', 'block_mmquicklink'),
                ],
                'completionprogress' => (object) [
                    'button' => 'completionprogress',
                    'string' => get_string('pluginname', 'block_completion_progress'),
                ],
                'activityprogress' => (object) [
                    'button' => 'activityprogress',
                    'string' => get_string('pluginname', 'report_progress'),
                ]
            ]
        ],
        'pageparent' => (object) [
            'button' => 'pageparent',
            'string' => get_string('setting_pageparent', 'block_mmquicklink'),
            'children' => [
                'customlang' => (object) [
                    'button' => 'customlang',
                    'string' => get_string('setting_langcust', 'block_mmquicklink'),
                ],
                'themesettings' => (object) [
                    'button' => 'themesettings',
                    'string' => get_string('setting_themesettings', 'block_mmquicklink'),
                ],
                'backup' => (object) [
                    'button' => 'backup',
                    'string' => get_string('backup', 'block_mmquicklink'),
                ],
                'frontpagesettings' => (object) [
                    'button' => 'frontpagesettings',
                    'string' => get_string('frontpagesettings'),
                ],
            ]
        ]
    ];
    $mmquicklink = new \block_mmquicklink\mmquicklink();
    foreach ($mmquicklink->get_custombuttons('other') as $button) {
        $buttons["custom_$button->id"] = (object) [
            'button' => "custom_$button->id",
            'string' => $button->description
        ];
    }
    return $buttons;
}


// Create an 'admin setting' sub class for sorting.
require_once($CFG->libdir . "/adminlib.php");
class admin_setting_configquicklinksort extends admin_setting {

    private function get_sort() {
        global $DB;

        // Existing sort order.
        $order = $DB->get_records('block_mmquicklink_sorting', null, 'sortorder ASC');
        $defaultorder = mmquicklink_get_default_button_order();
        // If there are no customizations to sort order, use default order.
        if (empty($order)) {
            mmquicklink_set_default_order();
            $order = $DB->get_records('block_mmquicklink_sorting', null, 'sortorder ASC');
        }
        $parents = [];

        foreach ($order as $key => $value) {
            if ($value->parent == "main-list") {
                unset($order[$key]);
                $parent = $value;
                $parent->string = $defaultorder[$value->button]->string;
                $parent->expandbutton = false;
                $children = [];
                foreach ($order as $key => $child) {
                    if ($child->parent == $value->button) {
                        $child->string = $defaultorder[$child->button]->string;
                        $children[] = $child;
                        unset($order[$key]);
                        unset($defaultorder[$child->button]);
                    }
                }

                if ($value->button == 'completionparent' || $value->button == 'courseparent' ||
                    $value->button == 'pageparent' || $value->button == 'participantsparent') {
                        $parent->expandbutton = true;
                }
                $parent->children = $children;
                $parent->haschildren = (!empty($children));
                $parents[] = $parent;
                unset($defaultorder[$value->button]);
            }
        }

        // If there are buttons left that arent in sorting_order db, add them under their default parent.
        foreach ($defaultorder as $button) {
            if ($button->parent == 'main-list') {
                $parents[] = $button;
                continue;
            }
            foreach ($parents as $parent) {
                if ($button->parent == $parent->button) {
                    $parent->children[] = $button;
                    continue;
                }
            }
        }

        return $parents;
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
        $context->parents = $this->get_sort();
        return $OUTPUT->render_from_template('block_mmquicklink/setting_quicklink', $context);
    }
}
