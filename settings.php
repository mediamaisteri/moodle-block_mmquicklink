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
 * Settings for MM Quicklink Block.
 *
 * @package   block_mmquicklink
 * @copyright 2019 Mediamaisteri Oy
 * @author    Mikko Haikonen <mikko.haikonen@mediamaisteri.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
if ($ADMIN->fulltree) {
    $PAGE->requires->js_call_amd('block_mmquicklink/jquery-sortable', 'init', []);
    global $DB;

    // Load course categories from DB.
    $categoryarray[0] = get_string('choose');
    $categories = $DB->get_records('course_categories', array(), $sort = 'path');
    foreach ($categories as $category) {
        if ($category->parent > 0) {
            $category->name = " - " . $category->name;
        }
        $categoryarray[$category->id] = $category->name;
    }

    $rolearray = [];
    $defaultroles = [];
    // Load existing roles from DB.
    $roles = $DB->get_records('role');
    foreach ($roles as $role) {
        // Add role to array.
        $rolearray[$role->id] = $role->shortname;
        // Default roles are manager, coursecreator and editingteacher.
        if (in_array($role->shortname, ['manager', 'coursecreator', 'editingteacher'])) {
            $defaultroles[$role->id] = $role->shortname;
        }
    }

    $pagelayouts = ['base',
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

    $defaultpagelayouts = ['course', 'coursecategory', 'incourse', 'frontpage', 'admin', 'report', 'mydashboard', 'base'];

    // General settings.
    $settings->add(new admin_setting_heading('block_mmquicklink_general_settings',
    get_string('setting_general', 'block_mmquicklink'), ''));
    $settings->add(new admin_setting_configtext('mmquicklink/config_blocktitle', get_string('setting_blocktitle',
    'block_mmquicklink'), get_string('setting_blocktitle_desc', 'block_mmquicklink'), ''));

    // Role settings.
    $settings->add(new admin_setting_heading('block_mmquicklink_role_settings',
    get_string('setting_roles', 'block_mmquicklink'), ''));
    $settings->add(new admin_setting_configmulticheckbox('mmquicklink/config_roles', get_string('setting_roles',
    'block_mmquicklink'), get_string('setting_roles_desc', 'block_mmquicklink'), $defaultroles, $rolearray));

    // Pagelayout settings.
    $settings->add(new admin_setting_heading('block_mmquicklink_pagelayout_settings',
    get_string('setting_pagelayouts', 'block_mmquicklink'), ''));
    $settings->add(new admin_setting_configmulticheckbox('mmquicklink/config_pagelayouts',
    get_string('setting_pagelayouts', 'block_mmquicklink'), get_string('setting_pagelayouts_desc',
    'block_mmquicklink'), $defaultpagelayouts, $pagelayouts));

    // Visibility settings.
    $settings->add(new admin_setting_heading('block_mmquicklink_visibility_settings',
    get_string('visibility_settings', 'block_mmquicklink'), ''));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_reports',
    get_string('setting_reports', 'block_mmquicklink'), get_string('setting_reports_desc', 'block_mmquicklink'), 0));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_competencereport',
    get_string('setting_competencereport', 'block_mmquicklink'),
    get_string('setting_competencereport_desc', 'block_mmquicklink'), 0));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_hidecourse',
    get_string('setting_hidecourse', 'block_mmquicklink'), get_string('setting_hidecourse_desc', 'block_mmquicklink'), 0));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_delcourse',
    get_string('setting_delcourse', 'block_mmquicklink'), get_string('setting_delcourse_desc', 'block_mmquicklink'), 0));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_archive',
    get_string('setting_archive', 'block_mmquicklink'), get_string('setting_archive_desc', 'block_mmquicklink'), 0));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_editsettings',
    get_string('setting_editsettings', 'block_mmquicklink'), get_string('setting_editsettings_desc', 'block_mmquicklink'), 0));

    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_activityprogress',
    get_string('setting_activityprogresssettings', 'block_mmquicklink'),
    get_string('setting_activityprogresssettings_desc', 'block_mmquicklink'), 1));

    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_coursecompletionsettings',
    get_string('setting_coursecompletionsettings', 'block_mmquicklink'),
    get_string('setting_coursecompletionsettings_desc', 'block_mmquicklink'), 1));

    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_participants',
    get_string('setting_participants', 'block_mmquicklink'), get_string('setting_participants_desc', 'block_mmquicklink'), 0));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_course_grades',
    get_string('setting_course_grades', 'block_mmquicklink'), get_string('setting_course_grades_desc', 'block_mmquicklink'), 0));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_themesettings',
    get_string('setting_themesettings', 'block_mmquicklink'), get_string('setting_themesettings_desc', 'block_mmquicklink'), 0));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_langcust',
    get_string('setting_langcust', 'block_mmquicklink'), get_string('setting_langcust_desc', 'block_mmquicklink'), 0));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_otherrole',
    get_string('setting_otherrole', 'block_mmquicklink'), get_string('setting_otherrole_desc', 'block_mmquicklink'), 0));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_local_reports_summary',
    get_string('setting_localreportssummary', 'block_mmquicklink'),
    get_string('setting_localreportssummary_desc', 'block_mmquicklink'), 1));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_coursebgimagechanger',
    get_string('setting_coursebgimagechanger', 'block_mmquicklink'),
    get_string('setting_coursebgimagechanger_desc', 'block_mmquicklink'), 1));

    // Question bank & categories & backup.
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_questionbank',
    get_string('setting_questionbank', 'block_mmquicklink'),
    get_string('setting_questionbank_desc', 'block_mmquicklink'), 1));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_questioncategory',
    get_string('setting_questioncategory', 'block_mmquicklink'),
    get_string('setting_questioncategory_desc', 'block_mmquicklink'), 1));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_backup',
    get_string('setting_backup', 'block_mmquicklink'), get_string('setting_backup_desc', 'block_mmquicklink'), 1));

    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_easylink',
    get_string('setting_easylink', 'block_mmquicklink'),
    get_string('setting_easylink_desc', 'block_mmquicklink'), 0));
    $settings->add(new admin_setting_configtext('mmquicklink/config_allowedcategories', get_string('setting_allowedcategories',
    'block_mmquicklink'), get_string('setting_allowedcategories_desc', 'block_mmquicklink'), ''));

    // Advanced options.
    $settings->add(new admin_setting_heading('block_mmquicklink_advanced_options',
    get_string('advanced_options', 'block_mmquicklink'), ''));

    // Show participation select only on pre 3.5 versions.
    if ($CFG->version < 2018051700.00) {
        $settings->add(new admin_setting_configselect('mmquicklink/config_participants_select',
        get_string('setting_participants_select', 'block_mmquicklink'),
        get_string('setting_participants_select_desc', 'block_mmquicklink'), '0',
        array(0 => get_string('setting_participants_users', 'block_mmquicklink'),
        1 => get_string('setting_participants_enrol', 'block_mmquicklink'))));
    }

    $settings->add(new admin_setting_configselect('mmquicklink/config_defaultcategory',
    get_string('setting_defaultcategory', 'block_mmquicklink'),
    get_string('setting_defaultcategory_desc', 'block_mmquicklink'), '0',
    $categoryarray));

    $settings->add(new admin_setting_configselect('mmquicklink/config_otherrole_select',
    get_string('setting_otherrole_select', 'block_mmquicklink'),
    get_string('setting_otherrole_select_desc', 'block_mmquicklink'), '5',
    $rolearray));

    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_unique_enrolmentkey',
    get_string('setting_unique_enrolmentkey', 'block_mmquicklink'),
    get_string('setting_unique_enrolmentkey_desc', 'block_mmquicklink'), 0));

    $settings->add(new admin_setting_description(
        'mmquicklink/config_custombuttons',
        get_string('custombuttons', 'block_mmquicklink'),
        get_string('custombuttons_desc', 'block_mmquicklink')
    ));

    // Sorting.
    $settings->add(new admin_setting_heading('block_mmquicklink_sorting_options',
    get_string('sorting_options', 'block_mmquicklink'), ''));

    $settings->add(new admin_setting_configquicklinksort('mmquicklink/sorting',
    '',
    '', '',
    ''));


}
