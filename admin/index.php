<?php
require_once __DIR__ . '/../includes/functions.php';
if (is_admin_logged_in()) {
    redirect('dashboard.php');
}
redirect('login.php');
