<?php
require_once 'config.php';

define('SESSIONS_FILE', DATA_DIR . '/sessions.json');

if (!file_exists(SESSIONS_FILE)) {
    file_put_contents(SESSIONS_FILE, json_encode([]));
}

function read_sessions() {
    return json_decode(file_get_contents(SESSIONS_FILE), true);
}

function write_sessions($sessions) {
    file_put_contents(SESSIONS_FILE, json_encode($sessions, JSON_PRETTY_PRINT));
}

function register_session($user_id) {
    $sessions = read_sessions();

    $sessions[] = [
        'session_id' => session_id(),
        'user_id' => $user_id,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'connected_at' => date('c')
    ];

    write_sessions($sessions);
}

function close_session() {
    $sessions = read_sessions();
    $sid = session_id();

    $sessions = array_filter($sessions, function ($s) use ($sid) {
        return $s['session_id'] !== $sid;
    });

    write_sessions($sessions);
}