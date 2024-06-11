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
 * Web service function for MM Quicklink Block.
 *
 * @package   block_mmquicklink
 * @copyright 2023 Mediamaisteri Oy
 * @author    Rosa Siuruainen <rosa.siuruainen@mediamaisteri.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_mmquicklink_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function update_sorting_parameters() {
        return new external_function_parameters([
            "parent" => new external_value(PARAM_TEXT, 'parent list id'),
            "children" => new external_multiple_structure(new external_value(PARAM_TEXT, 'button ids')),
        ]);
    }

    /**
     * Create groups
     * @param array $groups array of group description arrays (with keys groupname and courseid)
     * @return array of newly created groups
     */
    public static function update_sorting($parent, $children) {
        global $CFG, $DB;

        $params = self::validate_parameters(self::update_sorting_parameters(), ['parent' => $parent, 'children' => $children]);

        $order = 1;
        foreach ($children as $child) {

            $data = (object) [
                "button" => $child,
                "parent" => $parent,
                "sortorder" => $order
            ];
            $recordexists = $DB->get_record('block_mmquicklink_sorting', ['button' => $child]);
            if ($recordexists) {
                $data->id = $recordexists->id;
                $DB->update_record('block_mmquicklink_sorting', $data);
            } else {
                $DB->insert_record('block_mmquicklink_sorting', $data);
            }
            $order++;
        }
        return true;
    }

    public static function update_sorting_returns() {
        return new external_value(PARAM_BOOL, 'success');
    }

}
