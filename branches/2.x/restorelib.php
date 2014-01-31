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
    /// This php script contains all the stuff to backup/restore
    /// openmeetings mods

    //This function executes all the restore procedure about this mod
require_once($CFG->dirroot.'/mod/openmeetings/lib.php');

    function openmeetings_restore_mods($mod,$restore) {

        global $CFG;

        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

        if ($data) {
            //Now get completed xmlized object
            $info = $data->info;

            //traverse_xmlize($info);                                                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug
            // if necessary, write to restorelog and adjust date/time fields
            if ($restore->course_startdateoffset) {
               // restore_log_date_changes('openmeetings', $restore, $info['MOD']['#'], array('openmeetingsTIME'));
            }
            //Now, build the openmeetings record structure
            $openmeetings->course = $restore->course_id;
            $openmeetings->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $openmeetings->intro = backup_todb($info['MOD']['#']['INTRO']['0']['#']);
            $openmeetings->introformat = backup_todb($info['MOD']['#']['INTROFORMAT']['0']['#']);
            $openmeetings->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);
            $openmeetings->timecreated = backup_todb($info['MOD']['#']['TIMECREATED']['0']['#']);
            $openmeetings->teacher = backup_todb($info['MOD']['#']['TEACHER']['0']['#']);
	    $newid = openmeetings_add_instance($openmeetings);

            //Do some output
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("modulename","openmeetings")." \"".format_string(stripslashes($openmeetings->name),true)."\"</li>";
            }
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //Now check if want to restore user data and do it.
                if (restore_userdata_selected($restore,'openmeetings',$mod->id)) {
                    //Restore nothing
                    $status = false;
                }
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }

        return $status;
    }

    //Return a content decoded to support interactivities linking. Every module
    //should have its own. They are called automatically from
    //openmeetings_decode_content_links_caller() function in each module
    //in the restore process
    function openmeetings_decode_content_links ($content,$restore) {

        global $CFG;

        $result = $content;

        //Link to the list of openmeetingss

        $searchstring='/\$@(OPENMEETINGSINDEX)\*([0-9]+)@\$/';
        //We look for it
        preg_match_all($searchstring,$content,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //print_object($foundset);                                     //Debug
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (course id)
                $rec = backup_getid($restore->backup_unique_code,"course",$old_id);
                //Personalize the searchstring
                $searchstring='/\$@(OPENMEETINGSINDEX)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/openmeetings/index.php?id='.$rec->new_id,$result);
                } else {
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/openmeetings/index.php?id='.$old_id,$result);
                }
            }
        }

        //Link to openmeetings view by moduleid

        $searchstring='/\$@(OPENMEETINGSVIEWBYID)\*([0-9]+)@\$/';
        //We look for it
        preg_match_all($searchstring,$result,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //print_object($foundset);                                     //Debug
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (course_modules id)
                $rec = backup_getid($restore->backup_unique_code,"course_modules",$old_id);
                //Personalize the searchstring
                $searchstring='/\$@(OPENMEETINGSVIEWBYID)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/openmeetings/view.php?id='.$rec->new_id,$result);
                } else {
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/openmeetings/view.php?id='.$old_id,$result);
                }
            }
        }

        return $result;
    }

    //This function makes all the necessary calls to xxxx_decode_content_links()
    //function in each module, passing them the desired contents to be decoded
    //from backup format to destination site/course in order to mantain inter-activities
    //working in the backup/restore process. It's called from restore_decode_content_links()
    //function in restore process
    function openmeetings_decode_content_links_caller($restore) {
        global $CFG;
        $status = true;

        if ($openmeetingss = get_records_sql ("SELECT c.id, c.intro
                                   FROM {$CFG->prefix}openmeetings c
                                   WHERE c.course = $restore->course_id")) {
                                               //Iterate over each openmeetings->intro
            $i = 0;   //Counter to send some output to the browser to avoid timeouts
            foreach ($openmeetingss as $openmeetings) {
                //Increment counter
                $i++;
                $content = $openmeetings->intro;
                $result = restore_decode_content_links_worker($content,$restore);
                if ($result != $content) {
                    //Update record
                    $openmeetings->intro = addslashes($result);
                    $status = update_record("openmeetings",$openmeetings);
                    if (debugging()) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<br /><hr />'.s($content).'<br />changed to<br />'.s($result).'<hr /><br />';
                        }
                    }
                }
                //Do some output
                if (($i+1) % 5 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 100 == 0) {
                            echo "<br />";
                        }
                    }
                    backup_flush(300);
                }
            }
        }

        return $status;
    }

    //This function returns a log record with all the necessay transformations
    //done. It's used by restore_log_module() to restore modules log.
    function openmeetings_restore_logs($restore,$log) {

        $status = false;

        //Depending of the action, we recode different things
        switch ($log->action) {
        case "add":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "update":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "talk":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "view":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "view all":
            $log->url = "index.php?id=".$log->course;
            $status = true;
            break;
        case "report":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "report.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        default:
            if (!defined('RESTORE_SILENTLY')) {
                echo "action (".$log->module."-".$log->action.") unknown. Not restored<br />";                 //Debug
            }
            break;
        }

        if ($status) {
            $status = $log;
        }
        return $status;
    }

