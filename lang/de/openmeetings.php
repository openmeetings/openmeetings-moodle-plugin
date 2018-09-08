<?php
/*
 * This file is part of Moodle - http://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 */
/*
* Licensed to the Apache Software Foundation (ASF) under one
* or more contributor license agreements.  See the NOTICE file
* distributed with this work for additional information
* regarding copyright ownership.  The ASF licenses this file
* to you under the Apache License, Version 2.0 (the
* "License") +  you may not use this file except in compliance
* with the License.  You may obtain a copy of the License at
*
*   http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing,
* software distributed under the License is distributed on an
* "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
* KIND, either express or implied.  See the License for the
* specific language governing permissions and limitations
* under the License.
*/

$string['openmeetings'] = 'openmeetings';
$string['openmeetings:addinstance'] = 'Eine OpenMeetings-Instanz hinzuf&uuml;gen';
$string['openmeetings:becomemoderator'] = 'Moderator in einem OpenMeetings-Raum werden';

$string['modulename'] = 'OpenMeetings';
$string['modulename_help'] = 'OpenMeetings ist e#ine freie browser-basierte Software, mit der Sie sich sehr einfach eine Webkonferenz einrichten k&ouml;nnen. Sie k&ouml;nnen daf&uuml;r Ihr Mikrofon und/oder Ihre Webcam nutzen, Dokumente auf einem Whiteboard austauschen, den Bildschirm teilen oder die Onlinesitzungen aufzeichnen.<br><br>Dieses Moodle-Plugin verwendet Apache OpenMeetings. Sie brauchen dazu eine eigene laufende Apache OpenMeetings Installation.<br><br>Weitere Informationen finden Sie hier: <a href="http://openmeetings.apache.org">http://openmeetings.apache.org</a>';
$string['modulenameplural'] = 'OpenMeetings';

$string['whiteboardfieldset'] = 'Custom example fieldset';
$string['whiteboardintro'] = 'OpenMeetings Intro';
$string['whiteboardname'] = 'Name';

$string['host'] = 'OpenMeetings Server Host oder IP';
$string['port'] = 'OpenMeetings Server Port';
$string['user'] = 'OpenMeetings Admin Benutzer';
$string['pass'] = 'OpenMeetings Admin Benutzer Passwort';
$string['moduleKey'] = 'Modul-Schl&uuml;ssel';
$string['moduleKeyDesc'] = 'Erweiterte Einstellung: OpenMeetings Modul-Schl&uuml;ssel (falls Sie mehrer Moodle Instanzen betreiben sollte der Schl&uuml;ssel bei jeder Instanz anders sein)';

$string['Room_Name'] = 'Raumname';
$string['Room_Type'] = 'Raumtyp';
$string['Room_Language'] = 'Sprache des Raumes';
$string['Max_User'] = 'Maximale Teilnehmerzahl';
$string['Wait_for_teacher'] = 'Moderations-Modus';

$string['recordings_label'] = 'Das Feld zur Auswahl aufgezeichneter Meetings wird nur verwendet, wenn der Raumtyp "Aufzeichnung anzeigen" gew&auml;hlt wurde. Es wird dann die Aufzeichnung anstatt des Konferenzraums angezeigt.';
$string['recordings_show'] = 'Aufgezeichnete Meetings';

$string['Conference'] = 'Konferenz (bis 16 Teilnehmer)';
$string['Interview'] = 'Interview (2 Teilnehmer)';
$string['Restricted'] = 'Webinar (bis 150 Teilnehmer)';
$string['Recording'] = 'Aufzeichnung anzeigen (Aufzeichnung ausw&auml;hlen, die statt dem Konferenzraum dann angezeigt wird!)';

$string['Moderation_Description'] = '(Moodle Administratoren, Lehrer und Kurs Moderatoren sind automatisch Moderator)';
$string['Moderation_TYPE_1'] = 'Teilnehmer m&uuml;ssen warten bis ein Moderator im Raum erscheint';
$string['Moderation_TYPE_2'] = 'Teilnehmer k&ouml;nnen selbstst&auml;ndig anfangen (der erste Besucher wird Moderator im Raum)';
$string['Moderation_TYPE_3'] = 'Jeder Teilnehmer ist automatisch Moderator im Konferenz-Raum';

$string['Allow_Recording'] = 'Funktion zum Aufzeichnen verf&uuml;gbar';
$string['Recording_TYPE_1'] = 'Die Funktion zum Aufzeichnen der Konferenz ist verf&uuml;gbar und kann gestartet werden.';
$string['Recording_TYPE_2'] = 'Die Funktion zum Aufzeichnen der Konferenz ist nicht verf&uuml;gbar.';

$string['webapp'] = 'Name der OpenMeetings-Webanwendung';
$string['webappDesc'] = 'Erweiterte Einstellung: Wenn sie die OpenMeetings Applikation umbenannt haben k&ouml;nnen Sie hier einen alternativen Namen eingeben.';

$string['download_mp4'] = 'Download .mp4';

$string['pluginadministration'] = 'OpenMeetings Administration';
$string['pluginname'] = 'OpenMeetings';
$string['whole_window'] = 'gesamtes Fenster verwenden';
$string['whole_window_type_1'] = 'in einem Rahmen anzeigen';
$string['whole_window_type_2'] = 'gesamtes Fenster verwenden';
$string['whole_window_type_3'] = 'ein neues Fenster öffnen';
$string['protocol'] = 'Protokoll';
$string['protocolDesc'] = 'Protokoll das verwendet wird, um die Openmeetings URLs zu erzeugen (Standard: http)';

$string['description'] = 'Beschreibung';

$string['Chat_Hidden'] = 'Chat verbergen';
$string['Chat_Hidden_TYPE_1'] = 'Chat im Raum anzeigen.';
$string['Chat_Hidden_TYPE_2'] = 'Chat im Raum verbergen.';

$string['Version_Ok'] = 'You have compatible version of OpenMeetings: ';
$string['Version_Bad'] = 'You need to update your OpenMeetings, minimum version is: ';

$string['checkpeer'] = 'Check SSL certificate';
$string['checkpeerDesc'] = 'Enable/Disable SSL certificate checking, INSECURE if unchecked (default: checked)';
$string['checkhost'] = 'Hostname verification';
$string['checkhostDesc'] = 'Enable/Disable hostname verification, INSECURE if unchecked (default: checked)';
