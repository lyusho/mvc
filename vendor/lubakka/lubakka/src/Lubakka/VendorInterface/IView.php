<?php
namespace Lubakka\VendorInterface;
/**
 *
 * @author lubakka
 */
interface IView {
    public static function render($bundle, $path, $name, $params, $response);
    
}
