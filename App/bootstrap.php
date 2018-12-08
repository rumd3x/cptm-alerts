<?php
require __DIR__.'/../vendor/autoload.php';
use CptmAlerts\Modules\Core;
use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$logger = new Logger('CPTM Alerts');
$logger->pushHandler(new StreamHandler(__DIR__ . '/../Storage/Logs/app.log'));
$logger->info('Application initializing.');
$_ENV['time_start'] = microtime(true);
try {
    $dotenv = new Dotenv(__DIR__ . '/..');
    $dotenv->load();
    $dotenv->required('SLACK_KEY')->notEmpty();
    $dotenv->required('SLACK_CHANNEL')->notEmpty();

    $core = new Core($logger);
    $code = $core->run();
    $logger->info(sprintf("Application executed gracefully! Exit code: %d in %f seconds.", $code, microtime(true) - $_ENV['time_start']));
} catch (Exception $e) {
    $logger->error(
        sprintf("Runtime Error in %f seconds", (microtime(true) - $_ENV['time_start'])),
        [
            'Message' => $e->getMessage(),
            'File' => $e->getFile(),
            'Line' => $e->getLine(),
            'Code' => $e->getCode(),
            'Trace' => $e->getTraceAsString(),
        ]
    );
}
