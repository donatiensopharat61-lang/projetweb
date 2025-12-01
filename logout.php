<?php
// logout.php
require_once 'config.php';
session_destroy();
header('Location: login.php');
exit;
require_once 'session_manager.php';
close_session();
