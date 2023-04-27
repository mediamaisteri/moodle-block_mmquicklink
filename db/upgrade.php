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

    global $DB, $CFG;
    require_once($CFG->dirroot . '/blocks/mmquicklink/lib.php');

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

    if ($oldversion < 2021082600) {

        // Define table block_mmquicklink_custombutt to be created.
        $table = new xmldb_table('block_mmquicklink_custombutt');

        // Adding fields to table block_mmquicklink_custombutt.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('href', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('context', XMLDB_TYPE_CHAR, '1333', null, null, null, null);
        $table->add_field('requiredcapability', XMLDB_TYPE_CHAR, '1333', null, null, null, null);
        $table->add_field('requiredroleid', XMLDB_TYPE_CHAR, '1333', null, null, null, null);
        $table->add_field('adminonly', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table block_mmquicklink_custombutt.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);

        // Conditionally launch create table for block_mmquicklink_custombutt.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Mmquicklink savepoint reached.
        upgrade_block_savepoint(true, 2021082600, 'mmquicklink');
    }

    if ($oldversion < 2023030601) {
        // Define table block_mmquicklink_sorting to be dropped.
        $table = new xmldb_table('block_mmquicklink_sorting');

        // Conditionally launch drop table for block_mmquicklink_sorting.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table block_mmquicklink_sorting to be created.
        $table = new xmldb_table('block_mmquicklink_sorting');

        // Adding fields to table block_mmquicklink_sorting.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('button', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('parent', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table block_mmquicklink_sorting.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for block_mmquicklink_sorting.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Mmquicklink savepoint reached.
        upgrade_block_savepoint(true, 2023030601, 'mmquicklink');

    }

    if ($oldversion < 2023042600) {

        $table = new xmldb_table('block_mmquicklink_sorting');
        if ($dbman->table_exists($table)) {
            $DB->execute("UPDATE {block_mmquicklink_sorting} SET parent = 'main-list'");
        }

        // Mmquicklink savepoint reached.
        upgrade_block_savepoint(true, 2023042600, 'mmquicklink');

    }

    return true;
}
