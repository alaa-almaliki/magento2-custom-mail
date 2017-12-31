<?php

namespace Alaa\CustomMail\Model;
use Magento\Framework\Mail\Template\TransportBuilder;

/**
 * Class SendMail
 * @package Alaa\CustomMail\Model
 * @author Alaa Al-Maliki <alaa.almaliki@gmail.com>
 */
class SendMail implements SendMailInterface
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    public function __construct(TransportBuilder $transportBuilder)
    {
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * @param array $config
     * @return \Alaa\CustomMail\Model\SendMailInterface
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return \Alaa\CustomMail\Model\SendMailInterface
     */
    public function send()
    {
        $this->prepareMail();
        $this->transportBuilder->getTransport()->sendMessage();
        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function prepareMail()
    {
        foreach ($this->config as $methodName => $value) {
            $method = $this->getMethod($methodName);
            if (!method_exists($this->transportBuilder, $method) || $method === null) {
                $message = sprintf(
                    'Undefined method %s of class %s',
                    $method,
                    get_class($this->transportBuilder)
                );
                throw new \Exception(__($message));
            }
            call_user_func_array(
                [$this->transportBuilder, $method],
                $value
            );
        }
        return $this;
    }

    /**
     * @param  string $name
     * @return string
     */
    public function getMethod($name)
    {
        $toCamelCase = $this->camelCase($name);
        return $this->resolveMethod($toCamelCase);
    }

    /**
     * @param string $camelCaseName
     * @return null|string
     */
    protected function resolveMethod($camelCaseName)
    {
        foreach (['set', 'add'] as $prefix) {
            $searchMethod = $prefix . $camelCaseName;
            if (array_key_exists($searchMethod, array_flip($this->getMethods()))) {
                return $searchMethod;
            }
        }

        return null;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function camelCase($name)
    {
        $toCamelCase = '';
        if (strpos($name, '_') !== false) {
            $parts = explode('_', $name);
            foreach ($parts as &$part) {
                $part = ucfirst($part);
                $toCamelCase = implode('', $parts);
            }
        } else {
            $toCamelCase = ucfirst($name);
        }

        return $toCamelCase;
    }

    /**
     * @return array
     */
    protected function getMethods()
    {
        static $methods = [];
        if (empty($methods)) {
            $methods = get_class_methods($this->transportBuilder);
        }
        return $methods;
    }
}
