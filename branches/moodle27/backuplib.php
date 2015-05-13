<?php 
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

    // This php script contains all the stuff to backup/restore
    // openmeetings mods

    // This function executes all the backup procedure about this mod
    function openmeetings_backup_mods($bf,$preferences) {

        global $CFG;

        $status = true;

        // Iterate over openmeetings table
        $openmeetingss = get_records ("openmeetings","course",$preferences->backup_course,"id");
        if ($openmeetingss) {
            foreach ($openmeetingss as $openmeetings) {
                if (backup_mod_selected($preferences,'openmeetings',$openmeetings->id)) {
                    $status = openmeetings_backup_one_mod($bf,$preferences,$openmeetings);
                }
            }
        }
        return $status;
    }

    function openmeetings_backup_one_mod($bf,$preferences,$openmeetings) {

        global $CFG;

        if (is_numeric($openmeetings)) {
            $openmeetings = get_record('openmeetings','id',$openmeetings);
        }

        $status = true;

        // Start mod
        fwrite ($bf,start_tag("MOD",3,true));
        // Print openmeetings data
        fwrite ($bf,full_tag("ID",4,false,$openmeetings->id));
        fwrite ($bf,full_tag("MODTYPE",4,false,"openmeetings"));
        fwrite ($bf,full_tag("NAME",4,false,$openmeetings->name));
        fwrite ($bf,full_tag("INTRO",4,false,$openmeetings->intro));
        fwrite ($bf,full_tag("INTROFORMAT",4,false,$openmeetings->introformat));
        fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$openmeetings->timemodified));
        fwrite ($bf,full_tag("TIMECREATED",4,false,$openmeetings->timecreated));
        fwrite ($bf,full_tag("ROOM_ID",4,false,$openmeetings->room_id));
        fwrite ($bf,full_tag("TEACHER",4,false,$openmeetings->teacher));
        // End mod
        $status =fwrite ($bf,end_tag("MOD",3,true));

        return $status;
    }


    // Return an array of info (name,value)
    function openmeetings_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null) {

        if (!empty($instances) && is_array($instances) && count($instances)) {
            $info = array();
            foreach ($instances as $id => $instance) {
                $info += openmeetings_check_backup_mods_instances($instance,$backup_unique_code);
            }
            return $info;
        }
        // First the course data
        $info[0][0] = get_string("modulenameplural","openmeetings");
        if ($ids = openmeetings_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }
        return $info;
    }

    // Return an array of info (name,value)
    function openmeetings_check_backup_mods_instances($instance,$backup_unique_code) {
        // First the course data
        $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
        $info[$instance->id.'0'][1] = '';
        return $info;
    }

    // Return a content encoded to support interactivities linking.
    //Every module should have its own. They are called automatically from the backup procedure.
    function openmeetings_encode_content_links ($content,$preferences) {

        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        // Link to the list of openmeetingss
        $buscar="/(".$base."\/mod\/openmeetings\/index.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@OPENMEETINGSINDEX*$2@$',$content);

        // Link to openmeetings view by moduleid
        $buscar="/(".$base."\/mod\/openmeetings\/view.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@OPENMEETINGSVIEWBYID*$2@$',$result);

        return $result;
    }

    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    // Returns an array of openmeetingss id
    function openmeetings_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT c.id, c.course
                                 FROM {$CFG->prefix}openmeetings c
                                 WHERE c.course = '$course'");
    }

    // Returns an array of assignment_submissions id
    function openmeetings_message_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT m.id , m.openmeetingsid
                                 FROM {$CFG->prefix}openmeetings_messages m,
                                      {$CFG->prefix}openmeetings c
                                 WHERE c.course = '$course' AND
                                       m.openmeetingsid = c.id");
    }

    // Returns an array of openmeetings id
    function openmeetings_message_ids_by_instance ($instanceid) {

        global $CFG;

        return get_records_sql ("SELECT m.id , m.openmeetingsid
                                 FROM {$CFG->prefix}openmeetings_messages m
                                 WHERE m.openmeetingsid = $instanceid");
    }

