<?php
/************************
Admineille, hallinnoijille ja opettajille näkyvä
lohko, jossa on pikalinkkejä näppäriin toimenpiteisiin.
Linkit vaihtuvat dynaamisesti sivun mukaan, eli kursseilla
on eri toimintoja, kuin etusivulla.

"Missä tämä lohko näkyy" -asetus tulee olla "Mikä tahansa sivu",
jotta lohko toimii halutulla tavalla.

2017
Mediamaisteri Oy
************************/

global $USER;
// Lohkon esittäminen vain admineille, managereille ja opettajille (oletetaan, että role id:itä ei ole muutettu).
if(is_siteadmin() OR user_has_role_assignment($USER->id, 1, context_system::instance()->id) OR user_has_role_assignment($USER->id, 2, context_system::instance()->id)) {

    class block_mmquicklink extends block_base {
        public function init() {
            $this->title = get_string('tools','core_admin');
        }

        public function instance_allow_multiple() {
          return false;
        }

        public function html_attributes() {
            $attributes = parent::html_attributes(); // Get default values.
            if ($this->instance_can_be_docked() && get_user_preferences('docked_block_instance_'.$this->instance->id, 1)) {
                // Telakoi defaulttina. Jos käyttäjä muuttaa telakointia, muutokset overridaa tämän.
                $attributes['class'] .= ' dock_on_load';
            }
            return $attributes;
        }

        public function get_content() {
            if ($this->content !== null) {
              return $this->content;
            }

            $this->content =  new stdClass;

              global $PAGE;
              // Tämän alle linkit, jotka löytyvät etusivulta.
              if($PAGE->pagelayout == 'frontpage' || $PAGE->pagelayout == 'admin') {
                  // Näytetään kurssinlisäyspainike, jos siihen on oikeuksia.
                  if(has_capability('moodle/course:create', context_system::instance())) {
                      $this->content->text   = "<li><a href='" . new moodle_url("course/edit.php?category=1") . "'>". get_string('addnewcourse') . "</a></li>";
                  }
                  $this->content->text   .= "<li><a href='" . new moodle_url("admin/settings.php?section=frontpagesettings") . "'>". get_string('frontpagesettings') . "</a></li>";

              // Tämän alle linkit, jotka löytyvät kurssisivulta.
              } else if($PAGE->pagelayout == 'course' || $PAGE->pagelayout == 'incourse' || $PAGE->pagelayout == 'report' ){
                  // Näytetään kurssiavaimenluontipainike, jos kurssinmuokkaukseen on oikeus.
                  if(has_capability('block/course_list:myaddinstance', context_system::instance())) {
                      $this->content->text   .= "<li><a href='" . new moodle_url("../enrol/editinstance.php?courseid=" . $PAGE->course->id . "&type=self") . "'>" . get_string('set','portfolio_flickr') . " " . strtolower(get_string('password', 'enrol_self')) . "</a></li>";
                  }
              }

              // $this->content->footer = 'Footer';
              return $this->content;
        }
    }

}
