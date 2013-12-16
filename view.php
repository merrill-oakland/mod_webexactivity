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
 * An activity to interface with WebEx.
 *
 * @package   mod_webexactvity
 * @copyright Eric Merrill (merrill@oakland.edu)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Code to view the passed webex.

require('../../config.php');

$id = optional_param('id', 0, PARAM_INT); // Course module ID.
$action = optional_param('action', false, PARAM_ALPHA);


$cm = get_coursemodule_from_id('webexactivity', $id, 0, false, MUST_EXIST);
$webex = $DB->get_record('webexactivity', array('id' => $cm->instance), '*', MUST_EXIST);

$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/webexactivity:view', $context);

$canhost    = has_capability('mod/webexactivity:hostmeeting', $context);

// Do redirect actions here.
switch ($action) {
    case 'hostmeeting':
        if (!$canhost) {
            // TODO Error here.
            return;
        }
        $webexobj = new \mod_webexactivity\webex();
        $webexuser = $webexobj->get_webex_user($USER);
        $hosturl = \mod_webexactivity\webex::get_meeting_host_url($webex);
        $authurl = $webexobj->get_login_url($webex, $webexuser, false, $hosturl);
        redirect($authurl);
        break;
    case 'joinmeeting':
        $joinurl = \mod_webexactivity\webex::get_meeting_join_url($webex, $USER);
        redirect($joinurl);
        break;
}




add_to_log($course->id, 'webexactivity', 'view', 'view.php?id='.$cm->id, $webex->id, $cm->id);

$PAGE->set_url('/mod/webexactivity/view.php', array('id' => $cm->id));

$PAGE->set_title($course->shortname.': '.$webex->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_activity_record($webex);


echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($webex->name), 2);

echo $OUTPUT->box_start();

//echo '<a href="?id='.$id.'&action=hostmeeting" target="_blank">Host</a><br>';
//echo '<a href="?id='.$id.'&action=joinmeeting" target="_blank">Join</a><br>';


echo '<table align="center" cellpadding="5">' . "\n";

$formelements = array(
    get_string('description','webexactivity')  => $webex->intro,
    get_string('starttime', 'webexactivity')      => userdate($webex->starttime),
    get_string('duration', 'webexactivity')        => $webex->length
);

foreach ($formelements as $key => $val) {
   echo '<tr valign="top">' . "\n";
   echo '<td align="right"><b>' . $key . ':</b></td><td align="left">' . $val . '</td>' . "\n";
   echo '</tr>' . "\n";
}

if ($canhost) {
    echo '<tr><td colspan=2 align="center"><a href="?id='.$id.'&action=hostmeeting" target="_blank">Host meeting</a></td></tr>';
}
echo '<tr><td colspan=2 align="center"><a href="?id='.$id.'&action=joinmeeting" target="_blank">Join as participant</a></td></tr>';

echo '</table>';

//echo userdate($webex->starttime);


//$urlbase = get_config('webexactivity', 'url').'.webex.com/oakland-dev';
//$url = $urlbase.'/m.php?AT=JM&MK='.$webex->meetingkey;

//echo $url;

//$url = $urlbase.'/m.php?AT=HM&MK='.$webex->meetingkey;
//$url = \mod_webexactivity\webex::get_meeting_host_url($webex);
//echo $url;


/*
$connector = new \mod_webexactivity\service_connector();
$stat = $connector->retrieve(\mod_webexactivity\xml_generator::get_training_info('344204292-'));
if ($stat) {
    print "<pre>";
    print_r($connector->get_response_array());
    print "</pre>";
} else {
    print "<pre>";
    print_r($connector->get_errors());
    print "</pre>";
}
*/
/*$connector = new \mod_webexactivity\service_connector();
$stat = $connector->retrieve(\mod_webexactivity\xml_generator::get_user_info('adm_merrill'));
if ($stat) {
    print "<pre>";
    print_r($connector->get_response_array());
    print "</pre>";
} else {
    print "<pre>";
    print_r($connector->get_errors());
    print "</pre>";
}*/

//$webexobj = new \mod_webexactivity\webex();
//print_r($webexobj->get_training_info($webex, $USER));
/*
$webexuser = $webexobj->get_webex_user($USER);
$hosturl = \mod_webexactivity\webex::get_meeting_host_url($webex);
echo $hosturl;*/
/*$webexobj = new \mod_webexactivity\webex();
$webexuser = $webexobj->get_webex_user($USER);
$hosturl = \mod_webexactivity\webex::get_meeting_host_url($webex);

$authurl = $webexobj->get_login_url($webex, $webexuser, false, $hosturl);
echo '<a href="'.$authurl.'" target="_blank">Host</a>';*/
//$webex = new \mod_webexactivity\webex();
//$webex->get_webex_user($USER, false);
echo \mod_webexactivity\webex::get_meeting_join_url($webex);

echo $OUTPUT->box_end();

echo $OUTPUT->footer();
