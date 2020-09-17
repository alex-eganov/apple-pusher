<?php

use Composer\Autoload\ClassLoader;

/** @var ClassLoader $loader */
$loader = require_once __DIR__ . '/../vendor/autoload.php';
$loader->setPsr4('bIbI4k0\\', __DIR__ . '/src/');

use bIbI4k0\ApplePusher\Auth\CertAuth;
use bIbI4k0\ApplePusher\Auth\TokenAuth;
use bIbI4k0\ApplePusher\Exception\CurlException;
use bIbI4k0\ApplePusher\Push;
use bIbI4k0\ApplePusher\Sender;
use bIbI4k0\ApplePusher\Payload\AlertPayload;

$helpText = <<< HELP
    --type      auth type: token, cert
    --cert      full path to the cert file
    --team-key  Team key. Only token auth
    --apns-key  Apns key. Only token auth
    --passwd    Passphrase for the cert file. Only cert auth
    --bundle    Bundle id for set in the topic. Required to set.
    --device    Device token. Required to set.
    --title     Alert title of the notification. Required to set.
    --text      Alert text of the notification
    --debug     Is debug mode. Enabled by default. Set to 0 for disable
    --help      This text

HELP;

if ($argc === 1) {
    echo $helpText;
    exit();
}

/**
 * @param string $msg
 * @param bool $withTime
 */
function printMessage(string $msg, bool $withTime = false): void {
    echo $withTime
        ? sprintf('%s: %s' . PHP_EOL,  (new DateTimeImmutable())->format('H:i:s'), $msg)
        : $msg . PHP_EOL;
}

/**
 * @param $jsonData
 */
function printAsPrettyJson($jsonData): void {
    echo json_encode($jsonData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
}

// read command-line params
$args = getopt('', ['type:', 'cert-file:', 'team-key:', 'apns-key:', 'passwd::', 'bundle:', 'device:', 'title:', 'text:', 'debug:']);

$type = $args['type'] ?? null;
if (!in_array($type, ['token', 'auth'], true)) {
    printMessage('Invalid auth type. Available: token, cert');
    exit(1);
}

$cert = trim($args['cert-file'] ?? null);
if (!$cert) {
    printMessage('Cert file is not set.');
    exit(1);
}
if (substr($cert, 1) !== '/' || substr($cert, 2, 1) !== ':\\') {
    $cert = __DIR__ . DIRECTORY_SEPARATOR . $cert;
    if (!is_readable($cert)) {
        printMessage('Cert file is not readable.');
        exit(1);
    }
}

$auth = null;
if ($type === 'token') {
    $teamId = $args['team-key'] ?? null;
    $apnsId = $args['apns-key'] ?? null;
    if (!$teamId || !$apnsId) {
        printMessage('Team key or APNS key is not set.');
        exit(1);
    }
    $cert = 'file:///' . $cert;
    $auth = new TokenAuth($apnsId, $teamId, $cert);
} else { // $type === cert
    $passwd = trim($args['passwd']);
    $auth = new CertAuth($cert, $passwd);
}

$isDebug = !isset($args['debug']) || $args['debug'] != '0';

$bundleId = $args['bundle'] ?? null;
if (!$bundleId) {
    printMessage('Bundle ID is not set.');
    exit(1);
}

$device = trim($args['device'] ?? null);
if (!$device) {
    printMessage('Device token is not set.');
    exit(1);
}

$alertTitle = trim($args['title'] ?? null);
if (!$alertTitle) {
    printMessage('Alert title is not set.');
    exit(1);
}
$alertText = $args['text'] ?? null;

$payload = new AlertPayload($alertTitle, null, $alertText);
$push = new Push($device, $payload);
$push->setTopic($bundleId);

$sender = new Sender($auth, $isDebug);
try {
    $resp = $sender->send($push);
    printAsPrettyJson($resp);

    if ($resp->isOk()) {
        printMessage('Push was sent successfully');
    } else {
        printMessage('Push sent was failed. Reason: ' . $resp->getReason());
    }
} catch (CurlException $e) {
    printMessage($e->getMessage());
    exit(1);
}
