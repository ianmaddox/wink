<?php namespace ianmaddox\wink\devices;

/**
 * The nimbusDial will be the primary object for interfacing with the individual gauges.
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
	
}