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
 * @package    auth_unilogin
 * @copyright  2014 IT Kartellet
 * @license    MIT
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');

/**
 * Plugin for UNI login authentication
 */
class auth_plugin_unilogin extends auth_plugin_base {
    /**
     * Constructor.
     */
    function auth_plugin_unilogin() {
        $this->authtype = 'unilogin';
        $this->config = get_config('auth/saml');
    }

    private function get_url($action = 'login') {
        $prefix = $this->config->auth_type;
        $base = "https://{$prefix}.emu.dk/";

        switch ($action) {
            case 'login':
                $path = $this->encode_return_url();
                $auth = $this->encode_auth($path);
                return $base . 'unilogin/login.cgi?id={$id}&path=$path&auth=$auth';
            case 'logout':
                return $base . 'logout';
        }
    }

    private function encode_return_url() {
        global $CFG;

        // TODO something something url
        return urlencode(base64_encode());
    }

    private function encode_auth($path) {
        return md5($path . $this->config->secret);
    }

    private function validate_ticket($user, $timestamp, $auth) {
        $fingerprint = md5($timestamp . $this->config->secret . $user);
        if ($fingerprint !== $auth) {
            return false;
        }

        if ($this->config->validation_behaviour === 'db') {
            // TODO something someting DB
        } else {
            $timestamp = strtotime('YYYYMMDDhhmmss', $timestamp);
            return (time() - $this->config->validatetime) < $timestamp;
        }
    }

    function user_login($username, $password) {
        // return false;
    }

    /**
     * No password updates.
     */
    function user_update_password($user, $newpassword) {
        return false;
    }

    function is_internal() {
        return false;
    }

    function config_form($config, $err, $user_fields) {
        include "config.php";
    }

    /**
     * No changing of password.
     *
     * @return bool
     */
    function can_change_password() {
        return true;
    }

    /**
     * No password resetting.
     */
    function can_reset_password() {
        return true;
    }

    /**
     * Returns true if plugin can be manually set.
     *
     * @return bool
     */
    function can_be_manually_set() {
        return true;
    }
}


