#!/usr/bin/env php
<?php

/**
 * Kolab inspection script
 *
 * @author Christian Mollekopf <mollekopf@apheleia-it.ch>
 *
 * Copyright (C) 2026, Apheleia IT AG <contact@apheleia-it.ch>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

define('INSTALL_PATH', __DIR__ . '/../../../');
ini_set('display_errors', 1);
libxml_use_internal_errors(true);

require_once INSTALL_PATH . 'program/include/clisetup.php';

function print_usage()
{
    print "Usage:  inspect.sh --user username --password password operation\n";
    print "-u, --user     Username\n";
    print "-p, --password Password\n";
}

// read arguments
$opts = rcube_utils::get_opt(array(
    'u' => 'username',
    'p' => 'password',
    'v' => 'verbose',
));

$operation = $opts[0];
$user = $opts['username'];
$password = $opts['password'];
$verbose = $opts['verbose'];

$rc = rcmail::get_instance();

if ($verbose) {
    $rc->config->set('kolab_api_debug', true);
}

// check the initial error state
if ($rc->config->get_error() || $rc->db->is_error()) {
     rcmail_fatal_error();
}

// Login
$auth = $rc->plugins->exec_hook('authenticate', [
    'host'  => $rc->autoselect_host(),
    'user'  => $user,
    'pass'  => $password,
    'valid' => true,
    'error' => null,
    'cookiecheck' => true,
]);

if ($auth['valid'] && !$auth['abort']
    && $rc->login($auth['user'], $auth['pass'], $auth['host'], $auth['cookiecheck'])) {
    print("Logged in");
}

// Run plugin startup
$startup = $rc->plugins->exec_hook('startup', ['task' => 'cli', 'action' => 'cli']);

if ($operation == "dumpconfig") {
    var_export($rc->config);
}
if ($operation == "dumpfolders") {
    # TODO
}
