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
 * Settings for the teachers' toolbox.
 *
 * @package   block_mmquicklink
 *
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    global $DB;
    
    $rolearray = [];
    $default_roles = [];
    // Load existing roles from DB.
    $roles = $DB->get_records('role');
    foreach($roles as $role) {
        // Add role to array.
        $rolearray[$role->id] = $role->shortname;
        // Default roles are manager, coursecreator and editingteacher.
        if (in_array($role->shortname, ['manager', 'coursecreator', 'editingteacher'])) {
            $default_roles[$role->id] = $role->shortname;
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

    $default_pagelayouts = ['course','coursecategory','incourse','frontpage','admin','report','mydashboard'];
    
    $settings->add(new admin_setting_heading('block_mmquicklink_role_settings', get_string('setting_roles', 'block_mmquicklink'), ''));
    $settings->add(new admin_setting_configmulticheckbox('mmquicklink/config_roles', get_string('setting_roles', 'block_mmquicklink'), get_string('setting_roles_desc', 'block_mmquicklink'), $default_roles, $rolearray));
    
    $settings->add(new admin_setting_heading('block_mmquicklink_pagelayout_settings', get_string('setting_pagelayouts', 'block_mmquicklink'), ''));
    $settings->add(new admin_setting_configmulticheckbox('mmquicklink/config_pagelayouts', get_string('setting_pagelayouts', 'block_mmquicklink'), get_string('setting_pagelayouts_desc', 'block_mmquicklink'), $default_pagelayouts, $pagelayouts));
        
    $settings->add(new admin_setting_heading('block_mmquicklink_visibility_settings', get_string('visibility_settings', 'block_mmquicklink'), ''));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_reports', get_string('setting_reports', 'block_mmquicklink'), get_string('setting_reports_desc', 'block_mmquicklink'), 0));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_delcourse', get_string('setting_delcourse', 'block_mmquicklink'), get_string('setting_delcourse_desc', 'block_mmquicklink'), 0));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_participants', get_string('setting_participants', 'block_mmquicklink'), get_string('setting_participants_desc', 'block_mmquicklink'), 0));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_course_grades', get_string('setting_course_grades', 'block_mmquicklink'), get_string('setting_course_grades_desc', 'block_mmquicklink'), 0));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_themesettings', get_string('setting_themesettings', 'block_mmquicklink'), get_string('setting_themesettings_desc', 'block_mmquicklink'), 0));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_langcust', get_string('setting_langcust', 'block_mmquicklink'), get_string('setting_langcust_desc', 'block_mmquicklink'), 0));

}