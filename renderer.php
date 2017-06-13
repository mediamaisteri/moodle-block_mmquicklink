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
 * This file contains classes used to manage the navigation structures in Moodle
 * and was introduced as part of the changes occuring in Moodle 2.0
 *
 * @since     Moodle 2.0
 * @package   block_navigation
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * The global navigation tree block class
 *
 * Used to produce the global navigation block new to Moodle 2.0
 *
 * @package   block_navigation
 * @category  navigation
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mmquicklink_renderer extends plugin_renderer_base {

    public function mmquicklink_tree($navigation) {
        global $PAGE;
        //$navigation->add_class('navigation_node');

        $content = $this->mmquicklink_node($navigation->children);
        if (isset($navigation->id) && !is_numeric($navigation->id) && !empty($content)) {
            $content = $this->output->box($content, 'block_tree_box', $navigation->id);
        }
        return $content;
    }

    protected function mmquicklink_node($items) {
        $itemlist = "<ul id='mmquicklink-reports'>";

        foreach($items as $item) {
            $item_text  = $item->text;
            $item_url   = $item->action;

            $itemlist .= "<li class='list'><a class='btn btn-secondary' href='" . $item_url . "'>" . $item_text . "</a></li>";
        }

        $itemlist .= "</ul>";

        return $itemlist;
    }

}
