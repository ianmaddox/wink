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

namespace ianmaddox\wink;

/**
 * Basic functions to interact with the Quirky Wink API.
 * Allows developers to build more sophisticated applications using Wink devices such
 * as the Nimbus desktop dashboard or the Spotter multipurpose sensor.
 */
class wink {

	const API_HOST = 'winkapi.quirky.com';
	const DEVICE_EGGMINDER = 'eggminder';
	const DEVICE_NIMBUS = 'nimbus';
	const DEVICE_NIMBUSDIAL = 'nimbusDial';
	const DEVICE_PIVOTPOWERGENIUS = 'pivotPowerGenius';
	const DEVICE_PORKFOLIO = 'porkfolio';
	const DEVICE_SPOTTER = 'spotter';

	private $access_token;
	private $refresh_token;
	private $token_endpoint;
	private $devices;
	private $deviceMap = array(
		'cloud_clock_id' => self::DEVICE_NIMBUS,
		'eggtray_id' => self::DEVICE_EGGMINDER,
		'powerstrip_id' => self::DEVICE_PIVOTPOWERGENIUS,
		'piggy_bank_id' => self::DEVICE_PORKFOLIO,
		'sensor_pod_id' => self::DEVICE_SPOTTER
	);
	
	private $debug = 1;

	public function __construct() {
		
	}

	/**
	 * Authenticate with a password.  Once complete, we get an access token for future
	 * requests.
	 * 
	 * @param string $clientID
	 * @param string $clientSecret
	 * @param string $username
	 * @param string $password
	 * @return bool success
	 */
	public function authPassword($clientID, $clientSecret, $username, $password) {
		$this->access_token = false;
		$this->refresh_token = false;
		$this->token_endpoint = false;

		$action = '/oauth2/token';

		$data = array(
			'client_id' => $clientID,
			'client_secret' => $clientSecret,
			'username' => $username,
			'password' => $password,
			'grant_type' => 'password'
		);

		$resp = $this->doRequest($action, $data);
		if(!empty($resp) && empty($resp['errors'])) {
			$this->access_token = $resp['data']['access_token'];
			$this->refresh_token = $resp['data']['refresh_token'];
			$this->token_endpoint = $resp['data']['token_endpoint'];
			$this->getDevices();
			return true;
		}
		$this->devices = null;
		return false;
	}

	/**
	 * List all of the devices associated with the active account
	 * @link http://docs.wink.apiary.io/#get-%2Fusers%2Fme%2Fwink_devices
	 * @return array
	 */
	public function getDevices() {
		$action = '/users/me/wink_devices';
		$devices = $this->doRequest($action);

		// Loop over all of the devices returned and identify them
		foreach($devices['data'] as $deviceData) {
			foreach($this->deviceMap as $idField => $deviceType) {
				if(isset($deviceData[$idField])) {
					// We found a match.  Pull the ID and create a device object
					$deviceID = $deviceData[$idField];
					$this->devices[$deviceType][$deviceID] = $this->makeDevice($deviceType, $deviceData);
					continue;
				}
			}
		}
		return $this->devices;
	}

	/**
	 * Generate an actual device instance.
	 * 
	 * @param string $deviceType
	 * @param array $deviceData
	 * @return \ianmaddox\wink\device
	 */
	public function makeDevice($deviceType, array $deviceData) {
		// Validate the deviceType
		$deviceConst = 'self::DEVICE_' . strtoupper($deviceType);
		if(!defined($deviceConst)) {
			trigger_error("Invalid wink device $deviceType", E_USER_ERROR);
		}
		$class = 'ianmaddox\wink\devices\\' . $deviceType;
		return new $class($this, $deviceData);
	}

	/**
	 * Return an instance of a particular device class.  If already authenticated,
	 * the device class will be granted access automatically.
	 * @param const $deviceType See self::DEVICE_* constants 
	 * @param string $deviceID Wink device ID as seen in self::getDevices().  If not provided, returns the first one.
	 * @return device
	 */
	public function getDevice($deviceType, $deviceID = false) {
		if($deviceID) {
			return isset($this->devices[$deviceType][$deviceID]) ? $this->devices[$deviceType][$deviceID] : false;
		}
		if(empty($this->devices[$deviceType])) {
			return false;
		}
		// They didn't ask for a specific device, so just return the first one in we got for this type.
		reset($this->devices[$deviceType]);
		return current($this->devices[$deviceType]);
	}

	/**
	 * Enumerate the available devices by their type.
	 * 
	 * @return array
	 */
	public function getDeviceList() {
		$deviceList = array();
		foreach($this->devices as $deviceType => $typeDevices) {
			foreach($typeDevices as $deviceID => $data) {
				$deviceList[$deviceType][] = $deviceID;
			}
		}
		return $deviceList;
	}

	/**
	 * Return the current user's profile
	 * 
	 * @link http://docs.wink.apiary.io/#get-%2Fusers%2Fme
	 * @return array
	 */
	public function getUserProfile() {
		$action = '/users/me';
		return $this->doRequest($action);
	}

	/**
	 * Set the current user's email address.  New address requires confirmation before
	 * API credentials change.
	 * 
	 * @link http://docs.wink.apiary.io/#put-%2Fusers%2Fme
	 * @param string $email
	 * @return array
	 */
	public function setUserEmail($email) {
		$action = '/users/me';
		return $this->doRequest($action, array('email' => $email), array(), 'PUT');
	}

	/**
	 * List the services linked to this account
	 * 
	 * @link http://docs.wink.apiary.io/#get-%2Fusers%2Fme%2Flinked_services
	 * @return array
	 */
	public function getLinkedServices() {
		$action = '/users/me/linked_services';
		return $this->doRequest($action);
	}

	/**
	 * The API is not sufficiently documented at this time.
	 * @link http://docs.wink.apiary.io/#post-%2Fusers%2Fme%2Flinked_services
	 */
	public function createLinkedService() {
		trigger_error("Method not yet implemented", E_USER_WARNING);
		$action = '/users/me/linked_services';
	}

	/**
	 * Lists available icons
	 * @link http://docs.wink.apiary.io/#get-%2Ficons
	 * @return type
	 */
	public function getIcons() {
		$action = '/icons';
		return $this->doRequest($action);
	}

	/**
	 * Retuns the list of available channels
	 * @link http://docs.wink.apiary.io/#channels
	 * @return array channels list
	 */
	public function getChannels() {
		$action = '/channels';
		return $this->doRequest($action);
	}

	/**
	 * @todo I don't have a device that supports triggers, so I can't accurately develop this yet
	 * @return array
	 */
	public function getTrigger() {
		trigger_error("Method not yet implemented", E_USER_WARNING);
		$action = '/@/@/triggers/@';
		return $this->doRequest($action, array(), array($triggerID));
	}

	/**
	 * @todo I don't have a device that supports triggers, so I can't accurately develop this yet
	 * @return array
	 */
	public function setTrigger() {
		trigger_error("Method not yet implemented", E_USER_WARNING);
		$action = '/@/@/triggers/@';
		return $this->doRequest($action, $data, array($triggerID));
	}

	/**
	 * Perform a request to the Wink API.
	 * 
	 * @param string $action
	 * @param array $data
	 * @param array $args
	 * @param string $method
	 * @return array
	 */
	public function doRequest($action, $data = array(), $args = array(), $method = 'GET') {
		//$args is an array that is replaced into the URL over top of @ signs
		if(substr_count($action, '@') != count($args)) {
			trigger_error(sprintf('Number of args provided does not match anchors in method \'%s\' Expected %d but got %d.', $action, substr_count($action, '@'), count($args)), E_USER_ERROR);
		}

		foreach($args as $arg) {
			$action = substr_replace($action, $arg, strpos($action, '@'), 1);
		}

		if($this->debug) {
			echo "$method $action >>>>>> " . json_encode($data, JSON_PRETTY_PRINT) . "\n\n";
		}

		$json = json_encode($data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://' . self::API_HOST . $action);
		curl_setopt($ch, CURLOPT_PORT, 443);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if(!empty($data)) {
			$method = $method == 'GET' ? 'POST' : $method;
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
			$headers = array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($json)
			);
		}
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		if(!empty($this->access_token)) {
			$headers[] = 'Authorization: Bearer ' . $this->access_token;
		}
		if(!empty($headers)) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
		$resp = curl_exec($ch);
		$respArr = json_decode($resp, JSON_OBJECT_AS_ARRAY);
		if($this->debug) {
			// We want to see pretty json, so reencode it with JSON_PRETTY_PRINT
			print_r("$method $action <<<<<< " . json_encode(json_decode($resp), JSON_PRETTY_PRINT) . "\n\n");
		}
		return $respArr;
	}

}
