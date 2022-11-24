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
 * Form for editing HTML block instances.
 *
 * @package   block_mmquicklink
 * @copyright 2017 Mediamaisteri Oy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'MM Quicklink';
$string['title'] = "Managing tools";
$string['coursemgmt'] = "Manage courses/categories";
$string['visibility_settings'] = 'Visibility settings';

$string['setting_reports'] = "Reports";
$string['setting_reports_desc'] = "Check to hide reports link";
$string['setting_competencereport'] = "Competence report";
$string['setting_competencereport_desc'] = "Check to hide competence report link under mReports";

$string['setting_participants'] = "Participants";
$string['setting_participants_desc'] = "Check to hide course participants link";

$string['setting_course_grades'] = "Course grades";
$string['setting_course_grades_desc'] = "Check to hide course grades link";

$string['setting_themesettings'] = "Theme settings";
$string['setting_themesettings_desc'] = "Check to hide theme settings link";
$string['addcourse'] = "Add course";
$string['setting_delcourse'] = "Delete course";
$string['setting_delcourse_desc'] = "Check to hide delete course link";
$string['setting_hidecourse'] = "Show/hide course";
$string['setting_hidecourse_desc'] = "Check to hide show/hide course link";
$string['setting_easylink'] = "Easylink";
$string['setting_easylink_desc'] = "Check to hide easylink button.";

$string['setting_archive'] = "Archive course";
$string['setting_archive_desc'] = "Check to hide 'archive course' button. This functionality required local_course_archive -plugin to be installed.";
$string['archive_course'] = "Move course to trashbin";
$string['archived'] = "The course has been moved to trashbin.";

$string['mmquicklink:addinstance'] = "Add a new Quicklink block";
$string['mmquicklink:myaddinstance'] = "Add a new Quicklink block on dashboard";

$string['setting_roles'] = "Approved roles";
$string['setting_roles_desc'] = "Check the roles which can see the block.";

$string['setting_langcust'] = "Language customization";
$string['setting_langcust_desc'] = "Check to hide language customization link";

$string['emptyblock'] = "On this page your role's permissions are not sufficient.";

$string['coursegrades'] = "Course grading";
$string['hide_course'] = "Hide course";
$string['show_course'] = "Show course";
$string['delete_course'] = "Delete course";

$string['setting_pagelayouts'] = "Pagelayouts";
$string['setting_pagelayouts_desc'] = "Check the pagelayouts which can show the block.";

$string['setting_editsettings'] = "Edit course settings";
$string['setting_editsettings_desc'] = "Check to hide 'edit course settings' link";

$string['setting_coursecompletionsettings'] = "Course completion settings";
$string['setting_coursecompletionsettings_desc'] = "Check to hide 'Edit course completion setting' link";

$string['setting_activityprogresssettings'] = "Course progress report";
$string['setting_activityprogresssettings_desc'] = "Check to hide 'Activity completion' link";

$string['setting_general'] = "General settings";
$string['setting_blocktitle'] = "Block title";
$string['setting_blocktitle_desc'] = "If title is unset, the block uses default title.";

$string['advanced_options'] = "Advanced options";
$string['setting_participants_select'] = "Participants link";
$string['setting_participants_select_desc'] = "Choose where the participants link should redirect.";
$string['setting_participants_users'] = "General participants page";
$string['setting_participants_enrol'] = "User enrolment page";
$string['enrolmentkey'] = "Enrolment key";
$string["trainingmanagement"] = "Training management";
$string['sorting_options'] = "Button sorting";
$string['switchrole'] = "View as";
$string['setting_otherrole_select'] = "View as role";
$string['setting_otherrole_select_desc'] = "Select a role used with 'View as' link.";
$string['setting_otherrole'] = "Switch roles";
$string['setting_otherrole_desc'] = "Check to hide switch roles link.";
$string['setting_defaultcategory'] = "Default category for new courses";
$string['setting_defaultcategory_desc'] = "Please select the default category for new courses.";
$string['setting_localreportssummary'] = "mReports on course page";
$string['setting_localreportssummary_desc'] = "Check to hide mRaportointi link on course page";
$string['local_reports_summary'] = "Summary report";
$string['setting_coursebgimagechanger'] = "Course background";
$string['setting_coursebgimagechanger_desc'] = "Check to hide 'course background' button.";
$string['coursebgimagechanger'] = "Course background";
$string['setting_allowedcategories'] = "Allowed categories where easylink button is displayed";
$string['setting_allowedcategories_desc'] = "Use top level categories only e.g. 1,2,3. Affects to top level category subcategories.";
$string['setting_unique_enrolmentkey'] = "Unique enrolment keys";
$string['setting_unique_enrolmentkey_desc'] = "Check to use unique enrolment keys";

$string['toomanyselfenrolments'] = 'This course has multiple self enrolment methods enabled. To use this functionality, remove extra self enrolment methods at ' . "<a href='" . '{$a}' . "'>" . '{$a}' . "</a>.";
$string['multiplepasswords'] = "This course has multiple self enrolments enabled. Check them on 'enrolment methods' page.";
$string['privacy:null_reason'] = "This plugin does not handle or store user data.";

$string['buttonsorting'] = "Drag and drop buttons to sort them.";
$string['clicktoreset'] = "Click to reset sorting";
$string['saved'] = "Changes have been succesfully saved!";
$string['saving'] = "Saving changes...";

$string['fromtemplate'] = "Add from a template";
$string['areyousure'] = "Are you sure you want to archive the following course";
$string['areyousurehide1'] = "Are you sure you want to hide the following course";
$string['areyousurehide0'] = "Are you sure you want to show the following course";
$string['hide1'] = "Hide course";
$string['hide0'] = "Show course";

$string['delete_course_modal_body'] = "Are you sure you want to delete the following course";
$string['delete_course_modal_title'] = "Delete course";
$string['delete_course_failed_msg'] = "Delete course failed";
$string['delete_course_success_msg'] = "Delete course success";

// Question bank & category.
$string['questionbank'] = "Question bank";
$string['setting_questionbank'] = "Question bank";
$string['setting_questionbank_desc'] = "Check to hide 'question bank' button.";

$string['questioncategory'] = "Question categories";
$string['setting_questioncategory'] = "Question categories";
$string['setting_questioncategory_desc'] = "Check to hide 'question categories' button.";

$string['backup'] = "Backup";
$string['setting_backup'] = "Backup";
$string['setting_backup_desc'] = "Check to hide 'Backup' button.";

$string['custombuttons'] = "Custom buttons";
$string['custombuttons_desc'] = "Click <a href='../blocks/mmquicklink/custombuttons.php'>here</a> to manage customized buttons.";
$string['href'] = "Link address";
$string['context'] = "Visibility";
$string['requiredcapability'] = "Required capability";
$string['requiredroleid'] = "Required role";
$string['adminonly'] = "Site administrators only";
$string['ok'] = "Action was successfully completed!";
$string['description'] = "Link text";
$string['variables'] = "You can use the following variables in link address & text:<br>
Course ID: {{id}}<br>
Context ID: {{contextid}}<br>
User ID: {{userid}}";

$string['enrolmentkey_reserved'] = "Enrolment key already in use on another course!";

$string['setting_restore'] = "Restore course";
$string['setting_restore_desc'] = 'Check to hide "Restore course from archive" button. This functionality requires local_course_archive -plugin to be installed.';
$string['restorecourse'] = "Restore course from archive";
$string['restorecourse_confirm'] = "Are you sure you want to restore the following course";
$string['notarchived'] = 'Course is not in archive or delete category';
$string['restored_restorecat'] = "Course will be restored to the restore category since its original category is unknown. Restore category is:";
$string['norestorecategory'] = "Course should be restored to the restore category since its original category is unknown. Restore category is not set in the course archive settings";
$string['restored_originalcat'] = "Course will be restored to its original category of: ";
$string['restored'] = "Course has been restored succesfully.";

$string['mmquicklink:custombuttons'] = "Capability to add custom buttons";
