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
 * This is the main file of the authentication plugin.
 *
 * @package    auth_unilogin
 * @category   authentication
 * @copyright  2015 Jan Aagaard Meier (IT-Kartellet)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');

/**
 * Plugin for UNI login authentication
 */
class auth_plugin_unilogin extends auth_plugin_base {
    function auth_plugin_unilogin() {
        $this->authtype = 'unilogin';
        $this->config = get_config('auth_unilogin');
    }

    function loginpage_hook() {
        global $CFG, $PAGE;

        if (empty($CFG->alternateloginurl) && $this->config->login_behaviour === 'redirect' && @$_GET['unilogin'] !== 'false') {
            // login/index.php will automatically redirect based on this alternate login url
            $CFG->alternateloginurl = $this->get_url();
        } else {
            $PAGE->requires->yui_module(
                'moodle-auth_unilogin-link_injector', 
                'M.auth_unilogin.link_injector.init', 
                array(
                    $this->get_url(), 
                    $this->config->login_behaviour_link_text, 
                    $this->config->login_behaviour_link_selector
                )
            );
        }
    }

    function user_login($username, $password) {
        // if true, user_login was initiated by unilogin/callback.php
        if(isset($GLOBALS['unilogin_in_progress']) && $GLOBALS['unilogin_in_progress'] === true) {
            unset($GLOBALS['unilogin_in_progress']);
            return true;
        }

        return false;
    }

    function get_userinfo($username) {
        // Query the webservice for user info http://stil.dk/~/media/UNIC/Filer/Publikationer/Tekniske%20vejledninger/uni-login-infotjenestenswebservice_ws02.pdf

        try {
            $client = new SoapClient('https://ws02.infotjeneste.uni-c.dk/infotjeneste-ws/ws?WSDL');
            $user = $client->hentPerson(array(
                'wsBrugerid' => $this->config->wsid,
                'wsPassword' => $this->config->wssecret,
                'brugerid' => $username
            ))->return;
        } catch (SoapFault $e) {
            throw new moodle_exception('webserviceerror', 'auth_unilogin', '', null, $e);            
        }
        
        return array(
            'username' => $username,
            'lastname' => $user->Efternavn,
            'firstname' => $user->Fornavn,
            'email' => $user->Mailadresse,
        );
    }

    private function get_url($action = 'login') {
        $prefix = $this->config->login_type;
        $base = "https://{$prefix}.emu.dk/";

        switch ($action) {
            case 'login':
                $id = $this->config->id;
                $path = $this->encode_return_url();
                $auth = $this->encode_auth($path);

                return $base . "unilogin/login.cgi?id={$id}&path=$path&auth=$auth";
            case 'logout':
                return $base . 'logout';
        }
    }

    private function get_returnurl() {
        global $CFG;

        return $CFG->wwwroot . '/auth/unilogin/callback.php';
    }

    private function encode_return_url() {
        return urlencode(base64_encode($this->get_returnurl()));
    }

    private function encode_auth($path) {
        return md5($this->get_returnurl() . $this->config->secret);
    }

    function validate_ticket($user, $timestamp, $auth) {
        $fingerprint = md5($timestamp . $this->config->secret . $user);
        if ($fingerprint !== $auth) {
            return false;
        }

        if ($this->config->validation_behaviour === 'db') {
            global $DB;

            if ($DB->record_exists('unilogin_tickets', array('ticket' => $auth))) {
                return false;
            }

            $DB->insert_record('unilogin_tickets', array('ticket' => $auth));
            return true;
        } else {
            $timestamp = DateTime::createFromFormat('YmdHis T', $timestamp . ' Z')->getTimestamp();
            return (time() - $this->config->validatetime) < $timestamp;
        }
    }    

    function logoutpage_hook() {
        global $USER;

        if ($USER->auth == 'unilogin') {
            require_logout();

            redirect('https://sso.emu.dk/logout');
        }
    }

    function is_internal() {
        return false;
    }

    function config_form($config, $err, $user_fields) {
        include "config.php";
    }

    function process_config($config) {
        foreach ((array)$config as $name => $value) {
            if (strstr($name, 's_auth_unilogin_')) {
                // This string prefix is generated by using the admin_setting_X classes - we just need the plain name
                $name = str_replace('s_auth_unilogin_', '', $name);
                set_config($name, $value, 'auth_unilogin');
            } else if (strstr($name, 'lockconfig_')) {
                $name = str_replace('lockconfig_', '', $name);
                set_config($name, $value, 'auth_unilogin');
            }
        }
    }

    function can_change_password() {
        return true;
    }

    function change_password_url() {
        return 'https://brugerprofil.emu.dk/';
    }
}
