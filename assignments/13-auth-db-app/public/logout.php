<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';
require_once dirname(__DIR__) . '/src/auth.php';

logout_current_user();
redirect_to('/login.php?status=logged-out');
