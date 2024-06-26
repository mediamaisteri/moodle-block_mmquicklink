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
 * MM Quick Link installation file.
 *
 * @package block_mmquicklink
 * @copyright 2019 Mediamaisteri Oy
 * @author Mikko Haikonen <mikko.haikonen@mediamaisteri.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * After installation script.
 * Adds the block automatically on frontpage and all other pages.
 * Sets mmquicklink also as an undeletable block.
 *
 * @return boolean always true
 */
function xmldb_block_mmquicklink_install() {
    global $CFG, $DB, $PAGE;

    $dbman = $DB->get_manager();

    // Create the block instance object.
    $instance = new stdClass();
    $instance->blockname = "mmquicklink";
    $instance->parentcontextid = 1;
    $instance->showinsubcontexts = 1;
    $instance->requiredbytheme = 0;
    $instance->pagetypepattern = "*";
    $instance->subpagepattern = null;
    $instance->defaultregion = "side-pre";
    $instance->defaultweight = -10;
    $instance->configdata = "Tzo4OiJzdGRDbGFzcyI6MDp7fQ==";
    $instance->timecreated = time();
    $instance->timemodified = time();

    // Insert a record into database.
    $insert = $DB->insert_record('block_instances', $instance, false);

    // Set mmquicklink as an undeletable block.
    require_once($CFG->dirroot . "/lib/blocklib.php");
    require_once($CFG->dirroot . "/blocks/mmquicklink/lib.php");
    $page = $PAGE;
    $blockmanager = new block_manager($page);
    $undeletableblocktypes = $blockmanager->get_undeletable_block_types();
    if (!in_array("mmquicklink", $undeletableblocktypes)) {
        $undeletableblocktypes[] = "mmquicklink";
        set_config('undeletableblocktypes', implode(',', $undeletableblocktypes));
    }

    return true;
}
