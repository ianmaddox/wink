<?php

/**
 * Wink SDK for PHP
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * See the file license.txt for copying permission.
 * 
 * @author    Ian Maddox <oss@ianmaddox.com>
 * @copyright 2014
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */
// Example for nimbus device
// The @var declarations below are typehints to IDEs which support code completion.

require_once("vendor/autoload.php");

use ianmaddox\wink\wink;

// Instantiate a new Wink API object
$wink = new wink();

// Load credentials from the config.json file in this same dir.
// Please make sure to use your Wink credentials. This is the email address and password
// you used to log in to the Wink app on your smartphone.  The API credentials are obtained
// from Quirky support.
$conf = json_decode(file_get_contents('config.json'));

// Authenticate against the API
$wink->authPassword($conf->clientID, $conf->clientSecret, $conf->username, $conf->password);

// Get the first available nimbus device in the account.
$nimbus = $wink->getDevice(wink::DEVICE_NIMBUS);
/* @var $nimbus \ianmaddox\wink\devices\nimbus */
$nimbus->print_r($nimbus->getClock());

// Grab the far right dial
$dial3 = $nimbus->getDial(3);
/* @var $dial3 \ianmaddox\wink\devices\nimbusDial */

// Don't automatically save after each set.  We're going to be modifying several values first.
$dial3->setAutoSave(false);

// Set the label and alternate label
$dial3->setText(date('g:i:s'), "Hello");

// Move the needle to the current seconds value
$dial3->setDegrees(date('s') / 60 * 360);

// Save the data to the dial
$dial3->save();

