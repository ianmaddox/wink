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

namespace ianmaddox\wink\devices;

/**
 * The GE Link bulb device parent class. This class allows you access to device-level data on the GE Link bulb,
 * from device naming, to sharing, and state changes.
 */
class geLinkLightbulb extends \ianmaddox\wink\device {

	/**
	 * Return the ID of this wink object
	 * @return int
	 */
	public function getID() {
		return $this->deviceData['light_bulb_id'];
	}

	/**
	 * Set the current state of the device. Specifically
	 * for this device is the powered state (boolean)
	 * and the brightness state (range of float 0.0-1.0)
	 * @return array;
	 */
	public function setState($powered, $brightness) {
		$action = '/light_bulbs/@';
		$args = array('light_bulb_id' => $this->deviceID);

		if ($powered !== true && $powered !== false) {
			trigger_error("The GE Link Bulb only has 'true' or 'false' as an accepted powered state. Cannot change desired powered state on bulb to '$powered'", E_USER_WARNING);
			return false;
		}

		if ($brightness < 1.0 || $powered > 1.0) {
			trigger_error("The GE Link Bulb only has the range 0.0 through 1.0 as an accepted brightness levels. Cannot change desired brightness level on bulb to '$brightness'", E_USER_WARNING);
			return false;
		}

		$data = array(
			'desired_state' => array(
				"powered" => $powered,
				"brightness" => $brightness
			)
		);

		return $this->wink->doRequest($action, $data, $args, 'PUT');
	}

	/**
	 * Return all available data about the device
	 * @return array
	 */
	public function getLightbulb() {
		$action = '/light_bulbs/@';
		$args = array('light_bulb_id' => $this->deviceID);
		return $this->wink->doRequest($action, array(), $args);
	}

	/**
	 * Change the device's friendly name
	 * 
	 * @param string $name
	 * @return object
	 */
	public function renameDevice($name) {
		$action = '/light_bulbs/@';
		$args = array('light_bulb_id' => $this->deviceID);
		$data = array('name' => $name);
		return $this->wink->doRequest($action, $data, $args, 'PUT');
	}

	/**
	 * List the users who have access to this GE Link Bulb
	 * @link http://docs.wink.apiary.io/#get-%2Flight_bulbs%2F%7Blight_bulb_id%7D%2Fusers
	 * @return object
	 */
	public function getLightbulbUsers() {
		$action = '/light_bulbs/@/users';
		$args = array('light_bulb_id' => $this->deviceID);
		return $this->wink->doRequest($action, array(), $args);
	}

	public function unshareLightbulb() {
		trigger_error("Method not yet implemented", E_USER_WARNING);
		$action = '/light_bulbs/@/users';
	}

	public function shareLightbulb() {
		trigger_error("Method not yet implemented", E_USER_WARNING);
		$action = '/light_bulbs/@/users/@';
	}

}
