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
 * Writes the new button order into database.
 *
 * @package    block_mmquicklink
 * @copyright  2019 Mediamaisteri Oy
 * @author     Mikko Haikonen <mikko.haikonen@mediamaisteri.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/my/lib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->libdir.'/filelib.php');

require_login();

// Check if user has capability.
if (has_capability('moodle/category:manage', context_system::instance())) {
    $button = required_param('button', PARAM_RAW);
    $orderid = required_param('order', PARAM_RAW);

    $check = $DB->get_record_sql(
        "SELECT * FROM {block_mmquicklink_sorting} WHERE button = :button",
        array('button' => $button)
    );
    if (count($check) > 0) {
        $btn = new \stdClass();
        $btn->button = $button;
        $btn->sortorder = $orderid;
        $btn->id = $check->id;
        $update = $DB->update_record('block_mmquicklink_sorting', $btn);
    } else {
        $btn = new \stdClass();
        $btn->button = $button;
        $btn->sortorder = $orderid;
        $insert = $DB->insert_record('block_mmquicklink_sorting', $btn);
    }
    // Output something.
    echo $button . "/" . $orderid;
}