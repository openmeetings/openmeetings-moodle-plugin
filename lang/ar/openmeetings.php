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

$string['openmeetings'] = 'غرفة الاجتماعات';
$string['openmeetings:addinstance'] = 'أضف نسخة من غرفة الاجتماعات';
$string['openmeetings:becomemoderator'] = 'كن مشرفًا في غرفة الاجتماعات';

$string['modulename'] = 'غرفة الاجتماعات';
$string['modulename_help'] = 'OpenMeetings is a free browser-based software that allows you to set up instantly a conference in the Web. You can use your microphone and/or webcam, share documents on a white board, share your screen or record meetings.
<br><br>This Moodle plugin uses Apache OpenMeetings.
<br>You need your own Apache OpenMeetings instance running.<br><br>For more information see: <a href="http://openmeetings.apache.org">http://openmeetings.apache.org</a>';
$string['modulenameplural'] = 'غرفة الاجتماعات';

$string['whiteboardfieldset'] = 'مثال مخصص لمجموعة الحقول';
$string['whiteboardintro'] = 'مقدمة غرفة الاجتماعات';
$string['whiteboardname'] = 'اسم المؤتمر';

$string['host'] = 'OpenMeetings Server Host or IP';
$string['port'] = 'OpenMeetings Server Port';
$string['user'] = 'OpenMeetings Admin User';
$string['pass'] = 'OpenMeetings Admin User Password';
$string['moduleKey'] = 'Module Key';
$string['moduleKeyDesc'] = 'Advanced setting: OpenMeetings Module key (vary for multiple instances using same OpenMeetings Server)';

$string['Room_Name'] = 'اسم الغرفة';
$string['Room_Type'] = 'نوع الغرفة';
$string['Room_Language'] = 'لغة الغرفة';
$string['Max_User'] = 'الحد الاقصى للمستخدمين';
$string['Wait_for_teacher'] = 'ننتظر المشرف';

$string['recordings_label'] = 'يتم استخدام حقل التسجيل فقط إذا كان نوع الغرفة هو التسجيل. سيتم عرض تسجيل بدلاً من غرفة الاجتماعات.';
$string['recordings_show'] = 'التسجيلات المتاحة للعروض';

$string['Conference'] = 'مؤتمر (بحد أقصى 16 مشاركًا)';
$string['Interview'] = 'مقابلة (2 مشاركين)';
$string['Restricted'] = 'ندوة عبر الإنترنت (بحد أقصى 150 مشاركًا)';
$string['Recording'] = 'إظهار التسجيل (حدد التسجيل من القائمة المنسدلة ليتم عرضها بدلاً من الاجتماع)';

$string['Moderation_Description'] = '(يُعد مسؤولو الموقع والمعلمون ومنشئو الدورة التدريبية مشرفين تلقائيًا)';
$string['Moderation_TYPE_1'] = 'يحتاج المشاركون إلى الانتظار حتى يدخل المعلم إلى الغرفة';
$string['Moderation_TYPE_2'] = 'يمكن للمشاركين البدء بالفعل (يصبح أول مستخدم في الغرفة مشرفًا)';
$string['Moderation_TYPE_3'] = 'كل مشارك هو المشرف تلقائيًا عند دخوله إلى الغرفة';

$string['Allow_Recording'] = 'يسمح بالتسجيل';
$string['Recording_TYPE_1'] = 'وظيفة التسجيل متاحة.';
$string['Recording_TYPE_2'] = 'وظيفة التسجيل غير متوفرة.';

$string['webapp'] = 'اسم تطبيق الويب لغرفة الاجتماعات';
$string['webappDesc'] = 'الإعداد المتقدم: إذا قمت بإعادة تسمية تطبيق الويب OpenMeetings ، يمكنك إدخال اسمك البديل هنا.';

$string['download_mp4'] = 'تنزيل mp4';

$string['pluginadministration'] = 'إدارة غرفة الاجتماعات';
$string['pluginname'] = 'غرفة الاجتماعات';
$string['whole_window'] = 'يشغل الصفحه بأكملها';
$string['whole_window_type_1'] = 'العرض في الإطار';
$string['whole_window_type_2'] = 'يشغل الصفحه بأكملها';
$string['whole_window_type_3'] = 'افتح في صفحه جديدة';
$string['protocol'] = 'Protocol';
$string['protocolDesc'] = 'Protocol to be used while constructing Openmeetings URLs (default: http)';

$string['description'] = 'الوصف';

$string['Chat_Hidden'] = 'اخفاء الدردشة';
$string['Chat_Hidden_TYPE_1'] = 'عرض الدردشة في الغرفة.';
$string['Chat_Hidden_TYPE_2'] = 'إخفاء الدردشة في الغرفة.';

$string['Version_Ok'] = 'You have compatible version of OpenMeetings: ';
$string['Version_Bad'] = 'You need to update your OpenMeetings, minimum version is: ';

$string['checkpeer'] = 'Check SSL certificate';
$string['checkpeerDesc'] = 'Enable/Disable SSL certificate checking, INSECURE if unchecked (default: checked)';
$string['checkhost'] = 'Hostname verification';
$string['checkhostDesc'] = 'Enable/Disable hostname verification, INSECURE if unchecked (default: checked)';

$string['upload_file'] = 'رفع ملف';
$string['room_files'] = 'ملفات الغرفة';
$string['wb_index'] = 'مؤشر السبورة البيضاء';
$string['om_file'] = 'ملف الغرفة موجود';
$string['room_file'] = 'ملف الغرفة';
$string['remove'] = 'إزالة';
$string['clean_wb'] = 'تنظيف السبورة البيضاء';

$string['No_Recordings'] = 'لا تسجيلات';
