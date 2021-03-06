<?php
/**
 * Created by PhpStorm.
 * User: lboykov
 * Date: 15-1-28
 * Time: 12:47
 */

namespace Lubakka\Service;

/**
 * Class Container
 * @package Lubakka\Service
 */
class Container implements \ArrayAccess
{

    /**
     * @var array
     */
    protected $services = array();
    /**
     * @var string
     */
    private $path = ROOT_DIR;
    /**
     * @var string
     */
    private $pathService = '../conf/service/';
    /**
     * @var string
     */
    private $pathRoot = '../conf/';
    /**
     * @var string
     */
    private $fileService = 'service.xml';
    /**
     * @var \SimpleXMLElement
     */
    private $xmlService;
    /**
     * @var string
     */
    private $pathParameters = '../conf/service/';
    /**
     * @var string
     */
    private $fileParameters = 'parameters.xml';
    /**
     * @var \SimpleXMLElement
     */
    private $xmlParameter;
    /**
     * @var
     */
    private $parameters;
    /**
     * @var string
     */
    private $namespace = 'Lubakka';

    /**
     * @var array
     */
    private $container = array();

    /**
     *
     */
    private function __construct()
    {
        $xmlFileService = file_get_contents(realpath($this->path . $this->pathService . $this->fileService));
        $xmlFileParameters = file_get_contents(realpath($this->path . $this->pathParameters . $this->fileParameters));

        $this->xmlService = new \SimpleXMLElement($xmlFileService);
        $this->xmlParameter = new \SimpleXMLElement($xmlFileParameters);

        $this->setParameters($this->xmlParameter);
        $this->setServices($this->xmlService);

    }

    /**
     * @param \SimpleXMLElement $xml
     */
    private function setParameters(\SimpleXMLElement $xml)
    {
        $param = array();
        if ($xml->xpath('/parameters/sql[@name="mysql"]')) {
            $param['driver'] = (string)$this->query_attribute($xml->sql, "name", "mysql")->attributes()->driver;
            $param['user'] = (string)$this->query_attribute($xml->sql, "name", "mysql")->user;
            $param['host'] = (string)$this->query_attribute($xml->sql, "name", "mysql")->host;
            $param['dbname'] = (string)$this->query_attribute($xml->sql, "name", "mysql")->dbname;
            $param['password'] = (string)$this->query_attribute($xml->sql, "name", "mysql")->password;

        } elseif ($xml->xpath('/parameters/sql[@name="mssql"]')) {
            $param['driver'] = (string)$this->query_attribute($xml->sql, "name", "mssql")->attributes()->driver;
            $param['user'] = (string)$this->query_attribute($xml->sql, "name", "mssql")->user;
            $param['host'] = (string)$this->query_attribute($xml->sql, "name", "mssql")->host;
            $param['dbname'] = (string)$this->query_attribute($xml->sql, "name", "mssql")->dbname;
            $param['password'] = (string)$this->query_attribute($xml->sql, "name", "mssql")->password;
        }

        $this->parameters = $param;
    }

    /**
     * @param $xmlNode
     * @param $attr_name
     * @param $attr_value
     *
     * @return mixed
     */
    private function query_attribute($xmlNode, $attr_name, $attr_value)
    {
        foreach ($xmlNode as $node) {
            switch ($node[$attr_name]) {
                case $attr_value:
                    return $node;
            }
        }
    }

    /**
     * @param \SimpleXMLElement $xml
     */
    private function setServices(\SimpleXMLElement $xml)
    {
        $service = array();
        if ($xml->xpath('/service-container/services/service[@id="service"]')) {

            $class_name = $this->namespace .
                '\\' .
                ucfirst((string)$xml->services->service->set->attributes()->id) .
                '\\' .
                ucfirst($this->camelCase((string)$xml->services->service->set->attributes()->name));

            $service [(string)$xml->services->service->set->attributes()->id] = $class_name;
        }
        $this->services = $service;
    }

    /**
     * @param       $str
     * @param array $noStrip
     *
     * @return mixed|string
     */
    public static function camelCase($str, array $noStrip = [])
    {
        // non-alpha and non-numeric characters become spaces
        $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
        $str = trim($str);
        // uppercase the first character of each word
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);
        $str = lcfirst($str);

        return $str;
    }

    /**
     * @return static
     */
    public static function getContainer()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getServices($id)
    {
        return new $this->services[$id];
    }

    /**
     * @return mixed
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getFileService()
    {
        return $this->fileService;
    }

    /**
     * @param string $fileService
     *
     * @return $this
     */
    public function setFileService($fileService)
    {
        $this->fileService = $fileService;

        return $this;
    }

    /**
     * @return string
     */
    public function getPathParameters()
    {
        return $this->pathParameters;
    }

    /**
     * @param string $pathParameters
     *
     * @return $this
     */
    public function setPathParameters($pathParameters)
    {
        $this->pathParameters = $pathParameters;

        return $this;
    }

    /**
     * @return string
     */
    public function getPathService()
    {
        return $this->pathService;
    }

    /**
     * @param string $pathService
     *
     * @return $this
     */
    public function setPathService($pathService)
    {
        $this->pathService = $pathService;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileParameters()
    {
        return $this->fileParameters;
    }

    /**
     * @param string $fileParameters
     *
     * @return $this
     */
    public function setFileParameters($fileParameters)
    {
        $this->fileParameters = $fileParameters;

        return $this;
    }

    /**
     * Namespace for where is call service
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Namespace for where is call service
     *
     * @param string $namespace
     *
     * @return $this
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPathRoot()
    {
        return $this->pathRoot;
    }

    /**
     * @param string $pathRoot
     */
    public function setPathRoot($pathRoot)
    {
        $this->pathRoot = $pathRoot;
    }

    /**
     * @return \SimpleXMLElement
     */
    public function getXmlService()
    {
        return $this->xmlService;
    }

    /**
     * @param \SimpleXMLElement $xmlService
     */
    public function setXmlService($xmlService)
    {
        $this->xmlService = $xmlService;
    }

    /**
     * @return \SimpleXMLElement
     */
    public function getXmlParameter()
    {
        return $this->xmlParameter;
    }

    /**
     * @param \SimpleXMLElement $xmlParameter
     */
    public function setXmlParameter($xmlParameter)
    {
        $this->xmlParameter = $xmlParameter;
    }

    /**
     * @param array $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }


}