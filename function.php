<?php
// functions.php
require_once 'config.php';

/** Helpers for file-based storage **/

function read_users() {
    return json_decode(file_get_contents(USERS_FILE), true) ?: [];
}

function write_users($users) {
    file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT));
}

function find_user_by_email($email) {
    $users = read_users();
    foreach ($users as $u) if (strtolower($u['email']) === strtolower($email)) return $u;
    return null;
}

function find_user_by_id($id) {
    $users = read_users();
    foreach ($users as $u) if ($u['id'] === $id) return $u;
    return null;
}

function save_user($user) {
    $users = read_users();
    $found = false;
    foreach ($users as $i => $u) {
        if ($u['id'] === $user['id']) { $users[$i] = $user; $found = true; break; }
    }
    if (!$found) $users[] = $user;
    write_users($users);
}

/** Quiz storage **/
function list_quizzes() {
    $files = glob(QUIZZES_DIR . '/*.json');
    $res = [];
    foreach ($files as $f) {
        $q = json_decode(file_get_contents($f), true);
        if ($q) $res[] = $q;
    }
    return $res;
}

function get_quiz($id) {
    $path = QUIZZES_DIR . "/$id.json";
    if (!file_exists($path)) return null;
    return json_decode(file_get_contents($path), true);
}

function save_quiz($quiz) {
    if (!isset($quiz['id'])) $quiz['id'] = uniqid('quiz_');
    $path = QUIZZES_DIR . "/{$quiz['id']}.json";
    file_put_contents($path, json_encode($quiz, JSON_PRETTY_PRINT));
    return $quiz['id'];
}

/** Responses **/
function read_responses() {
    return json_decode(file_get_contents(RESPONSES_FILE), true) ?: [];
}

function save_response($response) {
    $responses = read_responses();
    $responses[] = $response;
    file_put_contents(RESPONSES_FILE, json_encode($responses, JSON_PRETTY_PRINT));
}

/** Auth helpers **/
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    $user = find_user_by_id($_SESSION['user_id']);
    if (!$user || !($user['active'] ?? true)) {
        // disabled account
        session_destroy();
        echo "Compte désactivé ou introuvable.";
        exit;
    }
    return $user;
}

function require_role($role) {
    $user = require_login();
    if ($user['role'] !== $role) {
        http_response_code(403);
        echo "Accès refusé.";
        exit;
    }
}

/** Simple captcha (math) **/
function generate_captcha() {
    $a = rand(1, 9);
    $b = rand(1, 9);
    $_SESSION['captcha_answer'] = $a + $b;
    return "$a + $b = ?";
}

function verify_captcha($value) {
    return isset($_SESSION['captcha_answer']) && intval($value) === intval($_SESSION['captcha_answer']);
}
