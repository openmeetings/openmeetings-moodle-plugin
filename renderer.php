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
 * OpenMeetings module renderering methods are defined here
 *
 * @package    mod_openmeetings
 * @license    Apache-2.0 GPL-3.0-only
 * @copyright  OpenMeetings devs
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/openmeetings/lib.php');

/**
 * class to store OM activity instance
 */
class openmeetings implements renderable {
    /**
     * @var stdClass OM activity instance
     */
    public $om;

    /**
     * Constructor
     *
     * @param stdclass $openmeetings - OM activity instance
     */
    public function __construct(stdclass $openmeetings) {
        $this->om = $openmeetings;
    }
}

/**
 * class able to render OM activity instance
 */
class mod_openmeetings_renderer extends plugin_renderer_base {
    /**
     * No-op header
     */
    public function header() {
        // Designed to be empty.
    }

    /**
     * No-op footer
     */
    public function footer() {
        // Designed to be empty.
    }

    /**
     * Custom renderer for header
     *
     * @param openmeetings $openmeetings - holder for OM activity instance
     */
    private function out_header(openmeetings $openmeetings) {
        global $cm, $course, $CFG;

        $title = $course->shortname . ": " . $openmeetings->om->name;
        $this->page->set_title($title);
        $this->page->set_cacheable(false);
        $this->page->set_focuscontrol("");
        $this->page->set_url('/mod/openmeetings/view.php', array(
                'id' => $cm->id
        ));

        if ($openmeetings->om->whole_window > 0) {
            $out .= "<html" . $this->output->htmlattributes() . ">";
            $out .= html_writer::start_tag("head");
            $out .= html_writer::empty_tag("meta", array(
                    "http-equiv" => "pragma",
                    "content" => "no-cache")
                );
            $out .= html_writer::empty_tag("meta", array(
                    "http-equiv" => "expires",
                    "content" => "-1")
                );
            $out .= html_writer::empty_tag("meta", array(
                    "http-equiv" => "cache-control",
                    "content" => "no-cache")
                );
            $out .= html_writer::tag("title", $title);
            $out .= $this->output->standard_head_html();
            $out .= html_writer::end_tag("head");
            $out .= html_writer::start_tag("body", array("class" => "path-mod-openmeetings noMargin"));
        } else {
            $stropenmeetingss = get_string("modulenameplural", "openmeetings");

            $this->page->set_heading($course->fullname); // Required.
            $this->page->navbar->add($stropenmeetingss, null, null, navigation_node::TYPE_CUSTOM
                    , new moodle_url($CFG->wwwroot . '/user/index.php?id=' . $course->id));
            $this->page->navbar->add($openmeetings->om->name);
            $this->page->add_body_class('noMargin');

            $out .= $this->output->header();
        }
        return $out;
    }

    /**
     * Custom renderer for footer
     *
     * @param openmeetings $openmeetings - holder for OM activity instance
     */
    private function out_footer(openmeetings $openmeetings) {
        if ($openmeetings->om->whole_window > 0) {
            $out .= html_writer::end_tag("body");
            $out .= html_writer::end_tag("html");
        } else {
            $out .= $this->output->footer();
        }
        return $out;
    }

    /**
     * This function will render OM iframe (if login was successful)
     *
     * @param openmeetings $openmeetings - holder for OM activity instance
     */
    protected function render_openmeetings(openmeetings $openmeetings) {
        global $cm;

        $out .= $this->out_header($openmeetings);
        $context = context_module::instance($cm->id);
        $becomemoderator = false;
        if (has_capability('mod/openmeetings:becomemoderator', $context)) {
            $becomemoderator = true;
        }
        $gateway = new OmGateway(get_om_config());
        if ($gateway->login()) {
            $allowrecording = $openmeetings->om->allow_recording != 2;
            if ($openmeetings->om->is_moderated_room == 3) {
                $becomemoderator = true;
            }
            // Simulate the User automatically.
            if ($openmeetings->om->type != 'recording') {
                $hash = get_om_hash($gateway, array(
                    "roomId" => $openmeetings->om->room_id // Added for backward compatibility.
                    , "externalRoomId" => $openmeetings->om->id
                    , "moderator" => $becomemoderator
                    , "allowRecording" => $allowrecording));
            } else {
                $hash = get_om_hash($gateway, array("recordingId" => $openmeetings->om->room_recording_id));
            }

            if ($hash != "") {
                $url = $gateway->get_url() . "/hash?&secure=" . $hash . "&language=" . $openmeetings->om->language;
                $out .= html_writer::empty_tag("iframe", array(
                        "src" => $url,
                        "allow" => "microphone; camera; display-capture; fullscreen",
                        "class" => "openmeetings" . ($openmeetings->om->whole_window > 0 ? " wholeWindow" : "")
                ));
            }
        } else {
            $out .= "<p>Could not login User to OpenMeetings, check your OpenMeetings Module Configuration</p>";
        }

        $out .= $this->out_footer($openmeetings);
        return $out;
    }
}
