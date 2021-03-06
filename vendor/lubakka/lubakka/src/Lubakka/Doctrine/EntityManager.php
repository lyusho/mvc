<?php
/**
 * Created by PhpStorm.
 * User: lboykov
 * Date: 15-1-27
 * Time: 19:03
 */

namespace Lubakka\Doctrine;

use Doctrine\ORM\EntityManager as Entity;
use Doctrine\ORM\Tools\Setup;
use Lubakka\Service\Container;


/**
 * Class EntityManager
 * @package Lubakka\Doctrine
 */
class EntityManager
{

    /**
     * @var null
     */
    static $instance = null;

    /**
     * @var bool
     */
    private $isDevMode;

    /**
     * @var array
     */
    private $config = array();

    /**
     * @var array
     */
    private $conn = array();

    /**
     * @var string
     */
    private $driver = 'pdo_mysql';

    /**
     * @var
     */
    private $em;

    /**
     *
     */
    public function __construct()
    {
        $this->isDevMode = true;
        $this->setConn();
        $this->init();
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function init()
    {
        $vendorDir = Container::getContainer()->getPathRoot();

        $config = Setup::createXMLMetadataConfiguration(array(realpath($vendorDir . 'doctrine')), self::getIsDevMode());

        $this->em = Entity::create($this->getConn(), $config);
    }

    /**
     * @return boolean
     */
    public function getIsDevMode()
    {
        return $this->isDevMode;
    }

    /**
     * @param boolean $isDevMode
     *
     * @return $this
     */
    public function setIsDevMode($isDevMode)
    {
        $this->isDevMode = $isDevMode;

        return $this;
    }

    /**
     * @return array
     */
    public function getConn()
    {
        return $this->conn;
    }

    /**
     * @param array $conn
     *
     * @return $this
     */
    public function setConn(array $conn = array())
    {
        $default = array(
            'driver' => $this->getDriver(),
            'user' => 'root',
            'password' => '',
            'dbname' => 'test',
            'host' => 'localhost'
        );
        $conn = Container::getContainer()->getParameters();
        $result = array_merge($default, $conn);

        $this->conn = $result;

        return $this;
    }

    /**
     * @return static
     */
    public static function getEntityManager()
    {
        if (null == self::$instance) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     *
     * @return $this
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param string $driver
     *
     * @return $this
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEm()
    {
        return $this->em;
    }

}