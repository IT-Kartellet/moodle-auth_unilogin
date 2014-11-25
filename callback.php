<?php
error_reporting(-1);
ini_set('display_errors', 'On');

define('MOODLE_INTERNAL', 1);

require('../../config.php');
require('./auth.php');

$auth = new auth_plugin_unilogin();

if (!empty($_REQUEST['user']) && !empty($_REQUEST['timestamp']) && !empty($_REQUEST['auth'])) {
    $username = $_REQUEST['user'];
    if ($auth->validate_ticket($username, $_REQUEST['timestamp'], $_REQUEST['auth'])) {
        echo 'authed!';

        // We let the plugin know that this is a unilogin in user_login
        $GLOBALS['unilogin_in_progress'] = true;

        // Just passes time as a password. We have already validated that the ticket we got from UNI C is correct
        $user = authenticate_user_login($username, time());

        if ($user === false) {
            // Do something smart
        }

        // Complete the user login sequence
        $user = get_complete_user_data('id', $user->id);
        if ($user === false) {
            // Do something else smart
        }

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
        echo 'failed';
        // fail!
    }
} else {
    // wrong return params... 
}
