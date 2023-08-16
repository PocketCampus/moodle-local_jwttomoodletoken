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

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {


    $settings =
            new admin_settingpage('local_jwttomoodletoken', new lang_string('pluginname', 'local_jwttomoodletoken'));

    $settings->add(new admin_setting_configtext('local_jwttomoodletoken/userinfo_url',
            get_string('userinfo_url', 'local_jwttomoodletoken'), '', '', PARAM_URL));

    $settings->add(new admin_setting_configtext('local_jwttomoodletoken/username_attribute',
            get_string('username_attribute', 'local_jwttomoodletoken'), '', '', PARAM_ALPHANUM));

    $ADMIN->add('localplugins', $settings);
}

