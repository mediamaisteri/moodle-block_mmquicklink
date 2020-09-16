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
 * MM Quick Link upgrade script.
 *
 * @package block_mmquicklink
 * @copyright 2019 Mediamaisteri Oy
 * @author Mikko Haikonen <mikko.haikonen@mediamaisteri.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Upgrading the plugin.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool always true
 */
function xmldb_block_mmquicklink_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2019010720) {

        // Define table local_recomplete_user_config to be created.
        $table = new xmldb_table('block_mmquicklink_sorting');

        // Adding fields to table local_recomplete_user_config.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('button', XMLDB_TYPE_TEXT, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('order', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_recomplete_user_config.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for local_recomplete_user_config.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Recomplete savepoint reached.
        upgrade_plugin_savepoint(true, 2019010720, 'block', 'mmquicklink');

    }

    if ($oldversion < 2020091600) {

        // Rename field order on table block_mmquicklink_sorting to sortorder.
        $table = new xmldb_table('block_mmquicklink_sorting');
        $field = new xmldb_field('order', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'button');

        // Launch rename field sortorder.
        $dbman->rename_field($table, $field, 'sortorder');

        // Mmquicklink savepoint reached.
        upgrade_block_savepoint(true, 2020091600, 'mmquicklink');
    }

    return true;
}
