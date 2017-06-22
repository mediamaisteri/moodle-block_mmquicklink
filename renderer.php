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
 * Render local_reports navigation.
 */
/**
 * @package   block_mmquicklink
 * @copyright 2017 Mediamaisteri Oy
 */

defined('MOODLE_INTERNAL') || die();

class block_mmquicklink_renderer extends plugin_renderer_base {

    public function mmquicklink_tree($navigation) {
        global $PAGE;
        $content = $this->mmquicklink_node($navigation->children);
        if (isset($navigation->id) && !is_numeric($navigation->id) && !empty($content)) {
            $content = $this->output->box($content, 'block_tree_box', $navigation->id);
        }
        return $content;
    }

    protected function mmquicklink_node($items) {
        $itemlist = "<ul class='mmquicklink-reports'>";

        foreach ($items as $item) {
            $itemtext  = $item->text;
            $itemurl   = $item->action;
            $itemlist .= "<li class='list'><a class='btn btn-secondary' href='" . $itemurl . "'>" . $itemtext . "</a></li>";
        }

        $itemlist .= "</ul>";

        return $itemlist;
    }

}