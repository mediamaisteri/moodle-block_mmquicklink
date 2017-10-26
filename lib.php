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
 * mmquicklink lib
 *
 * @package   block_mmquicklink
 * @copyright 2017 Mediamaisteri Oy
 */

defined('MOODLE_INTERNAL') || die();

function mmquicklink_get_switched_role($user, $page, $options = array()) {
    global $OUTPUT, $DB, $SESSION, $CFG, $COURSE;

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