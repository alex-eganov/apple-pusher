# apple-pusher

A simple library for creating and sending push-notifications to apple devices via APNs.
Supports sending over http/2 protocol preferred by Apple and both of authentication types: token-based and via ssl-cert.

### Installation
```
# for PHP 8.* only
composer require bibi4k0/apple-pusher 

# for PHP 7.4+ or PHP 8.*
composer require bibi4k0/apple-pusher:^1.1

# for PHP 7.2+
composer require bibi4k0/apple-pusher:^1.0
```

### Usage
```php
use bIbI4k0\ApplePusher\BaseConfig;
use bIbI4k0\ApplePusher\Curl\CurlWrapper;
use bIbI4k0\ApplePusher\Sender;
use bIbI4k0\ApplePusher\Payload;

$auth = new TokenAuth(
    'your apns id', 
    'your team id', 
    'content from .p8 cert file or file path with prefix file://'
);

$isSandbox = false;

$sender = new Sender(
    $auth,
    new CachedCurlWrapper(),
    new BaseConfig($isSandbox)
);

$payload = new AlertPayload('Hello dude');
$push = new Push('device token', $payload);
$push->setTopic('bundle id of your app');

$resp = $sender->send($push);
if ($resp->isOk()) {
    echo 'push was sent successfully';
}
```

See also ```example/cmd.php```.

### Documentation
It's simple library and hardly needs any separate documentation.
Nevertheless, I tried to describe the code documentation well enough. See the source code for the more knowledges.

### Tests
Some unit tests are included. Still not enough, but I'm working on it :)

Can run it by command:

```composer run unit```
