<?php
/**
 * Created by PhpStorm.
 * User: Lboikov
 * Date: 14-9-30
 * Time: 23:09
 */

namespace Lubakka\Controllers;

use Lubakka\Exception\MasterControllerException;
use Lubakka\ParameterBag;
use Lubakka\Service\Container;
use Lubakka\Session;
use Lubakka\VendorInterface\Controllers\IController;
use Lubakka\View\View;

/**
 * Class MasterController
 * @package Lubakka\Controllers
 */
abstract class MasterController implements IController
{

    /**
     * @var Container
     */
    protected $container;
    /**
     * @var
     */
    protected $get;
    /**
     * @var
     */
    protected $view;

    /**
     *
     */
    function __construct()
    {
        $this->container = Container::getContainer();
    }

    /**
     * @return View
     */
    public function getView()
    {
        return $this->view;
    }


    /**
     * @return $this
     */
    public function index()
    {
        echo "Default";
        return $this;
    }

    /**
     * @param       $view
     * @param array $params
     * @param int   $response
     *
     * @throws \Exception
     */
    public function render($view, array $params = array(), $response = 200)
    {
        if ('@' !== $view[0]) {
            throw new \Exception(sprintf('A resource name must start with @ ("%s" given).', $view));
        }

        $views = new View();

        $bundle = substr($view, 1);

        $path = '';
        if (false !== strpos($bundle, '/')) {
            list($bundle, $name) = explode('/', $bundle, 2);
        }
        if (false !== strpos($bundle, ':')) {
            list($bundle, $path) = explode(':', $bundle);
        }

        return $views->render($bundle, $path, $name, $params, $response);
    }

    /**
     * @param       $view
     * @param array $params
     * @param int   $response
     *
     * @throws MasterControllerException
     * @throws \Exception
     * @throws \Lubakka\Exception\ViewException
     */
    public function layout($view, array $params = array(), $response = 200)
    {
        if ('@' !== $view[0]) {
            throw new \Exception(sprintf('A resource name must start with @ ("%s" given).', $view));
        }

        $bundle = substr($view, 1);

        $path = '';
        if (false !== strpos($bundle, '/')) {
            list($bundle, $name) = explode('/', $bundle, 2);
        }
        if (false !== strpos($bundle, ':')) {
            list($bundle, $path) = explode(':', $bundle);
        }

        try {
            return View::layout($bundle, $path, $name, $params, $response);
        } catch (MasterControllerException $e) {
            throw new MasterControllerException("Problem");
        }
    }

    /**
     * @return mixed
     */
    public function getSession()
    {
        return Session::getInstance()->getSession();
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return null === '' ? '' : 'Anonymous';
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function get($id)
    {
        return $this->getContainer($id);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getContainer($id)
    {
        return $this->container->getServices($id);
    }

    /**
     * @return string
     */
    function __toString()
    {
        $class = get_called_class();
        return $class;
    }

    /**
     *
     */
    public function redirect()
    {
        // TODO: Implement redirect() method.
    }
}