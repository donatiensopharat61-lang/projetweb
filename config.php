<?php
// config.php
session_start();

define('DATA_DIR', __DIR__ . '/data');
define('USERS_FILE', DATA_DIR . '/users.json');
define('RESPONSES_FILE', DATA_DIR . '/responses.json');
define('QUIZZES_DIR', DATA_DIR . '/quizzes');

// ensure directories exist
if (!file_exists(DATA_DIR)) mkdir(DATA_DIR, 0755, true);
if (!file_exists(QUIZZES_DIR)) mkdir(QUIZZES_DIR, 0755, true);
if (!file_exists(USERS_FILE)) file_put_contents(USERS_FILE, json_encode([]));
if (!file_exists(RESPONSES_FILE)) file_put_contents(RESPONSES_FILE, json_encode([]));

// Admin account default (if not exists)
$users = json_decode(file_get_contents(USERS_FILE), true) ?: [];
$adminExists = false;
foreach ($users as $u) {
    if (isset($u['role']) && $u['role'] === 'admin') { $adminExists = true; break; }
}
if (!$adminExists) {
    // default admin: admin@example.com / admin123 (recommend to change)
    $users[] = [
        'id' => uniqid('user_'),
        'email' => 'admin@example.com',
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
        'first_name' => 'Admin',
        'last_name' => 'Quizzeo',
        'role' => 'admin',
        'active' => true,
        'created_at' => date('c')
    ];
    file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT));
}
