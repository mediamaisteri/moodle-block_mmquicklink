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

defined('MOODLE_INTERNAL') || die();

$string['setting_coursecompletionsettings'] = "inställning av kurser";

$string['mmquicklink:custombuttons'] = "Höger för att lägga till anpassade knappar";

$string['fromtemplate'] = "Skapa från kursmall";
$string['questionbank'] = "Frågebank";
$string['setting_questionbank'] = "Frågebank";
$string['setting_questionbank_desc'] = "Välj för att dölja 'Frågebank' -knappen.";

$string['delete_course_modal_body'] = "Är du säker att du vill radera denna kurs?";
$string['delete_course_modal_title'] = "Radera kurs";
$string['delete_course_failed_msg'] = "Radering av kurs misslyckades";
$string['delete_course_success_msg'] = "Radering av kurs lyckades!";
$string['setting_delcourse'] = "Radera kurs";
$string['delete_course'] = "Radera kurs";

$string['pluginname'] = 'MM Quicklink';
$string['title'] = "Hanteringsverktyg";
$string['coursemgmt'] = "Redigera kurser/categorier";
$string['visibility_settings'] = 'Synlighetsinställningar';

$string['setting_reports'] = "Rapporter";
$string['setting_reports_desc'] = "Välj för att dölja rapporter länken";
$string['setting_competencereport'] = "Kompetensrapport";
$string['setting_competencereport_desc'] = "Markera för att dölja kompetensrapportlänken under mReports";

$string['setting_participants'] = "Deltagare";
$string['setting_participants_desc'] = "Välj för att dölja kursdeltagarelänk";

$string['setting_course_grades'] = "Kursbetyg";
$string['setting_course_grades_desc'] = "Välj för att dölja kursbetygslänk";

$string['setting_themesettings'] = "Temainställningar";
$string['setting_themesettings_desc'] = "Välj för att dölja temainställningslänk";
$string['addcourse'] = "Lägg till kurs";
$string['setting_delcourse'] = "Radera kurs";
$string['setting_delcourse_desc'] = "Välj för att dölja radera kurs länken";
$string['setting_hidecourse'] = "Dölj kurs";
$string['setting_hidecourse_desc'] = "Välj för att dölja dölj kurs länken";
$string['setting_easylink'] = "Easylink";
$string['setting_easylink_desc'] = "Välj för att dölja Easylink knappen.";

$string['setting_archive'] = "Arkivera kurs";
$string['setting_archive_desc'] = "Välj för att dölja 'Arkivera kurs' knappen. Denna funktionalitet kräver att local_course_archive plugin är installerad";
$string['archive_course'] = "Flytta kursen till skräpkorgen";
$string['archived'] = "Kursen har flyttats till skräpkorgen";

$string['mmquicklink:addinstance'] = "Lägg till en ny Quicklink block";
$string['mmquicklink:myaddinstance'] = "Lägg till en ny Quicklink block till översikt";

$string['setting_roles'] = "Godkända roller";
$string['setting_roles_desc'] = "Kolla vilka roller kan se blocket";

$string['setting_langcust'] = "Språkanpassning";
$string['setting_langcust_desc'] = "Välj för att dölja språkanpassningslänken";

$string['emptyblock'] = "På denna sida är din rolls tillstånd inte tillräckliga";

$string['coursegrades'] = "Kursbetyg";
$string['hide_course'] = "Dölj kurs";
$string['show_course'] = "Visa kurs";
$string['delete_course'] = "Radera kurs";

$string['setting_pagelayouts'] = "Sidlayout";
$string['setting_pagelayouts_desc'] = "Kolla sidlayouten som kan visa blocket";

$string['setting_editsettings'] = "Editera kursinställningar";
$string['setting_editsettings_desc'] = "Välj på att dölja 'Editera kursinställningar' länken";

$string['setting_coursecompletionsettings'] = "Fullgörande av kurs inställningar";
$string['setting_coursecompletionsettings_desc'] = "Välj för att dölja 'Editera fullgörande av kurs inställningar' länken";

$string['setting_activityprogresssettings'] = "Aktivietetsrapport";
$string['setting_activityprogresssettings_desc'] = "Välj för att dölja 'Aktivitetsrapport' länken";

$string['setting_general'] = "Allmänna inställningar";
$string['setting_blocktitle'] = "Blocktitel";
$string['setting_blocktitle_desc'] = "Om titeln är oinställd, använder blocket standardtitel.";

$string['advanced_options'] = "Avancerade inställningar";
$string['setting_participants_select'] = "Deltagare länk";
$string['setting_participants_select_desc'] = "Välj vart deltagarelänken styr.";
$string['setting_participants_users'] = "Allmän deltagarsida";
$string['setting_participants_enrol'] = "Användarregistreringssida";
$string['enrolmentkey'] = "Kursnyckel";
$string["trainingmanagement"] = "Utbildningsledning";
$string['sorting_options'] = "Knappsortering";
$string['switchrole'] = "Byt roll till";
$string['setting_otherrole_select'] = "Byt roll";
$string['setting_otherrole_select_desc'] = "Välj roll som används med 'Byt roll till' länken.";
$string['setting_otherrole'] = "Byt roll";
$string['setting_otherrole_desc'] = "Välk för att dölja 'Byt roll' länken";
$string['setting_defaultcategory'] = "Standardkategori för nya kurser";
$string['setting_defaultcategory_desc'] = "Vänligen välj standardkategorin för nya kurser.";
$string['setting_localreportssummary'] = "mReports på kurssidan";
$string['setting_localreportssummary_desc'] = "Välj för att dölja mReports länken på kurssidan";
$string['local_reports_summary'] = "Sammanfattningsrapport";
$string['setting_coursebgimagechanger'] = "Kursbakgrund";
$string['setting_coursebgimagechanger_desc'] = "Välj för att dölja 'Kursbakgrund' knappen.";
$string['coursebgimagechanger'] = "Kursbakgrund";
$string['setting_allowedcategories'] = "Tillåtna kategorier där easylink-knappen visas";
$string['setting_allowedcategories_desc'] = "Använd endast toppnivåkategorier t.ex. 1,2,3. Påverkar underkategorier på toppnivå.";
$string['setting_unique_enrolmentkey'] = "Unika kursnycklar";
$string['setting_unique_enrolmentkey_desc'] = "Välj för att använda unika kursnycklar";

$string['toomanyselfenrolments'] = 'Denna kurs har flera metoder för självregistrering aktiverade. För att använda denna funktion, ta bort extra självregistreringsmetoder på ' . "<a href='" . '{$a}' . "'>" . '{$a}' . "</a>.";
$string['multiplepasswords'] = "Denna kurs har flera självregistreringar aktiverade. Kontrollera dem på sidan 'registreringsmetoder'.";
$string['privacy:null_reason'] = "Denna plugin hanterar eller lagrar inte användardata.";

$string['buttonsorting'] = "Dra och släpp knappar för att sortera dem.";
$string['clicktoreset'] = "Klicka för att återställa sorteringen";
$string['saved'] = "Ändringar har sparats!";
$string['saving'] = "Sparar ändringar...";

$string['fromtemplate'] = "Skapa från mall";
$string['areyousure'] = "Är du säker på att du vill arkivera följande kurs";
$string['areyousurehide1'] = "Är du säker på att du vill dölja följande kurs";
$string['areyousurehide0'] = "Är du säker på att du vill visa följande kurs";
$string['hide1'] = "Dölj kurs";
$string['hide0'] = "Visa kurs";

$string['delete_course_modal_body'] = "Är du säker att du vill radera följande kurs?";
$string['delete_course_modal_title'] = "Radera kurs";
$string['delete_course_failed_msg'] = "Radering av kurs misslyckades";
$string['delete_course_success_msg'] = "Radering av kurs lyckades";

// Frågebank & kategori.
$string['questionbank'] = "Frågebank";
$string['setting_questionbank'] = "Frågebank";
$string['setting_questionbank_desc'] = "Välj för att dölja 'Frågebank' knappen.";

$string['questioncategory'] = "Frågekategori";
$string['setting_questioncategory'] = "Frågekategorier";
$string['setting_questioncategory_desc'] = "Välj för att dölja 'Frågekategorier' knappen.";

$string['backup'] = "Säkerhetskopia";
$string['setting_backup'] = "Säkerhetskopia";
$string['setting_backup_desc'] = "Välj för att dölja 'Säkerhetskopia' knappen.";

$string['custombuttons'] = "Specialknappar";
$string['custombuttons_desc'] = "Klicka <a href='../blocks/mmquicklink/custombuttons.php'>här</a> för att hantera specialknapparna.";
$string['href'] = "Adresslänk";
$string['context'] = "Synlighet";
$string['requiredcapability'] = "Nödvändig förmåga";
$string['requiredroleid'] = "Nödvändig roll";
$string['adminonly'] = "Endast administratörer";
$string['ok'] = "Åtgärden slutfördes framgångsrikt!";
$string['description'] = "Textlänk";
$string['variables'] = "Du kan använda följande variabler i länkadress och text:<br>
Kurs ID: {{id}}<br>
Kontext ID: {{contextid}}<br>
Användare ID: {{userid}}";

$string['enrolmentkey_reserved'] = "Kursnyckeln är redan i användning i en annan kurs!";

$string['setting_restore'] = "Återställ kurs";
$string['setting_restore_desc'] = 'Välj för att dölja knappen "Återställ kurs från arkiv". Denna funktion kräver att local_course_archive plugin är installerad.';
$string['restorecourse'] = "Återställ kurs från arkiv";
$string['restorecourse_confirm'] = "Är du säker att du vill återställa följande kurs";
$string['notarchived'] = 'Kursen är inte i arkivet eller i raderat kategorin';
$string['restored_restorecat'] = "Kursen kommer att återställas till återställningskategorin eftersom dess ursprungliga kategori är okänd. Återställ kategori-ID är";
$string['norestorecategory'] = "Kursen bör återställas till återställningskategorin eftersom dess ursprungliga kategori är okänd. Återställningskategori är inte inställd i kursarkivets inställningar";
$string['restored_originalcat'] = "Kursen kommer att återställas till sin ursprungliga kategori med ett ID på";
$string['restored'] = "Kursen har återställts framgångsrikt.";

$string['mmquicklink:custombuttons'] = "Möjlighet att lägga till anpassade knappar";