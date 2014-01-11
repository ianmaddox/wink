<?php namespace ianmaddox\wink;

abstract class device {
	public $wink;
	protected $deviceID;
	
	public function __construct(wink $wink, $deviceID) {
		$this->wink = $wink;
		$this->deviceID = $deviceID;
	}
}
