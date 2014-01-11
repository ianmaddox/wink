<?php
// Example for nimbus device
require_once("/Users/ian/Dropbox/dev/composer/vendor/autoload.php");
use ianmaddox\wink\wink;
$wink = new wink();
$conf = json_decode(file_get_contents('config.json'));

$wink->authPassword($conf->clientID, $conf->clientSecret, $conf->username, $conf->password);
$nimbus = $wink->getDevice(wink::DEVICE_NIMBUS, 2211);
/* @var $nimbus ianmaddox\wink\devices\nimbus */
//$nimbus->getClock();
//$nimbus->getDialTemplates();
$nimbus->getDial(8970);
//$nimbus->updateDial(8970, 3, 'LOL', 'Cool.', rand(0,359), rand(1,100));
