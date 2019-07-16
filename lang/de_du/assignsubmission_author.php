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

// Shown during submission.
$string['choose'] = 'Co-Autor wählen';
$string['choose_group'] = 'Bestehende Autorengruppe wählen';
$string['choose_coauthors'] = 'Co-Autoren wählen';
$string['choose_coauthors_remove'] = '. Wenn Sie die Co-Autoren ändern, nachdem eine Einreichung erfolgt ist, wird die Einreichung für die alten Co-Autoren gelöscht.';
$string['choose_coauthors_no_remove'] = '. Wenn Sie die Co-Autoren ändern, nachdem eine Einreichung erfolgt ist, bleibt die alte Einreichung bei den alten Co-Autoren bestehen.';
$string['choose_new_coauthors'] = 'Neue Co-Autoren auswählen';
$string['choose_new_coauthors_no_remove'] = '. Wenn bereits etwas eingereicht wurde, bleibt die alte Einreichung bei den oben genannten alten Autoren bestehen.';
$string['choose_defaultcoauthors'] = 'Auswahl ihrer Standard-Co-Autoren';
$string['choose_nocoauthors'] = 'Keine Co-Autoren';

$string['summary_author'] = 'Autor';
$string['summary_coauthors'] = 'Co-Autoren';
$string['summary_nocoauthors'] = 'Keine Co-Autoren';

$string['group'] = 'Gruppe';
$string['coauthors'] = 'Co-Autoren';
$string['defaultcoauthors'] = 'Standard-Co-Autoren';

$string['oneauthoronly'] = 'Die maximale Anzahl an Co-Autoren wurde auf 1 gesetzt.';
$string['noteamsubmission'] = 'Der Abgabetyp "Autorengruppen" kann nicht gemeinsam mit der Einstellung für Gruppeneinreichungen genutzt werden. Bitte kontaktiere bei weiteren Fragen den Veranstalter des Kurses.';

$string['subject'] = 'Abgabe in einer Autorengruppe im Kurs "{$a->coursename}"';
$string['subject_deleted'] = 'Du wurdest aus einer Autorengruppe im Kurs "{$a->coursename}" entfernt';
$string['message'] = 'Kurs <a href="{$a->courseurl}">{$a->coursename}</a>: <a href="{$a->userurl}">{$a->username}</a> hat eine Einreichung für die Abgabe <a href="{$a->assignmenturl}">{$a->assignmentname}</a> eingereicht und dich als Co-Autor hinzugefügt.';
$string['message_deleted'] = 'Kurs <a href="{$a->courseurl}">{$a->coursename}</a>: <a href="{$a->userurl}">{$a->username}</a> hat dich als co-autor in der Abgabe <a href="{$a->assignmenturl}">{$a->assignmentname}</a> entfernt.';

// Mod settings for each instance.
$string['setting_explanation'] = 'Diese Einstellungen sind deaktiviert, wenn die "'.$string['author'].'" Checkbox in dem Abschnitt "{$a->submisiontypesstring}" oben nicht gesetzt ist.';
$string['maxauthors'] = 'Max. Anzahl Autoren pro Gruppe';
$string['maxauthors_help'] = 'Die maximale Anzahl an Autoren pro Gruppe beinhaltet den Autor sowie die Co-Autoren, z. B. bedeutet "1", dass keine Co-Autoren zulässig sind, "2" das ein weiterer Co-Autor zulässig ist.';
$string['ingroupsonly'] = 'Nur aus der selben Gruppe';
$string['ingroupsonly_help'] = 'Teilnehmer/innen können nur aus der eigenen Gruppe Co-Autoren auswählen.';
$string['notification'] = 'Benachrichtung aller Co-Autoren';
$string['notification_help'] = 'Alle Co-Autoren werden bei einer Abgabe benachrichtigt und, wenn sie von einer Ko-Autoren Gruppe entfernt werden.';
$string['groupsused'] = 'Nutzer müssen in einer Gruppe sein';
$string['groupsused_help'] = 'Ist der Gruppenmodus aktiviert und diese Option wird ausgewählt, dann wird zur Nutzung von Autorengruppen vorausgesetzt, dass der Teilnehmer in eine Gruppe eingetragen ist. Ist er es nicht, kann er keine Autorengruppe auswählen.';
$string['displaymail'] = 'Mail-Adresse von Nutzern bei Auswahl der Co-Autoren anzeigen';
$string['displaymail_help'] = "Dies hilft Nutzern, ihre gewünschten Co-Autoren zu identifizieren, wenn es mehrfach den gleichen Namen gibt. <br><br>Bitte beachte die Privatsphäre der Nutzer.";
$string['duplicatesubmission'] = 'Dupliziere die Abgabe für alle Gruppenmitglieder';
$string['duplicatesubmission_help'] = 'Wenn aktiviert, wird die Abgabe des Originalautors für jeden Mitautor dupliziert und umgekehrt. '
.'Auf diese Weise können alle Autoren einer Co-Autorengruppe die Abgabe sehen und bearbeiten. <br><br>'
.'Beachten Sie, dass ein Benutzer seine Einreichung für jeden Benutzer im Kurs vervielfachen könnte, wenn er/sie durch alle verfügbaren Co-Autoren jongliert. <br>'
.'Es führt auch zu Falschanzeigen bei Plagiat-Plugins, weil die Benutzer genau die gleiche Abgabe haben.';
$string['removesubmission'] = 'Entferne die Abgabe, wenn Co-Autoren entfernt werden';
$string['removesubmission_help'] = 'Wenn die Mitglieder einer Autorengruppe nach einer Abgabe durch den Autor geändert werden, wird auch die Abgabe für die entfernten Autoren entfernt. '
.'Die entfernten Autoren besitzen dann keine Abgabe mehr. Dies kann nützlich sein, wenn der Autor falsche Co-Autoren ausgewählt hat. <br><br>'
.'Obwohl dies dazu führen kann, dass der Autor anderen Benutzern die Einreichung "wegnehmen" kann, kann eine E-Mail gesendet werden, wenn Co-Autoren aus einer Autorengruppe entfernt werden (siehe Option oben).<br><br>'
.'Wenn Ko-Autoren ihre Gruppe wechseln, bleibt die Einreichung beim ursprünglichen Autor und beim Rest der Ko-Autoren erhalten.';

$string['asdefault'] = 'Als neue Standard-Co-Autoren für diesen Kurs speichern';
$string['default'] = 'Standardmäßig aktiviert';
$string['default_help'] = 'Die gewählte Methode für die Abgabe von Lösungen wird für alle neuen Aufgaben voreingestellt.';
$string['enabled'] = 'Autorengruppen';
$string['enabled_help'] = 'Teilnehmer/innen können nach der Aktivierung fü ihre Abgabe Co-Autoren auswählen und ggf. als Standard speichern.';

$string['pluginname'] = 'Abgabe als Autorengruppen';
$string['nopossiblecoauthors'] = 'Du kannst keine Co-Autoren auswählen, da du keiner Gruppe angehörst. Bitte kontaktiere den Veranstalter des Kurses.';

$string['error_teamsubmission'] = 'Es kann keine Lösung eingereicht werden. <br><br>Der Abgabetyp "Autorengruppen" kann nicht gemeinsam mit der Einstellung für Gruppeneinreichungen genutzt werden. Bitte kontaktiere bei weiteren Fragen den Veranstalter des Kurses.';
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
$string['privacy:assignignsubmission_author_def:userid'] = 'ID des Studierenden, der die ursprüngliche Aufgabe eingereicht hat';
$string['privacy:assignignsubmission_author_def:coauthors'] = 'Kommagetrennte Liste der Studierenden-IDs, die der Schüler als seine Standard-Koautoren deklariert hat.';