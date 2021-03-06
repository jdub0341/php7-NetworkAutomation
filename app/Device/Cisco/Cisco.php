<?php

namespace App\Device\Cisco;

use DB;
use Metaclassing\SSH;

class Cisco extends \App\Device\Device
{
    protected static $singleTableSubclasses = [
        \App\Device\Cisco\IOS::class,
        \App\Device\Cisco\IOSXE::class,
        \App\Device\Cisco\IOSXR::class,
        \App\Device\Cisco\NXOS::class,
    ];
    protected static $singleTableType = __CLASS__;

    public $discover_commands = [
        'sh version',
        'sh version running',
    ];

    public $discover_regex = [
        'App\Device\Cisco\IOS'     => [
            '/cisco ios software/i',
        ],
        'App\Device\Cisco\IOSXE'   => [
            '/ios-xe/i',
            '/package:/i',
        ],
        'App\Device\Cisco\IOSXR'   => [
            '/ios xr/i',
            '/iosxr/i',
        ],
        'App\Device\Cisco\NXOS'    => [
            '/Cisco Nexus/i',
            '/nx-os/i',
        ],       
    ];
    //List of commands to run during a scan of this device.
    public $scan_cmds = [
        'run'           => 'sh run',
        'version'       => 'sh version',
        'interfaces'    => 'sh interfaces',
        'inventory'     => 'sh inventory',
        'dir'           => 'dir',
        'cdp'           => 'sh cdp neighbor detail',
        'lldp'          => 'sh lldp neighbor detail',
        'mac'           => 'sh mac address-table',
        'arp'           => 'sh ip arp',
    ];

    /*
    This method is used to establish a CLI session with a device.
    It will attempt to use Metaclassing\SSH library to work with specific models of devices that do not support ssh2.0 natively.
    Returns a Metaclassing\SSH object.
    */
    public function getCli()
    {
        $credentials = $this->getCredentials();
        foreach ($credentials as $credential) {
            // Attempt to connect using Metaclassing\SSH library.
            try {
                $cli = $this->getSSH1($this->ip, $credential->username, $credential->passkey);
            } catch (\Exception $e) {
                //If that fails, attempt to connect using phpseclib\Net\SSH2 library.
            }
            if ($cli) {
                $this->credential_id = $credential->id;
                //$this->save();

                return $cli;
            }
        }
    }

    /*
    Find the name of this device from DATA.
    Returns string (device name).
    */
    public function getName()
    {
        $reg = "/hostname (\S+)/";
        if (preg_match($reg, $this->data['run'], $hits)) {
            return $hits[1];
        }
    }

    /*
    Find the serial of this device from DATA.
    Returns string (device serial).
    */
    public function getSerial()
    {
        $reg = "/^Processor board ID (\S+)/m";
        if (preg_match($reg, $this->data['version'], $hits)) {
            return $hits[1];
        }
    }

    /*
    Find the model of this device from DATA.
    Returns string (device model).
    */
    public function getModel()
    {
        if (preg_match('/.*isco\s+(WS-\S+)\s.*/', $this->data['version'], $reg)) {
            return $reg[1];
        }
        if (preg_match('/.*isco\s+(OS-\S+)\s.*/', $this->data['version'], $reg)) {
            return $reg[1];
        }
        if (preg_match('/.*ardware:\s+(\S+),.*/', $this->data['version'], $reg)) {
            return $reg[1];
        }
        if (preg_match('/.*ardware:\s+(\S+).*/', $this->data['version'], $reg)) {
            return $reg[1];
        }
        if (preg_match('/^[c,C]isco\s(\S+)\s\(.*/m', $this->data['version'], $reg)) {
            return $reg[1];
        }
    }
}
