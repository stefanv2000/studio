<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Entities\Entity\Section;
use Entities\Mapper\UserMapper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {

        /** @var EntityManager $em */
        $em = $this->getServiceLocator()->get("Doctrine\ORM\EntityManager");
        $arraysec = $em->getRepository("Entities\Entity\Section")->findBy(array("parent" => null),array("id" => "asc"));
        foreach ($arraysec as $section) {
            $this->printSection($section,0);
        }
        return new ViewModel();
    }

    /**
     * @param $section Section
     * @param $level
     */
    private function printSection($section,$level) {
        $str="-----";
        for ($i=0;$i<$level;$i++){
            echo $str;
        }
        echo $section->getName()."\n";
        /** @var ArrayCollection $subsections */
        $subsections = $section->getSubsections()->toArray();
        foreach ($subsections as $subsection){
            $this->printSection($subsection,$level+1);
        }
    }

}
