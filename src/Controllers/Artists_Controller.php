<?php
/**
 * Created by PhpStorm.
 * User: Lboikov
 * Date: 14-10-1
 * Time: 0:17
 */

namespace src\Controllers;


use Vendor\Controllers\Master_Controller;

class Artists_Controller extends Master_Controller {
    
    public function indexAction($id, $t){
        echo 'Artists in the House In INDEX : ' . $id;
    }
    
    public function viewAction() {
        echo 'Artists in the House In VIEW';
    }
} 