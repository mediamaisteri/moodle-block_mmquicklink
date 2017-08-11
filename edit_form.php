<?php

class block_mmquicklink_edit_form extends block_edit_form {

    protected function specific_definition($mform) {

        // Commented out because settings are now global.

        // Set settings header.
        /* $mform->addElement('header', 'configheader', get_string('visibility_settings', 'block_mmquicklink')); */

        // Render setting checkboxes.
        /*$mform->addElement('advcheckbox', 'config_hide_reports', get_string('setting_reports', 'block_mmquicklink'), get_string('setting_reports_desc', 'block_mmquicklink'));
        $mform->addElement('advcheckbox', 'config_hide_delcourse', get_string('setting_delcourse', 'block_mmquicklink'), get_string('setting_delcourse_desc', 'block_mmquicklink'));
        $mform->addElement('advcheckbox', 'config_hide_participants', get_string('setting_participants', 'block_mmquicklink'), get_string('setting_participants_desc', 'block_mmquicklink'));
        $mform->addElement('advcheckbox', 'config_hide_course_grades', get_string('setting_course_grades', 'block_mmquicklink'), get_string('setting_course_grades_desc', 'block_mmquicklink'));
        $mform->addElement('advcheckbox', 'config_hide_themesettings', get_string('setting_themesettings', 'block_mmquicklink'), get_string('setting_themesettings_desc', 'block_mmquicklink')); */

    }

}
