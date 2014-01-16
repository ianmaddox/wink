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
 * The nimbusDial is the primary object for interfacing with the individual gauges.
 */
class nimbusDial extends \ianmaddox\wink\device {

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

	private $autoSave = true;

	/**
	 * Get the ID of this dial
	 * @return int
	 */
	public function getID() {
		return $this->deviceData['dial_id'];
	}

	/**
	 * Control whether saving is automatic after making a set*() method call
	 * @param bool $bool
	 */
	public function setAutoSave($bool) {
		$this->autoSave = $bool;
	}

	/**
	 * Get the raw device data. Very useful for exploring the values you can configure.
	 * 
	 * Note that dial_id, dial_index, and position are immutable. labels is also supposed to
	 * be immutable according to the API, but it is currently the only way to set the alt text
	 * when in manual mode.
	 * 
	 * @link http://docs.wink.apiary.io/#put-%2Fdials%2F%7Bdial_id%7D
	 * @return array
	 */
	public function getData() {
		return $this->deviceData;
	}

	public function setBrightness($percent) {
		if($percent > 100 || $percent < 0) {
			trigger_error('Cannot set brightness greater than 100% or less than 0%', E_USER_NOTICE);
		}
		$percent = $percent < 0 ? 0 : $percent;
		$percent = $percent > 100 ? 100 : $percent;
		$this->setData(array('brightness' => $percent));
	}

	/**
	 * Sets the needle to any angle.  Will switch the mode to manual.
	 * 
	 * @param int $deg
	 * @return type
	 */
	public function setDegrees($deg) {
		// Normalize the input.
		$deg = $deg < 0 ? 360 + $deg % 360 : $deg % 360;
		$deg = $deg == 0 ? 360 : (int) $deg;
		return $this->setData(array(
					'value' => $deg,
					'channel_configuration' =>
					array('channel_id' => self::TEMPLATE_MANUAL)
						)
		);
	}

	/**
	 * Set the main display and alternate display text for the dial
	 * 
	 * @param string $main
	 * @param string $alt
	 * @return bool
	 */
	public function setText($main, $alt = '') {
		// Don't override the secondary label if not specified
		// At the time this was written, labels[] was described as immutible in the docs
		// but it is the only way to set the secondary text for a dial.
		if(empty($alt)) {
			return $this->setData(array('labels' => array($main)));
		}
		return $this->setData(array('labels' => array($main, $alt)));
	}

	/**
	 * Set the dial configuration template. Not terribly useful on its own, because you will
	 * generally need contextual data specific to the channel for most of them. The most obvious
	 * exception is TEMPLATE_MANUAL which allows for free-range setting of the degrees.
	 * 
	 * @param const $templateID see self::TEMPLATE_* constants
	 * @return bool
	 */
	public function setTemplate($templateID) {
		return $this->setData(
						array('channel_configuration' =>
							array('channel_id' => $templateID)
						)
		);
	}

	/**
	 * 
	 * @param type $config
	 * @return type
	 */
	public function setData($config) {

		/**
		 * Merge two arrays recursively with the source taking precedence for any overlapping key name.
		 * 
		 * @param array source array
		 * @param array destination array
		 * @return array destination array with source recursively overlaid on top
		 */
		$arrMerge = function($src, $dest) use(&$arrMerge) {
			foreach($src as $key => $val) {
				if(is_array($val) && isset($dest[$key])) {
					$dest[$key] = $arrMerge($val, $dest[$key]);
				} else {
					// If there is already an array in the dest that conflicts with an incoming key, clobber it.
					if(!is_array($dest)) {
						$dest = array();
					}
					$dest[$key] = $val;
				}
			}
			return $dest;
		};
		$this->deviceData = $arrMerge($config, $this->getData());
		if($this->autoSave) {
			return $this->save();
		}
		return true;
	}

	/**
	 * Push the latest data to the Wink API
	 * @return boolean success
	 */
	public function save() {
		$action = '/dials/@';
		$args = array('dial_id' => $this->getID());

		$config = $this->getData();
		unset($config['refreshed_at']);
		$resp = $this->wink->doRequest($action, $config, $args, 'PUT');
		if(empty($resp) || !empty($resp['errors'])) {
			$this->data = array();
			return false;
		}
		$this->data = $resp['data'];
		return true;
	}

}
