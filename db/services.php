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
 * Web service functions for MM Quicklink Block.
 *
 * @package   block_mmquicklink
 * @copyright 2023 Mediamaisteri Oy
 * @author    Rosa Siuruainen <rosa.siuruainen@mediamaisteri.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$services = array(
    'block_mmquicklink' => array(
    'functions' => array(
        'block_mmquicklink_update_sort',
    ),
    'requiredcapability' => '',
    'restrictedusers' => 0,
    'enabled' => 1,
    )
);


$functions = [
    'block_mmquicklink_update_sorting' => [
        'classname'   => 'block_mmquicklink_external',
        'methodname'  => 'update_sorting',
        'classpath'   => 'blocks/mmquicklink/externallib.php',
        'description' => 'Updates button sorting.',
        'type'        => 'write',
        'ajax'        => true,
    ],
];
