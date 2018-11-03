<?php

namespace zhongwu;

use zhongwu\protocol\RpcClient;

class Printer
{
    private $device_id, $device_secret, $client;

    /**
     * printer constructor.
     * @param RpcClient $rpc
     */
    public function __construct($rpc)
    {
        $this->client = $rpc;
    }

    public function set_args($device_id, $device_secret)
    {
        $this->device_id = $device_id;

        $this->device_secret = $device_secret;

        return $this;
    }

    private function get_device()
    {
        return array('deviceid' => $this->device_id, 'devicesecret' => $this->device_secret);
    }

    /**
     * @desc
     * @param $printdata
     * @return bool|mixed
     * @throws \Exception
     * @throws exceptions\BusinessException
     */
    public function cloud_print($printdata)
    {
        if (strlen($printdata) > 6000) {

            return false;

        }
        return $this->client->call('', array_merge(array('printdata' => $printdata), $this->get_device()));
    }

    /**
     * @desc
     * @return mixed
     * @throws \Exception
     */
    public function get_status()
    {
        return $this->client->call('status', $this->get_device(), 'get');
    }

    /**
     * @desc
     * @param $image
     * @return mixed
     * @throws \Exception
     */
    public function set_logo($image)
    {
        $logodata = '';

        if (!empty($image)) {

            $imagedata = file_get_contents($image);

            if (strlen($imagedata) > 40 * 1024) {

                return false;

            }

            $logodata = base64_encode($imagedata);
        }


        return $this->client->call('logo', array_merge(array('logodata' => $logodata), $this->get_device()));
    }

    /**
     * @desc
     * @param $sound
     * @return mixed
     * @throws \Exception
     * @throws exceptions\BusinessException
     */
    public function set_sound($sound)
    {
        return $this->client->call('sound', array_merge(array('sound' => $sound), $this->get_device()));
    }

    /**
     * @desc
     * @param int $id
     * @return mixed
     * @throws \Exception
     * @throws exceptions\BusinessException
     */
    public function get_print_status($id)
    {
        return $this->client->call('printstatus', array_merge(array('dataid' => $id), $this->get_device()), 'get');
    }

    /**
     * @desc
     * @return mixed
     * @throws \Exception
     * @throws exceptions\BusinessException
     */
    public function empty_print_queue()
    {
        return $this->client->call('emptyprintqueue', $this->get_device());
    }
}