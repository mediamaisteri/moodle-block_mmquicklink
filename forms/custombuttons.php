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
 * Custom buttons form.
 *
 * @package   block_mmquicklink
 * @copyright 2021 Mediamaisteri Oy
 * @author    Mikko Haiku <mikko.haiku@mediamaisteri.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/formslib.php");

class block_mmquicklink_custombuttons_forms extends moodleform {

    const SYSCONTEXTLEVEL = 10;

    /**
     * Define import form.
     */
    public function definition() {
        global $CFG, $DB;
        $mform = $this->_form;

        $mform->addElement('html', "<h5 class='pb-4'>" . get_string('custombuttons', 'block_mmquicklink') . "</h5>");

        $mform->addElement('text', 'description', get_string('description', 'block_mmquicklink'), array("size" => 50));
        $mform->setType('description', PARAM_RAW);

        $mform->addElement('text', 'href', get_string('href', 'block_mmquicklink'), array("size" => 50));
        $mform->setType('href', PARAM_RAW);

        $contexts = array(
            'course' => get_string('course'),
            'other' => get_string('other'),
        );
        $mform->addElement('select', 'context', get_string('context', 'block_mmquicklink'), $contexts);
        $mform->setType('context', PARAM_RAW);

        // Capabilities list. Currently supports only system context capabilities.
        $capabilities = $DB->get_records('capabilities', array('contextlevel' => self::SYSCONTEXTLEVEL), "name");
        $c[] = get_string('choose');
        foreach ($capabilities as $cap) {
            $c[$cap->name] = $cap->name;
        }
        $mform->addElement('select', 'requiredcapability',
        get_string('requiredcapability', 'block_mmquicklink'), $c);
        $mform->setType('requiredcapability', PARAM_RAW);

        // Role list. Currently supports only system roles.
        $roleids = $DB->get_records('role_context_levels', array('contextlevel' => self::SYSCONTEXTLEVEL));
        $roles = $DB->get_records('role');
        $rolelist[] = get_string('choose');
        foreach ($roles as $role) {
            $rr[$role->id] = $role->shortname;
        }
        foreach ($roleids as $roleid) {
            $rolelist[$roleid->roleid] = $rr[$roleid->roleid];
        }

        $mform->addElement('select', 'requiredroleid', get_string('requiredroleid', 'block_mmquicklink'), $rolelist);
        $mform->setType('requiredroleid', PARAM_RAW);

        $mform->addElement('checkbox', 'adminonly', get_string('adminonly', 'block_mmquicklink'));

        $mform->addElement('static', 'variableinfo', null, get_string('variables', 'block_mmquicklink'));

        $this->add_action_buttons(false, get_string('save'));
    }

}
