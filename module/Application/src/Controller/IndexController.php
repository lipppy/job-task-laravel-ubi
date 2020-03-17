<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Service\MapService;

class IndexController extends AbstractActionController
{

    private $adapter;

    public function __construct($adapter) 
    {
        $this->adapter = $adapter;
    }

    public function indexAction()
    {
        $ms = new MapService($this->adapter);
        $regions = $ms->getRegionsForMenu();

        return new ViewModel([
            "regions" => $regions,
        ]);
    }
}
