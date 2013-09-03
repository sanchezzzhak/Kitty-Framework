<?

error_reporting(E_ALL | E_STRICT);

// Подключение фреймворка
include_once __DIR__ . "/kitty/init.php";

// Общий конфиг для backend и frontend
\kitty\app\config::load( require __DIR__ . "/app/config.php" );

// Если backend-config
if (preg_match('#^/admin/?([^/.,;?\n]+)?#i', $_SERVER['REQUEST_URI'])) {
    $config = require( __DIR__ . "/app/backend/config/main.php");
// Иначе frontend-config
} else {
    $config = require(__DIR__ . "/app/frontend/config/main.php");
}
// Запускаем приложение
$app = \kitty\app\app::make('\kitty\app\WebApplication', $config);
$app->run();



