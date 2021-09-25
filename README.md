# apple-pusher

A simple library for creating and sending push-notifications to apple devices via APNs.
Supports sending over http/2 protocol preferred by Apple and both of authentication types: token-based and via ssl-cert.

### Installation
```composer require bibi4k0/apple-pusher```

### Usage
```php
use bIbI4k0\ApplePusher\BaseConfig;
use bIbI4k0\ApplePusher\Curl\CurlWrapper;
use bIbI4k0\ApplePusher\Sender;
use bIbI4k0\ApplePusher\Payload;

$auth = new TokenAuth(
    'your apns id', 
    'your team id', 
    'content from .p8 cert file or file path with prefix file:///'
);

$isSandbox = false;

$sender = new Sender(
    $auth,
    new CachedCurlWrapper(),
    new BaseConfig($isSandbox)
);

$payload = new AlertPayload($alertTitle, null, $alertText);
$push = new Push($device, $payload);
$push->setTopic($bundleId);

$resp = $sender->send($push);
if ($resp->isOk()) {
    echo 'push was sent successfully';
}
```

See also ```example/cmd.php```.

### Documentation
It's simple library and hardly needs any separate documentation.
Nevertheless, I tried to describe the code documentation well enough. See the source code for the more knowledges.
