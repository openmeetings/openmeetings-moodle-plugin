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

// Licensed to the Apache Software Foundation (ASF) under one
// or more contributor license agreements.  See the NOTICE file
// distributed with this work for additional information
// regarding copyright ownership.  The ASF licenses this file
// to you under the Apache License, Version 2.0 (the
// "License") +  you may not use this file except in compliance
// with the License.  You may obtain a copy of the License at
//
// http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing,
// software distributed under the License is distributed on an
// "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
// KIND, either express or implied.  See the License for the
// specific language governing permissions and limitations
// under the License.

/**
 * Russian strings
 *
 * @package mod_openmeetings
 * @license    Apache-2.0 GPL-3.0-only
 * @copyright  OpenMeetings devs
 */

$string['openmeetings'] = 'openmeetings';
$string['openmeetings:addinstance'] = 'Добавить экземпляр OpenMeetings';
$string['openmeetings:becomemoderator'] = 'Стать модератором комнаты OpenMeetings';

$string['modulename'] = 'OpenMeetings';
$string['modulename_help'] = 'OpenMeetings - бесплатное веб приложение, позволяющее моментально развернуть конференцию в сети Интернет. Вы можете использовать микрофон и/или камеру, делиться документами, показывать свой экран и делать записи встреч.
<br><br>Этот модуль использует Apache OpenMeetings.
<br>Для работы необходима отдельная установка Apache OpenMeetings.<br>Дополнительная информация: <a hrefp="http://openmeetings.apache.org">http://openmeetings.apache.org</a>';
$string['modulenameplural'] = 'OpenMeetings';

$string['whiteboardfieldset'] = 'Пример настроек';
$string['whiteboardintro'] = 'Введение в OpenMeetings';
$string['whiteboardname'] = 'Название конференции';

$string['url'] = 'OpenMeetings URL';
$string['urlDesc'] = 'Полный URL до Вашего сервера OpenMeetings';
$string['user'] = 'Имя администатора OpenMeetings';
$string['pass'] = 'Пароль администратора OpenMeetings';
$string['moduleKey'] = 'Ключ модуля';
$string['moduleKeyDesc'] = 'Дополнительная функция: Ключ модуля OpenMeetings (должен быть уникальным чтобы использовать несколько экземпляров с одним сервером OpenMeetings)';

$string['Room_Name'] = 'Название комнаты';
$string['Room_Type'] = 'Тип комнаты';
$string['Room_Language'] = 'Язык комнаты';
$string['Max_User'] = 'Максимальное число пользователей';
$string['Wait_for_teacher'] = 'Ожидание модератора';

$string['recordings_label'] = 'Поле "Запись" используется только для комнат типа "Запись". Запись будет показана вместо конференции.';
$string['recordings_show'] = 'Записи, доступные для показа';
$string['recordings_search'] = 'Пожалуйста воспользуйтесь поиском для выбора записи';

$string['Conference'] = 'Конференция (до 16 участников)';
$string['Interview'] = 'Интервью (2 участника)';
$string['Restricted'] = 'Вебинар (до 150 участников)';
$string['Recording'] = 'Показать запись (выберите запись из выпадающего списка, чтобы показать вместо совещания)';

$string['Moderation_Description'] = '(Администраторы Moodle, преподаватели и создатели курсов автоматически становятся модераторами)';
$string['Moderation_TYPE_1'] = 'Участники должны должны ждать пока преподаватель войдёт в комнату';
$string['Moderation_TYPE_2'] = 'Участники не должны ждать (первый вошедший становится модератором)';
$string['Moderation_TYPE_3'] = 'Каждый участник автоматически становитсся модератором, когда входит в комнату';

$string['Allow_Recording'] = 'Разрешить запись?';
$string['Allow_RecordingDesc'] = 'Глобальная настройка для разрешения/запрещения записи';
$string['Recording_TYPE_1'] = 'Разрешить';
$string['Recording_TYPE_2'] = 'Запретить';

$string['download_mp4'] = 'Скачать .mp4';

$string['pluginadministration'] = 'Администратор OpenMeetings';
$string['pluginname'] = 'OpenMeetings';
$string['whole_window'] = 'Расположение';
$string['whole_window_type_1'] = 'В рамке';
$string['whole_window_type_2'] = 'На всё окно';
$string['whole_window_type_3'] = 'В новом окне';

$string['description'] = 'Описание';

$string['Chat_Hidden'] = 'Отображение чата';
$string['Chat_Hidden_TYPE_1'] = 'Показывать чат комнаты';
$string['Chat_Hidden_TYPE_2'] = 'Скрывать чат комнаты';

$string['Version_Ok'] = 'Вы используете совместимую версию OpenMeetings: ';
$string['Version_Bad'] = 'Вам необходимо обновить версию OpenMeetings, минимальная версия: ';

$string['checkpeer'] = 'Проверять сертификаты SSL';
$string['checkpeerDesc'] = 'Включить/выключить проверку SSL сертификатов, система будет УЯЗВИМА если выключить (по умолчанию: включено)';
$string['checkhost'] = 'Проверять адрес сервера';
$string['checkhostDesc'] = 'Включить/выключить проверку адреса сервера, система будет УЯЗВИМА если выключить (по умолчанию: включено)';

$string['upload_file'] = 'Загрузить файл';
$string['room_files'] = 'Файлы комнаты';
$string['wb_index'] = 'Индекс доски';
$string['om_file'] = 'Уже загруженный файл';
$string['room_file'] = 'Файл комнаты';
$string['remove'] = 'Удалить';
$string['clean_wb'] = 'Очистить доску';

$string['No_Recordings'] = 'Нет записей';
