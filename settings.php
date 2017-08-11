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
    
    // Load existing roles from DB.
    $roles = $DB->get_records('role');
    $settings->add(new admin_setting_heading('block_mmquicklink_role_settings', get_string('setting_roles', 'block_mmquicklink'), ''));
    foreach($roles as $role) {
        if($role->id <= 3) {
            $defanswer = 1;
        } else {
            $defanswer = 0;
        }
        // Add new checkbox for every role.
        $settings->add(new admin_setting_configcheckbox('mmquicklink/config_roleid_' . $role->id, $role->shortname, '', $defanswer));
    }
    
    $settings->add(new admin_setting_heading('block_mmquicklink_visibility_settings', get_string('visibility_settings', 'block_mmquicklink'), ''));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_reports', get_string('setting_reports', 'block_mmquicklink'), get_string('setting_reports_desc', 'block_mmquicklink'), 0));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_delcourse', get_string('setting_delcourse', 'block_mmquicklink'), get_string('setting_delcourse_desc', 'block_mmquicklink'), 0));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_participants', get_string('setting_participants', 'block_mmquicklink'), get_string('setting_participants_desc', 'block_mmquicklink'), 0));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_course_grades', get_string('setting_course_grades', 'block_mmquicklink'), get_string('setting_course_grades_desc', 'block_mmquicklink'), 0));
    $settings->add(new admin_setting_configcheckbox('mmquicklink/config_hide_themesettings', get_string('setting_themesettings', 'block_mmquicklink'), get_string('setting_themesettings_desc', 'block_mmquicklink'), 0));

}