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
 * @copyright  2015 Jan Aagaard Meier (IT-Kartellet)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');

/**
 * Plugin for UNI login authentication
 * @copyright  2015 Jan Aagaard Meier (IT-Kartellet)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_plugin_unilogin extends auth_plugin_base {
    /**
     * Constructor.
     */
    function __construct() {
        $this->authtype = 'unilogin';
        $this->config = get_config('auth_unilogin');
    }

    /**
    * Hook for overriding behaviour of login page.
    * This method is called from login/index.php page for all enabled auth plugins.
    *
    * If the login behaviour is 'redirect' this hook will redirect the user directly to the UNILogin login page.
    * Otherwise it will inject some javascript into the page, which will in turn inject a link to the UNILogin page.
    *
    * @return void
    */
    public function loginpage_hook() {
        global $CFG, $PAGE;

        if (empty($CFG->alternateloginurl) && $this->config->login_behaviour === 'redirect' && @$_GET['unilogin'] !== 'false') {
            // login/index.php will automatically redirect based on this alternate login url.
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

    public function user_login($username, $password) {
        // If true, user_login was initiated by unilogin/callback.php.
        global $uniloginInProgress;
        if (isset($uniloginInProgress) && $uniloginInProgress === true) {
            return true;
        }

        return false;
    }

    /**
     * Get additional information about the user by querying the UNIC web-service.
     *
     * @param  string   $username
     * @return object
     */
    public function get_userinfo($username) {
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

    /**
     * Get the link for either logging in or out.
     *
     * @param  string   $action     The action to perform, one of 'login' and 'logout'
     * @return string
     */
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

    /**
     * Get the returnurl that the user should be redirected to after authenticating with UNILogin.
     *
     * @return string
     */
    private function get_returnurl() {
        global $CFG;

        return $CFG->wwwroot . '/auth/unilogin/callback.php';
    }

    /**
     * Encode the return url in the format expected by UNILogin.
     *
     * @return string
     */
    private function encode_return_url() {
        return urlencode(base64_encode($this->get_returnurl()));
    }

    /**
     * Encode the auth parameter in the format expected by UNILogin.
     *
     * @return string
     */
    private function encode_auth() {
        return md5($this->get_returnurl() . $this->config->secret);
    }

    /**
     * Validate the ticket we got from UNILogin based on the strategy chosen by the user.
     *
     * @param  string   $username
     * @param  int      $timestamp
     * @param  string   $auth
     * @return bool
     */
    public function validate_ticket($username, $timestamp, $auth) {
        $fingerprint = md5($timestamp . $this->config->secret . $username);
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

    /**
     * Hook for overriding behaviour of logout page.
     * This method is called from login/logout.php page for all enabled auth plugins.
     *
     * Redirects the user to the UNILogin logout page on logout.
     *
     * @global object
     * @global string
     */
    public function logoutpage_hook() {
        global $USER;

        if ($USER->auth == 'unilogin') {
            require_logout();

            redirect('https://sso.emu.dk/logout');
        }
    }

    /**
     * Returns true if this authentication plugin is "internal".
     *
     * @return bool
     */
    public function is_internal() {
        return false;
    }

    /**
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     *
     * @param object $config
     * @param object $err
     * @param array $userFields
     */
    public function config_form($config, $err, $userFields) {
        include("config.php");
    }

    /**
     * Processes and stores configuration data for this authentication plugin.
     *
     * @param object object with submitted configuration settings (without system magic quotes)
     */
    public function process_config($config) {
        foreach ((array)$config as $name => $value) {
            if (strstr($name, 's_auth_unilogin_')) {
                // This string prefix is generated by using the admin_setting_X classes - we just need the plain name.
                $name = str_replace('s_auth_unilogin_', '', $name);
                set_config($name, $value, 'auth_unilogin');
            } else if (strstr($name, 'lockconfig_')) {
                $name = str_replace('lockconfig_', '', $name);
                set_config($name, $value, 'auth_unilogin');
            }
        }
    }

    /**
     * Returns true if this authentication plugin can change the users'
     * password.
     *
     * @return bool
     */
    public function can_change_password() {
        return true;
    }

    /**
     * Returns the URL for changing the users' passwords
     *
     * @return string
     */
    public function change_password_url() {
        return 'https://brugerprofil.emu.dk/';
    }
}
