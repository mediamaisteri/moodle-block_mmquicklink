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
 * Confirmation box.
 *
 * @package    theme_maisteriboost
 * @copyright  2019 Mediamaisteri Oy
 * @author     Mikko Haikonen <mikko.haikonen@mediamaisteri.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_login();

$PAGE->set_context(context_system::instance());
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_url(new moodle_url('/blocks/mmquicklink/custombuttons.php'));
$PAGE->set_pagelayout('admin');

require_capability('block/mmquicklink:custombuttons', \context_system::instance());

require_once("classes/block_mmquicklink.php");
$mmquicklink = new \block_mmquicklink\mmquicklink();
$id = optional_param("id", null, PARAM_INT);
$action = optional_param("action", null, PARAM_RAW);

if (!$id && !$action) {
    echo $OUTPUT->header();
}

echo $mmquicklink->manage_custombuttons($id, $action);
echo $OUTPUT->footer();
