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
 * The Nimbus device parent class.  This class allows you access to device-level data on the Nimbus,
 * from device naming to sharing.  Actions specific to a dial are performed on individual nimbusDial
 * objects obtained by calling getDial()
 * 
 */
class nimbus extends \ianmaddox\wink\device {

	/**
	 * Return the ID of this wink object
	 * @return int
	 */
	public function getID() {
		return $this->deviceData['cloud_clock_id'];
	}

	/**
	 * Return all available data about the device
	 * @return array
	 */
	public function getClock() {
		$action = '/cloud_clocks/@';
		$args = array('cloud_clock_id' => $this->deviceID);
		return $this->wink->doRequest($action, array(), $args);
	}

	/**
	 * Change the device's friendly name
	 * 
	 * @param string $name
	 * @return object
	 */
	public function renameClock($name) {
		$action = '/cloud_clocks/@';
		$args = array('cloud_clock_id' => $this->deviceID);
		$data = array('name' => $name);
		return $this->wink->doRequest($action, $data, $args, 'PUT');
	}

	/**
	 * List the users who have access to this Nimbus
	 * @link http://docs.wink.apiary.io/#get-%2Fcloud_clocks%2F%7Bcloud_clock_id%7D%2Fusers
	 * @return object
	 */
	public function getClockUsers() {
		$action = '/cloud_clocks/@/users';
		$args = array('cloud_clock_id' => $this->deviceID);
		return $this->wink->doRequest($action, array(), $args);
	}

	public function shareClock() {
		trigger_error("Method not yet implemented", E_USER_WARNING);
		$action = '/cloud_clocks/@/users';
	}

	public function unshareClock() {
		trigger_error("Method not yet implemented", E_USER_WARNING);
		$action = '/cloud_clocks/@/users/@';
	}

	/**
	 * Grabs the list of available templates for the dials.
	 * @link http://docs.wink.apiary.io/#get-%2Fdial_templates
	 * @return object
	 */
	public function getDialTemplates() {
		$action = '/dial_templates';
		return $this->wink->doRequest($action);
	}

	/**
	 * Return the nimbusDial object for the given position, 0-3
	 * 
	 * @param int $position Dial position: 0, 1, 2, or 3
	 * @return ianmaddox\wink\devices\nimbusDial
	 */
	public function getDial($position) {
		// cast away all doubt.
		$position = (int) $position;
		if($position < 0 || $position > 3) {
			trigger_error("The nimbus only has dials 0, 1, 2, and 3.  Cannot supply data on dial '$position'", E_USER_WARNING);
			return false;
		}
		return $this->wink->makeDevice(\ianmaddox\wink\wink::DEVICE_NIMBUSDIAL, $this->deviceData['dials'][$position], $this);
	}

	public function getDialObject($index) {
		// Todo: Grab a nimbusDial object based solely on its index.
	}

	public function updateAlarm() {
		trigger_error("Method not yet implemented", E_USER_WARNING);
		$action = '/alarms/@';
	}

	/**
	 * @todo The API endpoint is not fully implemented
	 * Delete an alarm from the device
	 * 
	 * @param int $alarmID
	 * @return object
	 */
	public function deleteAlarm($alarmID) {
		trigger_error("Method not yet implemented", E_USER_WARNING);
		$action = '/alarms/@';
		$args = array('alarm_id' => $this->deviceID);
		return $this->wink->doRequest($action, array(), $args, 'DELETE');
	}

	/**
	 * @todo API endpoint is not complete
	 * 
	 * List all of the alarms configured for this device
	 * @link http://docs.wink.apiary.io/#get-%2Fcloud_clocks%2F%7Bcloud_clock_id%7D%2Falarms
	 * @return object
	 */
	public function getAlarms() {
		trigger_error("Method not yet implemented", E_USER_WARNING);
		$action = '/cloud_clocks/@/alarms';
		$args = array('cloud_clock_id' => $this->deviceID);
		return $this->wink->doRequest($action, array(), $args);
	}

	/**
	 * @todo API endpoint is not complete
	 * 
	 * Create a new alarm
	 * @link http://docs.wink.apiary.io/#post-%2Fcloud_clocks%2F%7Bcloud_clock_id%7D%2Falarms
	 * @return object
	 */
	public function createAlarm() {
		trigger_error("Method not yet implemented", E_USER_WARNING);
		$action = '/cloud_clocks/@/alarms';
	}

}
