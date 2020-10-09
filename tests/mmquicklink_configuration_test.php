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
 * MM Quicklink testcase
 *
 * @package   block_mmquicklink
 * @category  test
 * @copyright 2020 Mediamaisteri Oy
 * @author    Mikko Haikonen <mikko.haikonen@mediamaisteri.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_mmquicklink_configuration_test extends advanced_testcase {

    /**
     * Test getting block configuration
     */
    public function test_configuration() {
        global $CFG, $OUTPUT;
        require_once($CFG->dirroot . '/my/lib.php');

        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();

        // Force a setting change to check the returned blocks settings.
        set_config('config_roles', '0,1,2,3,4,5,6,7,8,9,10', 'block_mmquicklink');
        set_config('config_pagelayouts', '0,1,2,3,4,5,6,7', 'block_mmquicklink');

        $this->setUser($user);
        $context = context_system::instance();

        if (!$currentpage = my_get_page($user->id, MY_PAGE_PRIVATE)) {
            throw new moodle_exception('mymoodlesetup');
        }

        $this->page->set_url('/my/index.php');
        $this->page->set_context($context);
        $this->page->set_pagelayout('mydashboard');
        $this->page->set_pagetype('my-index');
        $this->page->blocks->add_region('content');
        $this->page->set_subpage($currentpage->id);

        // Load the block instances for all the regions.
        $this->page->blocks->load_blocks();
        $this->page->blocks->create_all_block_instances();

        $blocks = $this->page->blocks->get_content_for_all_regions($OUTPUT);
        $configs = null;
        foreach ($blocks as $region => $regionblocks) {
            $regioninstances = $this->page->blocks->get_blocks_for_region($region);

            foreach ($regioninstances as $ri) {
                // Look for mmquicklink block only.
                if ($ri->instance->blockname == 'mmquicklink') {
                    $hasaccess = $ri->hasaccess();
                    $configs = $ri->get_config_for_external();
                }
            }
        }

        // Test we receive all we expect (exact number and values of settings).
        $this->assertNotEmpty($configs);
        $this->assertEmpty((array) $configs->instance);
        $this->assertCount(19, (array) $configs->plugin);

        $this->assertEquals('0,1,2,3,4,5,6,7', $configs->plugin->config_pagelayouts);
        $this->assertEquals((int)get_config('block_mmquicklink', 'version'), $configs->plugin->version);

    }
}