<?php
require_once __DIR__ . '/includes/auth.php';
redirect(sudah_login() ? 'dashboard.php' : 'login.php');
