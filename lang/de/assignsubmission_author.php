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
 * Strings for component 'assignsubmission_author', language 'de_du'
 *
 * @package assignsubmission_author
 * @copyright 2013 Rene Roepke
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['author'] = 'Autorengruppen';

$string['choose'] = 'Co-Autor wählen';
$string['choose_group'] = 'Teilnahme an folgender Autorengruppe';
$string['choose_coauthors'] = 'Auswahl eigener Co-Autoren';
$string['choose_defaultcoauthors'] = 'Auswahl ihrer Standard-Co-Autoren';
$string['choose_nocoauthors'] = 'Keine Co-Autoren';

$string['summary_author'] = 'Autor';
$string['summary_coauthors'] = 'Co-Autoren';
$string['summary_nocoauthors'] = 'Keine Co-Autoren';

$string['group'] = 'Gruppe';
$string['coauthors'] = 'Co-Autoren';
$string['defaultcoauthors'] = 'Standard-Co-Autoren';

$string['oneauthoronly'] = 'Die maximale Anzahl an Co-Autoren wurde auf 1 gesetzt.';
$string['noteamsubmission'] = 'Der Abgabetyp "Autorengruppen" kann nicht gemeinsam mit der Einstellung für Gruppeneinreichungen genutzt werden. Bitte kontaktieren Sie bei weiteren Fragen den Veranstalter des Kurses.';

$string['subject'] = 'Abgabe als Autorengruppe im Kurs <a href="{$a->courseurl}">{$a->coursename}</a>';
$string['message'] = '{$a->username} hat Sie in Aufgabe <a href="{$a->assignmenturl}">{$a->assignmentname}</a> als Co-Autor eingetragen.';

$string['maxauthors'] = 'Max. Anzahl Autoren pro Gruppe';
$string['maxauthors_help'] = 'Die maximale Anzahl an Autoren pro Gruppe beinhaltet den Autor sowie die Co-Autoren, z. B. bedeutet "1", dass keine Co-Autoren zulässig sind, "2" das ein weiterer Co-Autor zulässig ist.';
$string['ingroupsonly'] = 'Nur in der selben Gruppe';
$string['ingroupsonly_help'] = 'Wenn diese Option ausgewählt ist, können Teilnehmer/innen nur aus der eigenen Gruppe Co-Autoren auswählen, wenn nicht, dann aus dem ganzen Kurs.';
$string['notification'] = 'Benachrichtung aller Co-Autoren';
$string['notification_help'] = 'Wenn diese Option ausgewählt ist, werden alle Co-Autoren bei Abgabe benachrichtigt';
$string['groupsused'] = 'Gruppenmodus aktiv?';
$string['groupsused_help'] = 'Ist der Gruppenmodus aktiviert und diese Option wird ausgewählt, dann wird zur Nutzung von Autorengruppen vorausgesetzt, dass der Teilnehmer in eine Gruppe eingetragen ist. Ist er es nicht, kann er keine Autorengruppe auswählen.';
$string['asdefault'] = 'Als neue Standard-Co-Autoren für diesen Kurs speichern';

$string['default'] = 'Standardmäßig aktiviert';
$string['default_help'] = 'Die gewählte Methode für die Abgabe von Lösungen wird für alle neuen Aufgaben voreingestellt.';
$string['enabled'] = 'Autorengruppen';
$string['enabled_help'] = 'Teilnehmer/innen können nach der Aktivierung fü ihre Abgabe Co-Autoren auswählen und ggf. als Standard speichern.';

$string['pluginname'] = 'Abgabe als Autorengruppen';
$string['nopossiblecoauthors'] = 'Sie können keine Co-Autoren auswählen, da sie keiner Gruppe angehören. Bitte kontaktieren Sie den Veranstalter des Kurses.';

$string['error_teamsubmission'] = 'Es kann keine Lösung eingereicht werden. <br><br>Der Abgabetyp "Autorengruppen" kann nicht gemeinsam mit der Einstellung für Gruppeneinreichungen genutzt werden. Bitte kontaktieren Sie bei weiteren Fragen den Veranstalter des Kurses.';
$string['header'] = 'Co-Autoren-Auswahl';

// Capability.
$string['author:canbecoauthor'] = 'Der Nutzer darf Co-Autoren bestimmen.';

// Privacy API.
$string['privacy:metadata:assignsubmission_author'] = 'Informationen über die Einreichung von Autoren, einschließlich Mitautoren';
$string['privacy:assignsubmission_author:id'] = 'ID dieses Tabelleneintrages';
$string['privacy:assignsubmission_author:assignment'] = 'ID der entsprechenden Zuordnung in der Tabelle "assign".';
$string['privacy:assignubmission_author:submission'] = 'ID der entsprechenden Zuordnung in der Tabelle "assign_submission".';
$string['privacy:assignsubmission_author:author'] = 'ID des Studierenden, der die ursprüngliche Aufgabe eingereicht hat';
$string['privacy:assignignsubmission_author:authorlist'] = 'Comma-separated list of studend IDs, die der Student als Co-Autoren deklariert hat';

$string['privacy:metadata:assignsubmission_author_def'] = 'Informationen über Standardwerte für Co-Autoren';
$string['privacy:assignignsubmission_author_def:id'] = 'ID dieses Tabelleneintrages';
$string['privacy:assignsubmission_author_def:course'] = 'Die Kurs ID, in dem diese Standardwerte angewendet werden sollen.';
$string['privacy:assignignsubmission_author_def:user'] = 'ID des Studierenden, der die ursprüngliche Aufgabe eingereicht hat';
$string['privacy:assignignsubmission_author_def:coauthors'] = 'Kommagetrennte Liste der Studierenden-IDs, die der Schüler als seine Standard-Koautoren deklariert hat.';