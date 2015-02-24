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
 * The user is redirected to this page, once they have completed the login sequence with UNIC
 *
 * Here, we instantiate the plugin, and validate the ticket. If the ticket is valid, we set
 * a global variable to signal to the plugin that we have already validated the ticket.
 * 
 * The validation cannot happen in authenticate_user_login, since it expects a password, whereas
 * what we get in return is a timestamp and a key. 
 *
 * @package    auth_unilogin
 * @category   authentication
 * @copyright  2015 Jan Aagaard Meier (IT-Kartellet)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('MOODLE_INTERNAL', 1);

require('../../config.php');
require('./auth.php');

$username = required_param('user', PARAM_TEXT);
$auth = required_param('auth', PARAM_TEXT);
$timestamp = required_param('timestamp', PARAM_FLOAT);

$auth_plugin = new auth_plugin_unilogin();

if ($auth_plugin->validate_ticket($username, $timestamp, $auth)) {
    // We let the plugin know that this is a unilogin in user_login
    $GLOBALS['unilogin_in_progress'] = true;

    // Just passes time as a password. We have already validated that the ticket we got from UNI C is correct
    $user = authenticate_user_login($username, time());

    // Complete the user login sequence
    $user = get_complete_user_data('id', $user->id);

    $USER = complete_user_login($user);

    if (isset($SESSION->wantsurl) && !empty($SESSION->wantsurl)) {
        $urltogo = $SESSION->wantsurl;         
    } else {
        $urltogo = $CFG->wwwroot;
    }

    $USER->loggedin = true;
    $USER->site = $CFG->wwwroot;
    set_moodle_cookie($USER->username);

    redirect($urltogo);
} else {
    throw new moodle_exception('ticketvalidationerror', 'auth_unilogin');
}
