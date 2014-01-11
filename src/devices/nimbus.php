<?php namespace ianmaddox\wink\devices;

class nimbus extends \ianmaddox\wink\device {
	
	const TEMPLATE_TIME = 1;
	const TEMPLATE_WEATHER = 2;
	const TEMPLATE_TRAFFIC = 3;
	const TEMPLATE_CALENDAR = 4;
	const TEMPLATE_EMAIL = 5;
	const TEMPLATE_FACEBOOK = 6;
	const TEMPLATE_INSTAGRAM = 7;
	const TEMPLATE_TWITTER = 8;
	const TEMPLATE_FITBIT = 9;
	const TEMPLATE_MANUAL = 10;
	const TEMPLATE_EGGMINDER = 11;
	const TEMPLATE_PORKFOLIO = 12;
	const TEMPLATE_NIKEPLUS = 17;
	
	const SCALE_LINEAR = 'linear';
	const SCALE_LOG = 'log';
	const ROTATION_CW = 'cw';
	const ROTATION_CCW = 'ccw';
	
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
	
	public function updateDial($config) {
		$action = '/dials/@';
		$args = array('dial_id' => $dialID);		
		$data = array(
			'dial_id' => $dialID,
			'dial_index' => $dialIndex,
			'name' => $name,
			'labels' => array($label, 'foo'),
			'value' => $position,
			'brightness' => $brightness,
			'channel_configuration' => array(
				'channel_id' => 10
			)
		);
		
		return $this->wink->doRequest($action, $data, $args, 'PUT');
	}
	
	public function getDial($dialID) {
		$action = '/dials/@';
		$args = array('dial_id' => $dialID);		
//		$data = array(
//			'dial_id' => $dialID,
//			'dial_index' => $dialIndex,
//			'name' => $name,
//			'labels' => array($label, 'foo'),
//			'value' => $position,
//			'brightness' => $brightness,
//			'channel_configuration' => array(
//				'channel_id' => 10
//			)
//		);
		
		return $this->wink->doRequest($action, array(), $args);
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