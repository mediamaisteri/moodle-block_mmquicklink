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
 * @package   block_mmquicklink
 * @copyright 2017 Mediamaisteri Oy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/************************
Admineille, hallinnoijille ja opettajille näkyvä
lohko, jossa on pikalinkkejä näppäriin toimenpiteisiin.
Linkit vaihtuvat dynaamisesti sivun mukaan, eli kursseilla
on eri toimintoja, kuin etusivulla.

Moduulin "Missä tämä lohko näkyy" -asetus tulee olla "Mikä tahansa sivu",
jotta lohko toimii halutulla tavalla.

2017
Mediamaisteri Oy
************************/

defined('MOODLE_INTERNAL') || die();

global $USER;
// Lohkon esittäminen vain admineille, managereille ja opettajille (oletetaan, että role id:itä ei ole muutettu).
if (is_siteadmin() OR user_has_role_assignment($USER->id, 1, context_system::instance()->id) OR
user_has_role_assignment($USER->id, 2, context_system::instance()->id)) {

    class block_mmquicklink extends block_base {
        public function init() {
            $this->title = get_string('title', 'block_mmquicklink');
        }

        public function instance_allow_multiple() {
            return false;
        }

        public function html_attributes() {
            $attributes = parent::html_attributes();
            if ($this->instance_can_be_docked() && get_user_preferences('docked_block_instance_'.$this->instance->id, 1)) {
                // Telakoidaan lohko defaulttina. Jos käyttäjä muuttaa telakointia, muutokset overridaa tämän.
                $attributes['class'] .= ' dock_on_load';
            }
            return $attributes;
        }

        public function get_content() {
            if ($this->content !== null) {
                return $this->content;
            }

            $this->page->requires->js_call_amd('block_mmquicklink/enrolmentdiv','init', []);

            $this->content = new stdClass;

            global $PAGE, $CFG, $USER, $COURSE; // Load required globals.
            $this->content->text = ""; // Set variable.

            // Tämän alle linkit, jotka löytyvät kurssisivulta.
            if ($PAGE->pagelayout == 'course' || $PAGE->pagelayout == 'incourse' || $PAGE->pagelayout == 'report' ||
            $PAGE->pagetype == 'course-view-topics') {

                // Muokkaustila päälle tai pois -painike. Ensin tarkistetaan onko oikeuksia asiaan.
                if (has_capability('moodle/course:update', context_system::instance())) {
                    if ($PAGE->user_is_editing()) {
                        $editingmode = "off";
                        $editingmodestring = get_string("turneditingoff");
                    } else {
                        $editingmode = "on";
                        $editingmodestring = get_string("turneditingon");
                    }
                    $this->content->text .= "<li class='list'><a href='" .
                        new moodle_url($CFG->wwwroot . "/course/view.php?id=1&edit=" . $editingmode .
                        "&sesskey=" . $USER->sesskey) . "'>" . $editingmodestring . "</a>";
                }

                // Piilota/Näytä kurssi -painike.
                if (has_capability('moodle/course:visibility', context_system::instance())) {
                    if ($COURSE->visible == "1") {
                        $this->content->text .= "<li class='list'><a href='" .
                            new moodle_url($CFG->wwwroot . "/blocks/mmquicklink/changevisibility.php?hide=1&sesskey=" .
                            $USER->sesskey . "&id=" . $COURSE->id) . "'>" . get_string('hide') . " " .
                            strtolower(get_string('course')) . "</a>";
                    } else {
                        $this->content->text .= "<li class='list'><a href='" .
                            new moodle_url($CFG->wwwroot . "/blocks/mmquicklink/changevisibility.php?hide=0&sesskey=" .
                            $USER->sesskey . "&id=" . $COURSE->id) . "'>" . get_string('show') . " " .
                            strtolower(get_string('course')) . "</a>";
                    }
                }

                // Näytetään kurssiavaimenluontipainike, jos kurssinmuokkaukseen on oikeus.
                if (has_capability('moodle/course:update', context_system::instance())) {
                    $this->content->text .= "<li class='list mmquicklink-enrolmentkey'><a href='#'>" . get_string('set', 'portfolio_flickr') . " " .
                        strtolower(get_string('password', 'enrol_self')) . "</a></li>
                        <div class='mmquicklink-enrolmentkey-div'>
                            <form method='get' action='" . $CFG->wwwroot . "/blocks/mmquicklink/setenrolmentkey.php'>
                            <input type='hidden' name='courseid' value='" . $COURSE->id . "'>
                            <input type='text' name='enrolmentkey'> <input type='submit' value='" . get_string('save', 'core_admin') . "'>
                        </div>";
                }
                // Osallistujat -sivu.
                if (has_capability('moodle/course:viewparticipants', context_system::instance())) {
                    $this->content->text .= "<li class='list'><a href='" . new moodle_url($CFG->wwwroot .
                    "/user/index.php?id=" . $PAGE->course->id) . "'>" .
                        get_string('show') . " " . strtolower(get_string('participants')) . "</a></li>";
                }
                // Kurssin arvioinnit -sivut.
                if (has_capability('mod/assign:grade', context_system::instance())) {
                    $this->content->text .= "<li class='list'><a href='" . new moodle_url($CFG->wwwroot .
                    "/grade/report/grader/index.php?id=" . $PAGE->course->id) . "'>" . get_string('coursegrades') . "</a></li>";
                }

            } else {
                // Tähän alle linkit, jotka löytyvät etusivulta.

                // Muokkaustila päälle tai pois -painike. Ensin tarkistetaan onko oikeuksia asiaan.
                if (is_siteadmin() OR user_has_role_assignment($USER->id, 1, context_system::instance()->id)) {
                    if ($PAGE->user_is_editing()) {
                        $editingmode = "off";
                        $editingmodestring = get_string("turneditingoff");
                    } else {
                        $editingmode = "on";
                        $editingmodestring = get_string("turneditingon");
                    }
                    $this->content->text .= "<li class='list'><a href='" .
                        new moodle_url($CFG->wwwroot . "/course/view.php?id=1&edit=" . $editingmode .
                        "&sesskey=" . $USER->sesskey) . "'>" . $editingmodestring . "</a>";
                }

                // Näytetään kurssinlisäyspainike, jos siihen on oikeuksia.
                if (has_capability('moodle/course:create', context_system::instance())) {
                    $this->content->text .= "<li class='list'><a href='" .
                        new moodle_url($CFG->wwwroot . "/course/edit.php?category=1") . "'>".
                        get_string('addnewcourse') . "</a></li>";
                }
                // Näytetään kurssienhallintalinkki, jos sellaiseen on oikeudet.
                if (has_capability('moodle/category:manage', context_system::instance())) {
                    $this->content->text .= "<li class='list'><a href='" .
                        new moodle_url($CFG->wwwroot . "/course/management.php") . "'>".
                        get_string('coursecatmanagement') . "</a></li>";
                }
                // Etusivun asetukset -linkki näytetään kaikille, joille lohko näkyy.
                $this->content->text .= "<li class='list'><a href='" .
                    new moodle_url($CFG->wwwroot . "/admin/settings.php?section=frontpagesettings") . "'>" .
                    get_string('frontpagesettings') . "</a></li>";

            }

            return $this->content;
        }
    }

}
