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
 * @package    local_jwttomoodletoken
 * @author     Nicolas Dunand <nicolas.dunand@unil.ch>
 * @author     Amer Chamseddine <amer@pocketcampus.org>
 * @copyright  2023 Copyright PocketCampus Sàrl {@link https://pocketcampus.org/}
 * @copyright  based on work by 2020 Copyright Université de Lausanne, RISET {@link http://www.unil.ch/riset}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

class local_jwttomoodletoken_external extends external_api {

    /**
     * @return external_multiple_structure
     */
    public static function gettoken_returns() {
        return new external_single_structure([
                'moodletoken' => new external_value(PARAM_ALPHANUM, 'valid Moodle mobile token')
        ]);
    }

    /**
     * @param $useremail
     * @param $since
     *
     * @return array
     * @throws coding_exception
     * @throws invalid_parameter_exception
     */
    public static function gettoken($accesstoken) {
        global $CFG, $DB, $PAGE, $SITE, $USER;
        $PAGE->set_url('/webservice/rest/server.php', []);
        $params = self::validate_parameters(self::gettoken_parameters(), [
                'accesstoken' => $accesstoken
        ]);

        $userinfo_url = get_config('local_jwttomoodletoken', 'userinfo_url');
        $username_attribute = get_config('local_jwttomoodletoken', 'username_attribute');

        $user_attributes = json_decode(
            file_get_contents(
                $userinfo_url,
                false,
                stream_context_create(
                    array(
                        'http' => array(
                            'ignore_errors' => true,
                            'header' => "Authorization: Bearer {$params['accesstoken']}"
                        )
                    )
                )
            ),
            true
        );
        if ($user_attributes['error'] == 'invalid_grant') {
            throw new moodle_exception('invalidaccesstoken', 'webservice');
        }
        if ($user_attributes['error']) {
            throw new moodle_exception('userinfoerror', 'webservice', '', $user_attributes);
        }

        $username = $user_attributes[$username_attribute];
        if (is_array($username)) {
            $username = array_shift($username);
        }
        if (!$username) {
            throw new moodle_exception('usernamenotfound', 'webservice');
        }

        $user = $DB->get_record('user', [
                'username'  => $username,
                //'auth'      => 'shibboleth',
                'suspended' => 0,
                'deleted'   => 0
        ], '*', IGNORE_MISSING);
        if (!$user) {
            throw new moodle_exception('usernotfound', 'webservice', '', $username);
        }

        // Check if the service exists and is enabled.
        $service = $DB->get_record('external_services', [
                'shortname' => 'moodle_mobile_app',
                'enabled'   => 1
        ]);
        if (empty($service)) {
            throw new moodle_exception('servicenotavailable', 'webservice');
        }

        // Ugly hack.
        $realuser = $USER;
        $USER = $user;
        $token = external_generate_token_for_current_user($service);
        $USER = $realuser;

        external_log_token_request($token);

        return [
                'moodletoken' => $token->token
        ];
    }

    /**
     * @return external_function_parameters
     */
    public static function gettoken_parameters() {
        return new external_function_parameters([
                'accesstoken' => new external_value(PARAM_RAW_TRIMMED, 'the OAuth2 access_token')
        ]);
    }

}

