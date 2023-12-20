##Apache OpenMeetings Moodle Plugin Change Log

4.4.0 (2023122001)
 * Plugin should work as expected with Moodle 4.3.1 (Issue #52)
 * Build is green

4.3.0 (2022122001)
 * 'fullscreen' is added to allow list
 * Autocomplete is added for the recordings

4.2.0 (2021021001)
 * Room open in new window is fixed
 * `Module key` can contain space from now on
 * Weird `MOODLE_COURSE_ID_` is removed from genearted room name
 * `protocol/host/post/context` settings are replaced with `url`
 * Global setting to disable recording is added

4.1.3 (2020123001)
 * Arabic translation is added
 * User avatar pictures are fixed
 * Course backup is fixed
 * Activity Description is displayed in activity list

4.1.2 (2020121801)
 * Ability to access the room via externalId/type is added

4.1.1 (2020080111)
 * tested to work with Moodle 3.9, changes in Travis config

4.1.0 (202052401)
 * OM Version 5.0.0-M4 is required
 * MP4 download should work as expected
 * 'display-capture' is added to allow list
 * Framed version looks better
 * M4 related changes

4.0.2 (2019052401)
 * Tested with Moodle 3.7, changes in Travis config

4.0.1 (2018120601)
 * Code clean-up, all Travis warnings are eliminated

4.0.0 (2018101601)
 * White board files are added (OM 4.0.6 is required)

3.0.0.6 (2018072401)
 * Options to turn on/off SSL checks are made configurable in UI

3.0.0.5 (2018061501)
 * Recording download should be fixed if 'zlib.output_compression' is turned ON

3.0.0.4 (2018020901)
 * Additional iframe arrtibutes are added to bypass https://goo.gl/EuHzyv

3.0.0.3 (2017112101)
 * New room is created during restoring of the activity

3.0.0.2 (2017111511)
 * Backup/restore is fixed

3.0.0.1 (2017110100)
 * Detailed CURL messages only being printed if debug is enabled

3.0.0.0 (2017101000)
 * Moodle plugin is updated to work as expected with OM 4.0

2.0.0.6 (2016112701)
 * Moodle plugin is updated to work as expected with OM 3.2
 * Replaced html codes with actual russian texts (Merge pull request #16 from chkhanu/master)

2.0.0.5 (2016070701)
 * Recording download from 'Add OpenMeetings activity' page is fixed

2.0.0.4 (2016052401)
 * code clean-up: versions are updated, Travis build is set up

2.0.0.3 (2016051801)
 * config values from old plugins are preserved

2.0.0.2 (2016051501)
 * fixed two upgrade issues (added chat_hidden field and changed type of the field type)

2.0.0.1 (2016043001)
 * Moodle plugin is updated to work as expected with OM 3.1 and Moodle 3.0

1.7.5.1 (2015051801)
   * GPL header was added to the files

1.7.5 (2015051301)
   * added standard_intro_elements as description field instead of the old mform for intro/comment
   * installation and upgrade works with moodle 2.9.x and Openmeetings with version 3.0.+

1.7.4.1 (2015040201)
  * added function openmeetings_scale_used_anywhere
  * installation and upgrade works with moodle 2.7.x & 2.8.x and Openmeetings with version 3.0.+

1.7.4 (2015020701)
  * Open in new window function is fully implemented
  * Additional display mode is added
  * installation and upgrade works with moodle 2.7.x & 2.8.x and Openmeetings with version 3.0.+

1.7.3 (2014121301)
  * Recording query is fixed to work with new API
  * Page for displaying activities block is fixed
  * installation and upgrade works with moodle 2.7.x & 2.8.x and Openmeetings with version 3.0.+

1.7.2 (2014111101)
  * installation and upgrade works with moodle 2.7.x & 2.8.x and Openmeetings with version 3.0.+

1.7.1
  (2014062001)
  * Correct lists are being returned from Recording related API methods
  (2014061801)
  * 'Can not find data record' issue is fixed
  (2014051301)
  * installation and upgrade works with moodle 2.7.x and Openmeetings with version 3.0.+

1.7 (2014031605)
  * installation and upgrade works with moodle 2.2.x, 2.3.x, 2.4.x, 2.5.x & 2.6.x and Openmeetings with version 3.0.+
  * Openmeetings rooms (conference, webinar & interview) can now be displayed in Moodle courses in a frame or can occucy the entire window

1.6 (2014022101)
  * installation and upgrade works with moodle 2.4.x, 2.5.x & 2.6.x and Openmeetings with version 2.0.+
  * Refactoring to use REST instead of SOAP/NuSOAP Library
  * Download recordings made directly in Moodle UI
  * update language files
  * fix room types

20111019 (2011101901)
  * installation and upgrade works with moodle 2.0.x & 2.1.x and Openmeetings with version 1.9.+
