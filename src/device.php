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

abstract class device {

	/** @var \ianmaddox\wink\wink $wink */
	public $wink;
	protected $deviceID;
	protected $deviceData;
	protected $parent;

	public function __construct(wink $wink, array $deviceData, device $parent = null) {
		$this->wink = $wink;
		$this->deviceData = $deviceData;
		$this->deviceID = $this->getID();
		$this->parent = $parent;
	}

	/**
	 * Return the ID of this wink object
	 * @return int
	 */
	abstract public function getID();
}
