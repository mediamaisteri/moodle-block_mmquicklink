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
 * Form for editing HTML block instances.
 *
 * @package   block_mmquicklink
 * @copyright 2017 Mediamaisteri Oy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'MM Quicklink / hallintatyökalut';
$string['title'] = "Hallintatyökalut";

$string['visibility_settings'] = "Näkyvyysasetukset";
$string['coursemgmt'] = "Lisää/muokkaa kursseja";

$string['setting_reports'] = "Raportit";
$string['setting_reports_desc'] = "Valitse piilottaaksesi raportit linkin.";
$string['setting_competencereport'] = "Osaamisraportti";
$string['setting_competencereport_desc'] = "Valitse piilottaaksesi osaamisraportti-linkin mRaportointi-linkin alta";

$string['setting_participants'] = "Osallistujat";
$string['setting_participants_desc'] = "Valitse piilottaaksesi osallistujat-linkin.";

$string['setting_course_grades'] = "Arvioinnit";
$string['setting_course_grades_desc'] = "Valitse piilottaaksesi arvioinnit-linkin.";
$string['setting_easylink'] = "HelppoLinkki";
$string['setting_easylink_desc'] = "Valitse piilottaaksesi HelppoLinkki-linkin.";

$string['setting_themesettings'] = "Teeman asetukset";
$string['setting_themesettings_desc'] = "Valitse piilottaaksesi teeman asetukset -linkin.";
$string['addcourse'] = "Luo kurssi";
$string['setting_delcourse'] = "Poista kurssi";
$string['setting_delcourse_desc'] = "Valitse piilottaaksesi kurssinpoistolinkin";
$string['setting_hidecourse'] = "Piilota/näytä kurssi";
$string['setting_hidecourse_desc'] = "Valitse piilottaaksesi piilota/näytä kurssi -linkki.";

$string['setting_coursecompletionsettings'] = "Kurssin suoritusasetukset";
$string['setting_coursecompletionsettings_desc'] = "Valitse piilottaksesi 'Muokkaa kurssin suoritusasetuksia' -painikkeen.";

$string['setting_activityprogresssettings'] = "Kurssin aktiviteettiraportti";
$string['setting_activityprogresssettings_desc'] = "Valitse piilottaaksesi kurssin aktiviteettiraportti -linkki.";

$string['setting_archive'] = "Arkistoi kurssi";
$string['setting_archive_desc'] = "Valitse piilottaaksesi arkistointipainikkeen. Painikkeen toiminta vaatii lisäksi local_course_archive -lisäosan.";
$string['archive_course'] = "Siirrä kurssi roskakoriin";
$string['archived'] = "Kurssin siirtäminen roskakoriin onnistui.";

$string['mmquicklink:addinstance'] = "Lisää Quicklink-lohko";
$string['mmquicklink:myaddinstance'] = "Lisää Quicklink-lohko työpöydälle";

$string['setting_roles'] = "Sallitut roolit";
$string['setting_roles_desc'] = "Roolit, jotka saavat nähdä lohkon.";

$string['setting_langcust'] = "Kielen muokkaus";
$string['setting_langcust_desc'] = "Valitse piilottaaksesi kielenmuokkauslinkin";

$string['emptyblock'] = "Tällä sivulla roolisi oikeudet eivät ole riittävät.";

$string['coursegrades'] = "Kurssin arvioinnit";
$string['hide_course'] = "Piilota kurssi";
$string['show_course'] = "Näytä kurssi";
$string['delete_course'] = "Poista kurssi";

$string['setting_pagelayouts'] = "Sallitut sivutyypit";
$string['setting_pagelayouts_desc'] = "Valitse sivutyypit, joilla lohko on mahdollista näyttää.";

$string['setting_editsettings'] = "Muokkaa kurssin asetuksia";
$string['setting_editsettings_desc'] = "Valitse piilottaaksesi 'muokkaa kurssin asetuksia' -linkin.";

$string['setting_general'] = "Yleiset asetukset";
$string['setting_blocktitle'] = "Lohkon otsikko";
$string['setting_blocktitle_desc'] = "Jos otsikko on tyhjä, käytetään oletusotsikkoa.";

$string['advanced_options'] = "Lisäasetukset";
$string['setting_participants_select'] = "Osallistujat-linkki";
$string['setting_participants_select_desc'] = "Valitse, minne osallistujat-linkki johtaa.";
$string['setting_participants_users'] = "Yleinen osallistujat-sivu";
$string['setting_participants_enrol'] = "Osallistujien lisääminen";
$string['sorting_options'] = "Painikkeiden järjestys";
$string["trainingmanagement"] = "Koulutuksenhallinta";

$string['switchrole'] = "Vaihda rooliin";
$string['setting_otherrole_select'] = "Vaihda roolia";
$string['setting_otherrole_select_desc'] = "Valitse rooli, jota käytetään 'vaihda roolia' -linkin kanssa.";
$string['setting_otherrole'] = "Vaihda roolia";
$string['setting_otherrole_desc'] = "Valitse piilottaaksesi 'vaihda roolia' -linkin.";
$string['setting_defaultcategory'] = "Uuden kurssin oletuskategoria.";
$string['setting_defaultcategory_desc'] = "Voit valita uuden kurssin oletuskategorian.";
$string['setting_localreportssummary'] = "mReports kurssisivulla";
$string['setting_localreportssummary_desc'] = "Valitse piilottaaksesi mRaportointilinkki kurssisivulla";
$string['local_reports_summary'] = "Yhteenvetoraportti";
$string['enrolmentkey'] = "Kurssiavain";
$string['setting_coursebgimagechanger'] = "Kurssin taustakuva";
$string['setting_coursebgimagechanger_desc'] = "Valitse piilottaaksesi 'kurssin taustakuvan vaihtaja' -painikkeen.";
$string['coursebgimagechanger'] = "Kurssin taustakuva";
$string['setting_allowedcategories'] = "Sallitut kategoriat missä helppolinkki painike näytetään";
$string['setting_allowedcategories_desc'] = "Käytä ainoastaan ylätason kategorioita esim. 1,2,3. Vaikuttaa myös ylätason kategorian alakategorioihin.";
$string['setting_unique_enrolmentkey'] = "Uniikit kurssiavaimet";
$string['setting_unique_enrolmentkey_desc'] = "Valitse käyttääksesi uniikkeja kurssiavaimia.";

$string['toomanyselfenrolments'] = 'Kurssilla on usea itserekisteröitymistapa käytössä. Jotta tämän lohkon kautta voitaisiin asettaa kurssiavain, sinun tulee poistaa ylimääräiset itserekisteröitymiset käytöstä osoitteessa ' . "<a href='" . '{$a}' . "'>" . '{$a}' . "</a>.";
$string['multiplepasswords'] = "Kurssilla on useampi itserekisteröitymistapa käytössä. Tarkista ne osallistujien lisäämistavoista.";
$string['privacy:null_reason'] = "Tämä lisäosa ei käsittele henkilötietoja.";

$string['buttonsorting'] = "Raahaa & pudota nappeja siirrelläksesi niiden paikkoja.";
$string['clicktoreset'] = "Klikkaa nollataksesi järjestyksen";
$string['saved'] = "Muutokset tallennettiin onnistuneesti!";
$string['saving'] = "Muutoksia tallennetaan...";

$string['fromtemplate'] = "Luo mallipohjasta";

$string['areyousure'] = "Oletko varma, että haluat arkistoida seuraavan kurssin";
$string['areyousurehide1'] = "Oletko varma, että haluat piilottaa seuraavan kurssin";
$string['areyousurehide0'] = "Oletko varma, että haluat näyttää seuraavan kurssin";
$string['hide1'] = "Piilota kurssi";
$string['hide0'] = "Näytä kurssi";

$string['delete_course_modal_body'] = "Haluatko varmasti poistaa seuraavan kurssin";
$string['delete_course_modal_title'] = "Poista kurssi";
$string['delete_course_failed_msg'] = "Kurssin poistaminen epäonnistui";
$string['delete_course_success_msg'] = "Kurssin poistaminen onnistui!";

// Question bank & category.
$string['questionbank'] = "Kysymyspankki";
$string['setting_questionbank'] = "Kysymyspankki";
$string['setting_questionbank_desc'] = "Valitse piilottaaksesi 'Kysymyspankki' -painikkeen.";

$string['questioncategory'] = "Kysymyskategoriat";
$string['setting_questioncategory'] = "Kysymyskategoriat";
$string['setting_questioncategory_desc'] = "Valitse piilottaaksesi 'Kysymyskategoriat' -painikkeen.";

$string['backup'] = "Varmuuskopiointi";
$string['setting_backup'] = "Varmuuskopiointi";
$string['setting_backup_desc'] = "Valitse piilottaaksesi 'Varmuuskopiointi' -painikkeen.";

$string['requiredcapability'] = "Vaadittu kyky";
$string['requiredroleid'] = "Vaadittu rooli";
$string['custombuttons'] = "Omat painikkeet";
$string['custombuttons_desc'] = "Klikkaa <a href='../../blocks/mmquicklink/custombuttons.php'>tästä</a> hallinnoidaksesi omia painikkeita.";
$string['ok'] = "Toiminto suoritettu onnistuneesti!";
$string['description'] = "Linkkiteksti";
$string['variables'] = "Voit käyttää seuraavia muuttujia linkin tekstissä ja osoitteessa:<br>
Kurssin ID: {{id}}<br>
Kontekstin ID: {{contextid}}<br>
Käyttäjn ID: {{userid}}";

$string['enrolmentkey_reserved'] = "Kurssiavain käytössä toisella kurssilla!";

$string['setting_restore'] = "Palauta kurssi";
$string['setting_restore_desc'] = 'Valitse piilottaaksesi "Palauta kurssi arkistosta"-painikkeen. Painikkeen toiminta vaatii lisäksi local_course_archive -lisäosan.';
$string['restorecourse'] = "Palauta kurssi arkistosta";
$string['restorecourse_confirm'] = "Oletko varma että haluat palauttaa seuraavan kurssin";
$string['notarchived'] = 'Kurssi ei ole arkisto- tai poistokategoriassa';
$string['norestorecategory'] = "Kurssi tulisi palauttaa palautuskategoriaan, sillä sen alkuperäinen kategoria ei ole tiedossa. Palautuskategoriaa ei ole asetettu kurssien arkistoinnin asetuksissa.";
$string['restored_restorecat'] = "Kurssi palautetaan palautuskategoriaan, sillä sen alkuperäinen kategoria ei ole tiedossa. Palautuskategorian ID on";
$string['restored_originalcat'] = "Kurssi palautetaan alkuperäiseen kategoriaansa, jonka ID on";
$string['restored'] = "Kurssi on palautettu onnistuneesti.";

$string['mmquicklink:custombuttons'] = "Oikeus lisätä kustomoituja painikkeita";
