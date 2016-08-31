<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Frontend\Controller;

use Application\Infrastructure\Slug;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Entities\Entity\Section;
use Entities\Mapper\SectionMapper;
use Entities\Mapper\UserMapper;
use Entities\Util\Serializer;
use Frontend\Forms\BecomeModelForm;
use Frontend\Infrastructure\Cache\CacheJson;
use Frontend\Infrastructure\Domain\FoodDrink;
use Frontend\Infrastructure\Domain\Intro;
use Frontend\Infrastructure\Domain\Models;
use Frontend\Infrastructure\Domain\Pages;
use Frontend\Infrastructure\Domain\Professionals;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{

    /**
     * @var SectionMapper
     */
    private $sectionMapper;
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var CacheJson
     */
    private $cacheJson;

    /**
     * IndexController constructor.
     * @param SectionMapper $sectionMapper
     * @param Serializer $serializer
     * @param CacheJson $cacheJson
     */
    public function __construct(SectionMapper $sectionMapper, Serializer $serializer, CacheJson $cacheJson) {
        $this->sectionMapper = $sectionMapper;
        $this->serializer = $serializer;
        $this->cacheJson = $cacheJson;
    }

    public function generatePdfAction(){

        $section = $this->sectionMapper->findSectionById($this->params()->fromRoute('id'));
        if ($section == null) exit;
        $image = $section->getFirstImage();
        if ($image == null) exit;

        $path = "http://" . $section->getBucket() . ".s3.amazonaws.com/" . urlencode($section->getPath()) . '/_media/' . urlencode($image->getName());

        $imagecontents = file_get_contents($path);
        file_put_contents($image->getOriginalName(),$imagecontents);


        $html = "";
        $html.='<div><img src="/'.$image->getOriginalName().'" style="max-width:100%;padding-bottom:20px;"/></div>';



        $mpdf=new \mPDF();
        $mpdf->SetTitle('Card');
        $mpdf->WriteHTML($html);
        $mpdf->Output('card.pdf','I');
        unlink($image->getOriginalName());
        exit;
    }


    public function homeAction(){

        $arrayCacheRequest=array('view','intro');
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new ViewModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));
        $intro = new Intro($this->sectionMapper,$this->serializer);

        $arrayRes = array('content' => $intro->getContentArray());
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new ViewModel($arrayRes);

    }

    public function hometypeAction(){
        $intro = new Intro($this->sectionMapper,$this->serializer);

        $arrayCacheRequest=array('view','introtype',$this->params()->fromRoute('link'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new ViewModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));

        $arrayRes = $intro->getContentIntroTypeArray($this->params()->fromRoute('link'));
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new ViewModel($arrayRes);
    }

    public function profileintroAction(){
        $arrayCacheRequest=array('view','profileintro',$this->params()->fromRoute('link'),$this->params()->fromRoute('type'),$this->params()->fromRoute('name'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new ViewModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));

        $professionals = new Professionals($this->sectionMapper,$this->serializer);
        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlugWithParents(Slug::slugify($this->params()->fromRoute('name')),array($this->params()->fromRoute('type'),$this->params()->fromRoute('link')));
        $sectiontype = $section->getParent()->getName();

        $path = '/'.$this->params()->fromRoute('link').'/'.$this->params()->fromRoute('type').'/'.$this->params()->fromRoute('name');

        $section = $this->sectionMapper->findSectionBySlugWithParents($this->params()->fromRoute('name'),array($this->params()->fromRoute('type')));

        $result = array('path' => $path,
            'content' => $professionals->getProfileArray($this->params()->fromRoute('name'),$this->params()->fromRoute('type'),$this->params()->fromRoute('link')),
            'type' => $sectiontype,
            'title' => $section->getName(),

        );

        $firstpageArray = $professionals->getFirstPageProfileAllArray($this->params()->fromRoute('name'),$this->params()->fromRoute('type'),$this->params()->fromRoute('link'));



        $arrayRes = array_merge($result,array('firstpage' => $firstpageArray));
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new ViewModel($arrayRes);
    }

    public function profileAction(){
        $arrayCacheRequest=array('view','profile',$this->params()->fromRoute('link'),$this->params()->fromRoute('type'),$this->params()->fromRoute('name'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new ViewModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));

        $professionals = new Professionals($this->sectionMapper,$this->serializer);

        $section = $this->sectionMapper->findSectionBySlugWithParents($this->params()->fromRoute('name'),array($this->params()->fromRoute('type')));


        $arrayRes = array_merge(
            array('title' => $section->getName()),
            $professionals->getProfilePageArray($this->params()->fromRoute('name'),$this->params()->fromRoute('type'),$this->params()->fromRoute('link')));
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new ViewModel($arrayRes);
    }

    public function portfolioAction(){

        $arrayCacheRequest=array('view','portfoliocontent',$this->params()->fromRoute('link'),$this->params()->fromRoute('type'),$this->params()->fromRoute('name'),$this->params()->fromRoute('portfolio'),$this->params()->fromRoute('gallery'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new ViewModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));

        $professionals = new Professionals($this->sectionMapper,$this->serializer);
        $title = ' - ' .strtoupper($this->params()->fromRoute('portfolio'));

        $gallery = $this->params()->fromRoute('gallery');


        if ($gallery!='') {
            $title.= ' - '.strtoupper($gallery);
            $gallery = '/'.$gallery;

        }
        $path = '/'.$this->params()->fromRoute('link').'/'.$this->params()->fromRoute('type').'/'.$this->params()->fromRoute('name').'/'.$this->params()->fromRoute('portfolio').$gallery;

        $section = $this->sectionMapper->findSectionBySlugWithParents($this->params()->fromRoute('name'),array($this->params()->fromRoute('type')));






        $arrayRes = array(
            'title' => $section->getName().$title,
            'type' => $section->getName(),
            'content' => $professionals->getContentPortfolioGallery($this->params()->fromRoute('name'),$this->params()->fromRoute('type'),$this->params()->fromRoute('link'),$this->params()->fromRoute('portfolio'),$this->params()->fromRoute('gallery')),
            'path' => $path
        );
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new ViewModel($arrayRes);

    }

    public function modelsmainAction(){

        $arrayCacheRequest=array('view','modelsmain');
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new ViewModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));

        $models = new Models($this->sectionMapper,$this->serializer);

        $arrayRes = array(
            'content' => $models->getCategoriesArray(),
            'path' => '/models'
        );
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new ViewModel($arrayRes);
    }

    public function modelcategoryAction(){
        $arrayCacheRequest=array('view','modelscategory',$this->params()->fromRoute('category'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new ViewModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));
        $models = new Models($this->sectionMapper,$this->serializer);


        $arrayRes = array(
            'title' => 'MODELS - '.strtoupper($this->params()->fromRoute('category')),
            'content' => $models->getCategoryContentArray($this->params()->fromRoute('category')),
            'path' => '/models/'.$this->params()->fromRoute('category')
        );
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new ViewModel($arrayRes);
    }

    public function modelPageAction(){

        $arrayCacheRequest=array('view','modelpage',$this->params()->fromRoute('category'),$this->params()->fromRoute('name'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new ViewModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));

        $models = new Models($this->sectionMapper,$this->serializer);


        $links = $models->getModelLinksArray($this->params()->fromRoute('name'),$this->params()->fromRoute('category'));
        $section = $this->sectionMapper->findSectionBySlugWithParents($this->params()->fromRoute('name'),array($this->params()->fromRoute('category')));


        $arrayRes = array(
            'title' => $section->getName().' - MODEL - '.$section->getParent()->getName(),
            'content' => $models->getGalleriesForModelArray($this->params()->fromRoute('name'),$this->params()->fromRoute('category')),
            'path' => '/models/'.$this->params()->fromRoute('category').'/'.$this->params()->fromRoute('name'),
            'links' => $links
        );
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new ViewModel($arrayRes);
    }

    public function portfolioModelAction(){


        $arrayCacheRequest=array('view','modelportfolio',$this->params()->fromRoute('category'),$this->params()->fromRoute('name'),$this->params()->fromRoute('gallery'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new ViewModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));


        $models = new Models($this->sectionMapper,$this->serializer);

        $path = '/models/'.$this->params()->fromRoute('category').'/'.$this->params()->fromRoute('name').'/'.$this->params()->fromRoute('gallery');

        $section = $this->sectionMapper->findSectionBySlugWithParents($this->params()->fromRoute('gallery'),array($this->params()->fromRoute('name'),$this->params()->fromRoute('category')));



        $arrayRes = array(
            'title' => $section->getName().' - '.$section->getParent()->getName().' - '.$section->getParent()->getParent()->getName(),
            'content' => $models->getModelGalleryContent($this->params()->fromRoute('category'),$this->params()->fromRoute('name'),$this->params()->fromRoute('gallery')),
            'modelinfo' => $models->getModelInfo($this->params()->fromRoute('name'),$this->params()->fromRoute('category')),
            'path' => '/models/'.$this->params()->fromRoute('category').'/'.$this->params()->fromRoute('name').'/'.$this->params()->fromRoute('gallery'),
        );

        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new ViewModel($arrayRes);
    }

    public function pagesAction(){
        $link = $this->params()->fromRoute('link');
        $pages = new Pages($this->sectionMapper,$this->serializer);
        $viewmodel = new ViewModel();
        $arrayCacheRequest=array('view','page'.$link);
        if ($link === 'contact') {

            if ($this->cacheJson->isCached($arrayCacheRequest)) $viewmodel = new ViewModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));
            else {
                $arrayRes = $pages->getContactContent();
                $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
                $viewmodel = new ViewModel($arrayRes);
            }
            $viewmodel->setTemplate('frontend/index/contact.phtml');

        }

        if ($link === 'special-occasion') {
            if ($this->cacheJson->isCached($arrayCacheRequest)) $viewmodel = new ViewModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));
            else {

                $arrayRes = array(
                    'content' => $pages->getSpecialOccasionContent(),
                    'text' => $pages->getSpecialOccasionText()
                );
                $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
                $viewmodel = new ViewModel($arrayRes);
            }

            $viewmodel->setTemplate('frontend/index/specialoccasion.phtml');

        }

        if ($link === 'international') {
            if ($this->cacheJson->isCached($arrayCacheRequest)) $viewmodel = new ViewModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));
            else {
                $arrayRes = array('content' => $pages->getInternationalContent());
                $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
                $viewmodel = new ViewModel($arrayRes);
            }
            $viewmodel->setTemplate('frontend/index/international.phtml');

        }

        if ($link === 'become-a-model') {
                $arrayRes = array(
                    'content' => $pages->getBecomeamodelContent(),
                    'form' => new BecomeModelForm(),
                );
                $viewmodel = new ViewModel($arrayRes);

            $viewmodel->setTemplate('frontend/index/becomemodel.phtml');

        }

        return $viewmodel;
    }

    public function profileintrofoodAction(){

        $arrayCacheRequest=array('view','profileintrofd',$this->params()->fromRoute('link'),$this->params()->fromRoute('name'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new ViewModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));

        $professionals = new Professionals($this->sectionMapper,$this->serializer);

        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlug(Slug::slugify($this->params()->fromRoute('name'),array($this->params()->fromRoute('link'))));
        $sectiontype = $section->getParent()->getName();

        $path = '/'.$this->params()->fromRoute('link').'/'.$this->params()->fromRoute('name');



        $result = array('path' => $path,
            'content' => $professionals->getProfileFDArray($this->params()->fromRoute('name'),$this->params()->fromRoute('link')),
            'more' => $professionals->getMoreProfessionalsArray($this->params()->fromRoute('name'),$this->params()->fromRoute('link'),null),
            'type' => $sectiontype,
            'title' => $section->getName());


        $firstpageArray = $professionals->getFirstPageProfileAllFDArray($this->params()->fromRoute('name'),$this->params()->fromRoute('link'));



        $arrayRes = array_merge($result,array('firstpage' => $firstpageArray));
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new ViewModel($arrayRes);
    }

    public function profilefoodAction(){

        $arrayCacheRequest=array('view','profilefood',$this->params()->fromRoute('link'),$this->params()->fromRoute('name'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new ViewModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));

        $professionals = new Professionals($this->sectionMapper,$this->serializer);


        $section = $this->sectionMapper->findSectionBySlugWithParents($this->params()->fromRoute('name'),array($this->params()->fromRoute('link')));


        $arrayRes = array_merge(
            array('title' => $section->getName()),
            $professionals->getProfilePageArray($this->params()->fromRoute('name'),$this->params()->fromRoute('link'),null));
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new ViewModel($arrayRes);


    }

    public function pressfoodAction(){
        $arrayCacheRequest=array('view','pressfood',$this->params()->fromRoute('link'),$this->params()->fromRoute('name'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new ViewModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));
        $fd = new FoodDrink($this->sectionMapper,$this->serializer);

        $section = $this->sectionMapper->findSectionBySlugWithParents($this->params()->fromRoute('name'),array($this->params()->fromRoute('link')));

        $arrayRes = array(
            'title' => $section->getName().' - PRESS',
            'content' => $fd->getPressContentArray(array($this->params()->fromRoute('name'),$this->params()->fromRoute('link')))
        );
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new ViewModel($arrayRes);
    }


    public function pressFoodPostAction(){
        $arrayCacheRequest=array('view','pressfoodpost',$this->params()->fromRoute('link'),"press",$this->params()->fromRoute('name'),$this->params()->fromRoute('postname'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new ViewModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));

        $fooddrink = new FoodDrink($this->sectionMapper,$this->serializer);


        $path = '/'.$this->params()->fromRoute('link').'/'.$this->params()->fromRoute('name').'/press/'.$this->params()->fromRoute('postname');

        $section = $this->sectionMapper->findSectionBySlugWithParents($this->params()->fromRoute('postname'),array('press',$this->params()->fromRoute('name'),$this->params()->fromRoute('link')));

        $content = $fooddrink->getContentPressPost($this->params()->fromRoute('postname'),$this->params()->fromRoute('name'),$this->params()->fromRoute('link'));

        $arrayRes = array(
            'content' => $content,
            'path' => $path,
            'title' => $section->getName().' - '.$section->getParent()->getName().' - '.$section->getParent()->getParent()->getName(),
        );
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        $viewmodel = new ViewModel($arrayRes);

        if (array_key_exists('body',$content)) {
            $viewmodel->setTemplate('frontend/index/press-food-post-text.phtml');
        }

        return $viewmodel;


    }


    public function portfoliofoodAction(){

        $arrayCacheRequest=array('view','portfoliocontentfood',$this->params()->fromRoute('link'),$this->params()->fromRoute('type'),$this->params()->fromRoute('name'),$this->params()->fromRoute('portfolio'),$this->params()->fromRoute('gallery'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new ViewModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));

        $professionals = new Professionals($this->sectionMapper,$this->serializer);

        $title = ' - ' .strtoupper($this->params()->fromRoute('portfolio'));

        $gallery = $this->params()->fromRoute('gallery');

        if ($gallery!='') {
            $title.= ' - '.strtoupper($gallery);
            $gallery = '/'.$gallery;

        }
        $path = '/'.$this->params()->fromRoute('link').'/'.$this->params()->fromRoute('name').'/'.$this->params()->fromRoute('portfolio').$gallery;

        $section = $this->sectionMapper->findSectionBySlugWithParents($this->params()->fromRoute('name'),array($this->params()->fromRoute('link')));

        $arrayRes = array(
            'title' => $section->getName().$title,
            'content' => $professionals->getContentPortfolioGallery($this->params()->fromRoute('name'),null,$this->params()->fromRoute('link'),$this->params()->fromRoute('portfolio'),$this->params()->fromRoute('gallery')),
            'path' => $path
        );
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new ViewModel($arrayRes);
    }




    public function indexAction()
    {

        return new ViewModel();
    }


    /**
     * upload image for the become a model form
     * @return JsonModel
     */
    public function becomeModelUploadAction(){
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );


            //todo check the file

            $imagepath = 'mailimages/'.md5(time().' ').$post['files']['name'];
            $moved = move_uploaded_file($post['files']['tmp_name'], $imagepath);
            if (!$moved) {
                return new JsonModel(array('code' => 'error'));
            }

            return new JsonModel(array('code' => 'success', 'imagepath' => $imagepath,'origname' => $post['files']['name']));
        }
        return new JsonModel(array('code' => 'error'));
    }

    public function redirectsAction(){

        $intro = new Intro($this->sectionMapper,$this->serializer);
        $intro->redirects();
        exit;
        return new ViewModel();
    }

}
