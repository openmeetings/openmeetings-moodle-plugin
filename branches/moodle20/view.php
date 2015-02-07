<?php  

require_once("../../config.php");
require_once("lib.php");
require_once("openmeetings_gateway.php");


$id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
$g  = optional_param('g', 0, PARAM_INT);

if (!empty($id)) {
    if (! $cm = get_coursemodule_from_id('openmeetings', $id)) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
        print_error('coursemisconf');
    }
    if (! $openmeetings = $DB->get_record("openmeetings", array("id"=>$cm->instance))) {
        print_error('invalidid', 'openmeetings');
    }

} else if (!empty($g)) {
    if (! $openmeetings = $DB->get_record("openmeetings", array("id"=>$g))) {
        print_error('invalidid', 'openmeetings');
    }
    if (! $course = $DB->get_record("course", array("id"=>$openmeetings->course))) {
        print_error('invalidcourseid');
    }
    if (!$cm = get_coursemodule_from_instance("openmeetings", $openmeetings->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
    $id = $cm->id;
} else {
    print_error('invalidid', 'openmeetings');
}

$url = new moodle_url('http://localhost/moodle22/mod/openmeetings/view.php?id=13');
$output_settings = unserialize($openmeetings->output_settings);

require_login($course->id);
add_to_log($course->id, "openmeetings", "view", "view.php?id=$cm->id", "$openmeetings->id");


$PAGE->set_url('/mod/openmeetings/view.php', array('id'=>$cm->id));
$PAGE->set_title("$course->shortname: ".format_string($openmeetings->name,true));
if ($output_settings->aspopup) {
        $PAGE->set_pagelayout('popup'); 

} else {
$PAGE->set_heading(format_string($course->fullname));
}

/////////////////////////////////////////////
/// Print the main part of the page
echo $OUTPUT->header();

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
    
$becomemoderator = 0;
if (has_capability('mod/openmeetings:becomemoderator', $context)) {
    $becomemoderator = 1;
}   	
    
$openmeetings_gateway = new openmeetings_gateway();
if ($openmeetings_gateway->openmeetings_loginuser()) {
    
    $allowRecording = 1;
    if ($openmeetings->allow_recording == 2) {
        $allowRecording = 0;
    }
    if ($openmeetings->is_moderated_room == 3) {
        $becomemoderator = 1;
    }
    
    // Simulate the User automatically
    //echo "openmeetings_setUserObjectWithExternalUser<br/>";
    if ($openmeetings->type != 0){
        $returnVal = $openmeetings_gateway->openmeetings_setUserObjectAndGenerateRoomHashByURLAndRecFlag($USER->username,$USER->firstname,
                        $USER->lastname,$USER->picture,$USER->email,$USER->id,$CFG->openmeetings_openmeetingsModuleKey,$openmeetings->room_id,$becomemoderator,$allowRecording);
    } else {
        $returnVal = $openmeetings_gateway->openmeetings_setUserObjectAndGenerateRecordingHashByURL($USER->username,$USER->firstname,
                        $USER->lastname,$USER->id,$CFG->openmeetings_openmeetingsModuleKey,$openmeetings->room_recording_id);
    }		
            
    if ($returnVal != "") {

        $scope_room_id = $openmeetings->room_id;

        if ($scope_room_id == 0) {
            $scope_room_id = "hibernate";
        }

        $iframe_d = new moodle_url("http://".$CFG->openmeetings_red5host.":".$CFG->openmeetings_red5port.
                                "/".$CFG->openmeetings_webappname);

        $urlparams = array('secureHash' => $returnVal,
                        'scopeRoomId' => $scope_room_id,
                        'language' => $openmeetings->language,
                        'picture' => $USER->picture,
                        'user_id' => $USER->id,
                        'moodleRoom' => 1,
                        'wwwroot' => $CFG->wwwroot);

        $iframe_d->params($urlparams);

        if ($output_settings->aspopup) {
            printf("<iframe src='%s' width='%s' height='%s' />",$iframe_d->out(),"100%", $output_settings->popupheight - 50);
        } else {
            printf("<iframe src='%s' width='%s' height='%s' />",$iframe_d->out(),"100%",640);
        }
    }
} else {
    echo "Could not login User to OpenMeetings, check your OpenMeetings Module Configuration";
    $OUTPUT->footer();
    exit();
}

/// Finish the page
echo $OUTPUT->footer();
