#OpenMetings Video Conference for Moodle#

This Moodle plugin uses Apache OpenMeetings.
You need your own Apache OpenMeetings instance running.

[![Build Status](https://travis-ci.org/openmeetings/openmeetings-moodle-plugin.svg?branch=master)](https://travis-ci.org/openmeetings/openmeetings-moodle-plugin)

##Requirements##
PHP 7.0 or later, OpenMeetings 3.2.0 or later and Moodle 2.9 or later.

##tested Versions##
OpenMeetings: 3.2.0

Moodle: 3.0

##Building/Developing

* Checkout necessary branch `git checkout <branch_name>`
* Perform necessary edits
* update `<property name="project.version" value="2.0.2.0" />` in build.xml
* run `ant` command

As a result `version.php` packed will have correct `$plugin->release` and `$plugin->version` set

##Check out:##

http://openmeetings.apache.org

##Mailinglists##

* http://mail-archives.apache.org/mod_mbox/openmeetings-user/
* http://mail-archives.apache.org/mod_mbox/openmeetings-dev/

##Tutorials for installing OpenMeetings and Tools##

* https://cwiki.apache.org/confluence/display/OPENMEETINGS/Tutorials+for+installing+OpenMeetings+and+Tools

##Development: Apache Build Server & JIRA Issue Navigator ##

* https://builds.apache.org/view/M-R/view/OpenMeetings/
* https://issues.apache.org/jira/browse/OPENMEETINGS/?selectedTab=com.atlassian.jira.jira-projects-plugin:summary-panel

##Commercial Support links##

* http://openmeetings.apache.org/commercial-support.html
* mailto:om.unipro@gmail.com

