<?php

namespace Alaa\CustomMail\Model;

/**
 * Interface SendMailInterface
 * @package Alaa\CustomMail\Model
 * @author Alaa Al-Maliki <alaa.almaliki@gmail.com>
 */
interface SendMailInterface
{
    /**
     * @param array $config
     * @return \Alaa\CustomMail\Model\SendMailInterface
     */
    public function setConfig(array $config);

    /**
     * @return \Alaa\CustomMail\Model\SendMailInterface
     */
    public function send();
}
