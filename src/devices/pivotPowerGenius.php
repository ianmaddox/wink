<?php

/**
 * Wink SDK for PHP.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * See the file license.txt for copying permission.
 *
 * @author    Ian Maddox <oss@ianmaddox.com>
 * @copyright 2014
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 * History :
 * July 5, 2017 - Jojo Kahanding <jojokahanding@gmail.com>
 *      Implemented 
 */

namespace ianmaddox\wink\devices;

class pivotPowerGenius extends \ianmaddox\wink\device
{
    public function getID()
    {
        return $this->deviceData['powerstrip_id'];
    }

    // turn on or off the power strip's named outlet
    // the pivotPowerGenius has only 2 outlets
    public function setState($name, $on, &$skipped)
    {
        $namesAndStates = $this->outletNamesAndStates();
        $outlets = array();
        $found = false;
        $skipped = false;
        foreach ($namesAndStates as $nameKey => $value) {
            if ($nameKey == $name) {
                if ($value == $on) {
                    $skipped = true;
                    break;
                }
                $value = $on;
                $found = true;
            }
            $outlets[] = array('desired_state' => array('powered' => $value));
        }
        if (!$found) {
            return false;
        }
        $action = '/powerstrips/@';
        $args = array('powerstrip_id' => $this->deviceID);
        $data = array('outlets' => $outlets);

        return $this->wink->doRequest($action, $data, $args, 'PUT');
    }

    // returns an array of 'on' states whose index is the outlet name.
    private function outletNamesAndStates()
    {
        return array(
            $this->deviceData['outlets'][0]['name'] =>
                $this->deviceData['outlets'][0]['powered'] == 1,
            $this->deviceData['outlets'][1]['name'] =>
                $this->deviceData['outlets'][1]['powered'] == 1,
        );
    }
}
