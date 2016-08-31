<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Frontend\Controller;


use Application\Infrastructure\Email\EmailSender;
use Application\Infrastructure\Slug;
use Entities\Entity\Section;
use Entities\Mapper\SectionMapper;
use Entities\Util\Serializer;
use Frontend\Forms\BecomeModelForm;
use Frontend\Forms\BecomeModelFormValidator;
use Frontend\Infrastructure\Cache\CacheJson;
use Frontend\Infrastructure\Domain\FoodDrink;
use Frontend\Infrastructure\Domain\Intro;
use Frontend\Infrastructure\Domain\Models;
use Frontend\Infrastructure\Domain\Pages;
use Frontend\Infrastructure\Domain\Professionals;
use Zend\Form\Element;
use Zend\Http\Response;
use Zend\Mail\Transport\Sendmail;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Helper\Json;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ApiController extends AbstractActionController{

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
     * ApiController constructor.
     * @param $sectionMapper SectionMapper
     * @param $serializer Serializer
     * @param $cacheJson CacheJson
     */
    public function __construct($sectionMapper, $serializer, $cacheJson) {
        $this->sectionMapper = $sectionMapper;
        $this->serializer = $serializer;
        $this->cacheJson = $cacheJson;


    }


    /**
     * provides data for the intro page
     * @return JsonModel
     */
    public function homeAction(){






        $arrayCacheRequest=array('intro');
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new JsonModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));
        $intro = new Intro($this->sectionMapper,$this->serializer);

        /** @var Section $section */
        //$section = $this->sectionMapper->findSectionWithParentByName(0,'INTRO');
        $section = $this->sectionMapper->findSectionBySlugWithParents('intro',array());
        $arrayBackground = array_map(function($elem){
            return array(
                'link' =>"http://proofs.space-us-standard.s3.amazonaws.com/".$elem->getSection()->getPath().'/_media/'.$elem->getName(),
                'width' => $elem->getImageWidth(),
                'height' => $elem->getImageHeight(),
            );
        },$section->getImages());
        $arrayRes = array('content' => $intro->getContentArray(),'background' => $arrayBackground);
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new JsonModel($arrayRes);
    }


    /**
     * provides data for the mobile home page
     * @return JsonModel
     */
    public function homeMobileAction(){
        $arrayCacheRequest=array('intromobile');
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new JsonModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));
        $intro = new Intro($this->sectionMapper,$this->serializer);

        return new JsonModel(array('content' => $intro->getContentMobileArray()));
        $arrayRes = array('content' => $intro->getContentArray(),'background' => $arrayBackground);
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new JsonModel($arrayRes);
    }

    /**
     * provides data for the first page for each type (artysts, production, food&drink etc.)
     * @return JsonModel
     */
    public function introtypeAction(){
        $arrayCacheRequest=array('introtype',$this->params()->fromRoute('link'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new JsonModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));
        $intro = new Intro($this->sectionMapper,$this->serializer);

        $arrayRes = $intro->getContentIntroTypeArray($this->params()->fromRoute('link'));
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new JsonModel($arrayRes);
    }

    /**
     * provides data for the first page for each professional, includes profile info if exists
     * @return JsonModel
     */
    public function profileintroAction(){

        $arrayCacheRequest=array('profileintro',$this->params()->fromRoute('link'),$this->params()->fromRoute('type'),$this->params()->fromRoute('name'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new JsonModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));

        $professionals = new Professionals($this->sectionMapper,$this->serializer);
        $arrayBackground = array();
        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlugWithParents(Slug::slugify($this->params()->fromRoute('name')),array($this->params()->fromRoute('type'),$this->params()->fromRoute('link')));
        $sectiontype = $section->getParent()->getName();
        $section = $this->sectionMapper->findSectionWithParentByName($section->getId(),'BACKGROUND');
        if ($section!=null)
        $arrayBackground = array_map(function($elem){
            return array(
                'link' =>"http://proofs.space-us-standard.s3.amazonaws.com/".$elem->getSection()->getPath().'/_media/'.$elem->getName(),
                'width' => $elem->getImageWidth(),
                'height' => $elem->getImageHeight(),
            );
        },$section->getImages());
        $path = '/'.$this->params()->fromRoute('link').'/'.$this->params()->fromRoute('type').'/'.$this->params()->fromRoute('name');

        $result = array('path' => $path,
            'content' => $professionals->getProfileArray($this->params()->fromRoute('name'),$this->params()->fromRoute('type'),$this->params()->fromRoute('link')),
            'more' => $professionals->getMoreProfessionalsArray($this->params()->fromRoute('name'),$this->params()->fromRoute('type'),$this->params()->fromRoute('link')),
            'background' => $arrayBackground,
        'type' => $sectiontype);

        $firstpageArray = $professionals->getFirstPageProfileAllArray($this->params()->fromRoute('name'),$this->params()->fromRoute('type'),$this->params()->fromRoute('link'));
        $result = array_merge($result,array('firstpage' => $firstpageArray));


        $arrayRes = $result;
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new JsonModel($arrayRes);
    }


    /**
     * action for first page (intro + profile) for the food & drink type sections
     * @return JsonModel
     */
    public function profileintroFDAction(){

        $arrayCacheRequest=array('profileintrofd',$this->params()->fromRoute('link'),$this->params()->fromRoute('name'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new JsonModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));
        $professionals = new Professionals($this->sectionMapper,$this->serializer);
        $arrayBackground = array();
        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlug(Slug::slugify($this->params()->fromRoute('name'),array($this->params()->fromRoute('link'))));
        $sectiontype = $section->getParent()->getName();
        $section = $this->sectionMapper->findSectionWithParentByName($section->getId(),'BACKGROUND');
        if ($section!=null)
            $arrayBackground = array_map(function($elem){
                return array(
                    'link' =>"http://proofs.space-us-standard.s3.amazonaws.com/".$elem->getSection()->getPath().'/_media/'.$elem->getName(),
                    'width' => $elem->getImageWidth(),
                    'height' => $elem->getImageHeight(),
                );
            },$section->getImages());
        $path = '/'.$this->params()->fromRoute('link').'/'.$this->params()->fromRoute('name');


        $result = array('path' => $path,
            'content' => $professionals->getProfileFDArray($this->params()->fromRoute('name'),$this->params()->fromRoute('link')),
            'more' => $professionals->getMoreProfessionalsArray($this->params()->fromRoute('name'),$this->params()->fromRoute('link'),null),
            'background' => $arrayBackground,
            'type' => $sectiontype);


        $firstpageArray = $professionals->getFirstPageProfileAllFDArray($this->params()->fromRoute('name'),$this->params()->fromRoute('link'));

        $result = array_merge($result,array('firstpage' => $firstpageArray));


        $arrayRes = $result;
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new JsonModel($arrayRes);
    }

    public function portfoliocontentAction(){

        $gallery = $this->params()->fromRoute('gallery');
        if ($gallery!='') $gallery = '/'.$gallery;

        $arrayCacheRequest=array('portfoliocontent',$this->params()->fromRoute('link'),$this->params()->fromRoute('type'),$this->params()->fromRoute('name'),$this->params()->fromRoute('portfolio'),$this->params()->fromRoute('gallery'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new JsonModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));
        $professionals = new Professionals($this->sectionMapper,$this->serializer);



        $path = '/'.$this->params()->fromRoute('link').'/'.$this->params()->fromRoute('type').'/'.$this->params()->fromRoute('name').'/'.$this->params()->fromRoute('portfolio').$gallery;


        $arrayRes = array(
            'content' => $professionals->getContentPortfolioGallery($this->params()->fromRoute('name'),$this->params()->fromRoute('type'),$this->params()->fromRoute('link'),$this->params()->fromRoute('portfolio'),$this->params()->fromRoute('gallery')),
            'path' => $path
        );
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new JsonModel($arrayRes);
    }


    public function portfoliocontentFDAction(){

        $arrayCacheRequest=array('portfoliocontentfd',$this->params()->fromRoute('link'),$this->params()->fromRoute('name'),$this->params()->fromRoute('portfolio'),$this->params()->fromRoute('gallery'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new JsonModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));


        $professionals = new Professionals($this->sectionMapper,$this->serializer);


        $gallery = $this->params()->fromRoute('gallery');

        if ($gallery!='') $gallery = '/'.$gallery;
        $path = '/'.$this->params()->fromRoute('link').'/'.$this->params()->fromRoute('name').'/'.$this->params()->fromRoute('portfolio').$gallery;


        $arrayRes = array(
            'content' => $professionals->getContentPortfolioGallery($this->params()->fromRoute('name'),null,$this->params()->fromRoute('link'),$this->params()->fromRoute('portfolio'),$this->params()->fromRoute('gallery')),
            'path' => $path
        );
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new JsonModel($arrayRes);
    }

    public function profileAction(){
        $arrayCacheRequest=array('profile',$this->params()->fromRoute('link'),$this->params()->fromRoute('type'),$this->params()->fromRoute('name'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new JsonModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));
        $professionals = new Professionals($this->sectionMapper,$this->serializer);

        $arrayRes = $professionals->getProfilePageArray($this->params()->fromRoute('name'),$this->params()->fromRoute('type'),$this->params()->fromRoute('link'));
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new JsonModel($arrayRes);
    }

    /**
     * provides data about models categories
     * @return JsonModel
     */
    public function modelsAction(){
        $arrayCacheRequest=array('modelscategories');
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new JsonModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));
        $models = new Models($this->sectionMapper,$this->serializer);


        $arrayRes = array(
            'content' => $models->getCategoriesArray(),
            'path' => '/models'
        );
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new JsonModel($arrayRes);
    }

    /**
     * provides data about all models from a category
     * @return JsonModel
     *
     */
    public function modelscategoryAction(){
        $arrayCacheRequest=array('modelscategory',$this->params()->fromRoute('category'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new JsonModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));
        $models = new Models($this->sectionMapper,$this->serializer);


        $arrayRes = array(
            'content' => $models->getCategoryContentArray($this->params()->fromRoute('category')),
            'path' => '/models/'.$this->params()->fromRoute('category')
        );
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new JsonModel($arrayRes);
    }


    /**
     * provides data about model portfolio sections and links (previous,next, all)
     * @return JsonModel
     */
    public function modelPageAction(){
        $arrayCacheRequest=array('modelpage',$this->params()->fromRoute('category'),$this->params()->fromRoute('name'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new JsonModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));
        $models = new Models($this->sectionMapper,$this->serializer);


        $links = $models->getModelLinksArray($this->params()->fromRoute('name'),$this->params()->fromRoute('category'));


        $arrayRes = array(
            'content' => $models->getGalleriesForModelArray($this->params()->fromRoute('name'),$this->params()->fromRoute('category')),
            'path' => '/models/'.$this->params()->fromRoute('category').'/'.$this->params()->fromRoute('name'),
            'links' => $links
        );
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new JsonModel($arrayRes);
    }

    /**
     * provides data from a portfolio gallery for a model
     * @return JsonModel
     */
    public function modelGalleryAction(){
        $arrayCacheRequest=array('modelgallery',$this->params()->fromRoute('category'),$this->params()->fromRoute('name'),$this->params()->fromRoute('gallery'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new JsonModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));
        $models = new Models($this->sectionMapper,$this->serializer);

        $path = '/models/'.$this->params()->fromRoute('category').'/'.$this->params()->fromRoute('name').'/'.$this->params()->fromRoute('gallery');

        $arrayRes = array(
            'content' => $models->getModelGalleryContent($this->params()->fromRoute('category'),$this->params()->fromRoute('name'),$this->params()->fromRoute('gallery')),
            'modelinfo' => $models->getModelInfo($this->params()->fromRoute('name'),$this->params()->fromRoute('category')),
            'path' => '/models/'.$this->params()->fromRoute('category').'/'.$this->params()->fromRoute('name').'/'.$this->params()->fromRoute('gallery'),
        );
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new JsonModel($arrayRes);

    }


    /**
     * provides data about all models characteristics for models search
     * @return JsonModel
     */
    public function searchAction(){
        $arrayCacheRequest=array('modelsearch');
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new JsonModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));
        $models = new Models($this->sectionMapper,$this->serializer);


        $arrayRes = $models->getModelsCar();
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new JsonModel($arrayRes);
    }


    /**
     * provides data for the contact page
     * @return JsonModel
     */
    public function contactAction(){
        $arrayCacheRequest=array('contectpage');
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new JsonModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));
        $pages = new Pages($this->sectionMapper,$this->serializer);


        $arrayRes = $pages->getContactContent();
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new JsonModel($arrayRes);

    }


    public function specialoccasionAction(){

        $arrayCacheRequest=array('specialoccasionpage');
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new JsonModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));
        $pages = new Pages($this->sectionMapper,$this->serializer);


        $arrayRes = array(
            'content' => $pages->getSpecialOccasionContent(),
            'text' => $pages->getSpecialOccasionText(),
            //'path' => '/models/'.$this->params()->fromRoute('category').'/'.$this->params()->fromRoute('name').'/'.$this->params()->fromRoute('gallery'),
        );
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new JsonModel($arrayRes);

    }

    public function internationalAction(){

        $arrayCacheRequest=array('internationalpage');
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new JsonModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));

        $pages = new Pages($this->sectionMapper,$this->serializer);


        $arrayRes = $pages->getInternationalContent();
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new JsonModel($arrayRes);

    }


    /**
     * returns the become a model page, image in the left and form displayed, !!!! returns html not json
     * @return ViewModel
     */
    public function becomeamodelAction(){
        $pages = new Pages($this->sectionMapper,$this->serializer);



        $view = new ViewModel(array(
            'content' => $pages->getBecomeamodelContent(),
            'form' => new BecomeModelForm(),
        ));
        $view->setTerminal(true);

        return $view;

    }


    /**
     * submit become a model form
     * @return JsonModel
     */
    public function sendBecomeamodelAction() {
        if ($this->getRequest()->isPost()) {
            $form = new BecomeModelForm();
            $validator = new BecomeModelFormValidator();
            $form->setInputFilter($validator->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                //send the email
                $text = '';
                /** @var Element $element */
                foreach ($form->getElements() as $element){
                    if ($element->getAttribute('name') == "filelist") continue;
                    if ($data[$element->getAttribute('name')]!='')
                    $text.= $element->getLabel()."<br />\n".$data[$element->getAttribute('name')]."<br /><br />\n\n";
                }

                $images = explode('|::|',$data['filelist']);


                $section = $this->sectionMapper->findSectionBySlugWithParents('become-a-model',array('second-menu'));
                $emails = explode(',',$section->getDescription3());
                //$emails = array("gigi2000@ymail.com");

                $sendmail = new EmailSender($text,$emails,$images);
                $sendmail->send();


                return new JsonModel(array('code' => 'success'));
                //return new JsonModel(array('code' => 'error', 'message' => 'database error'));
            } else
                return new JsonModel(array('code' => 'invalid', 'message' => $form->getMessages()));

        } else return new JsonModel(array('code' => 'error', 'message' => 'it should be POST'));
    }


    public function presscontentFDAction(){

        $arrayCacheRequest=array('presscontentfd',$this->params()->fromRoute('link'),$this->params()->fromRoute('name'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new JsonModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));


        $fd = new FoodDrink($this->sectionMapper,$this->serializer);

        $arrayRes = array(
            'content' => $fd->getPressContentArray(array($this->params()->fromRoute('name'),$this->params()->fromRoute('link')))
        );
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new JsonModel($arrayRes);
    }


    /**
     * profile page for food type sections
     * @return JsonModel
     */
    public function profileFDAction(){
        $arrayCacheRequest=array('profilefd',$this->params()->fromRoute('link'),$this->params()->fromRoute('name'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new JsonModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));
        $professionals = new Professionals($this->sectionMapper,$this->serializer);

        $arrayRes = $professionals->getProfilePageArray($this->params()->fromRoute('name'),$this->params()->fromRoute('link'),null);
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new JsonModel($arrayRes);
    }

    public function pressPostFDAction(){

        $arrayCacheRequest=array('presspostfd',$this->params()->fromRoute('link'),$this->params()->fromRoute('name'),$this->params()->fromRoute('postname'));
        if ($this->cacheJson->isCached($arrayCacheRequest)) return new JsonModel($this->cacheJson->getCachedContentAsArray($arrayCacheRequest));

            $fooddrink = new FoodDrink($this->sectionMapper,$this->serializer);


            $path = '/'.$this->params()->fromRoute('link').'/'.$this->params()->fromRoute('name').'/press/'.$this->params()->fromRoute('postname');



        $arrayRes = array(
            'content' => $fooddrink->getContentPressPost($this->params()->fromRoute('postname'),$this->params()->fromRoute('name'),$this->params()->fromRoute('link')),
            'links' => $fooddrink->getPressPostLinks($this->params()->fromRoute('postname'),$this->params()->fromRoute('name'),$this->params()->fromRoute('link')),
            'path' => $path
        );
        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayRes);
        return new JsonModel($arrayRes);

    }




}