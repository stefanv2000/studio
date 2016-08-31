<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Frontend\Infrastructure\Domain;


use Entities\Entity\Image;
use Entities\Entity\Section;
use Entities\Mapper\SectionMapper;
use Entities\Util\Serializer;

class Models {
    public $allowedArray = array('portfolio', 'press', 'profile', 'videos', 'social');

    public $forbiddenArray = array('intro', 'inactive', 'text', 'preview', 'info', 'comp card');

    /**
     * @var SectionMapper
     */
    private $sectionMapper;
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * Intro constructor.
     * @param $sectionMapper
     */
    public function __construct($sectionMapper, $serializer) {
        $this->sectionMapper = $sectionMapper;
        $this->serializer = $serializer;
    }


    public function getCategoriesArray() {
        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlugWithParents("models", array());

        $arrayCategory = array();

        /** @var Section $category */
        foreach ($section->getSubsections() as $category) {
            if ($category->isDisabled()) continue;
            if (in_array(strtolower($category->getName()), $this->forbiddenArray)) continue;
            $arrayElem = $this->serializer->serialize($category);

            $arrayCategory[] = $arrayElem;
            //echo $profileS->getName().' '.count($profileS->getSubsections()).' ';
        }

        //exit;
        return $arrayCategory;
    }

    public function getCategoryContentArray($category) {
        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlugWithParents($category, array("models"));


        $arrayCategory = array();


        $arrayGetSubsections = $section->getSubsectionSortedByName();
        /** @var Section $model */
        foreach ($arrayGetSubsections as $model) {
            if ($model->isDisabled()) continue;

            $arrayElem = $this->serializer->serialize($model);

            ///** @var Section $previewSection */
            //$previewSection = $this->sectionMapper->findSectionBySlugWithParents($model->getSlug(),array($category,'models'));
            $previewSection = null;
            /** @var Section $firstSection */
            $firstSection = null;
            /** @var Section $subsect */
            foreach ($model->getSubsections() as $subsect) {
                if ($subsect->isDisabled()) continue;
                if ($subsect->getSlug() == "preview") {
                    $previewSection = $subsect;
                    continue;
                }
                if (($firstSection == null) && ((!in_array(strtolower($subsect->getName()), $this->forbiddenArray)))) $firstSection = $subsect;
            }


            if ($previewSection != null) {


                    $image = $previewSection->getFirstImage();
                    if ($image!==null) $arrayElem['imagepath'] = "http://" . $previewSection->getBucket() . ".s3.amazonaws.com/" . $previewSection->getPath() . '/_thumbnails2/' . $image->getName();


            }

            $modelSections = $model->getSubsections();
            $arrayElem['firstcategory'] = $firstSection->getSlug();


            $arrayCategory[] = $arrayElem;
            //echo $profileS->getName().' '.count($profileS->getSubsections()).' ';
        }

        //exit;
        return $arrayCategory;
    }

    public function getGalleriesForModelArray($name, $category) {
        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlugWithParents($name, array($category, "models"));

        $arrayGalleries = array();

        $arrayGalleries[] = array(
            'name' => $section->getName(),
            'id' => -1,
        );

        /** @var Section $gallery */
        foreach ($section->getSubsections() as $gallery) {
            if ($gallery->isDisabled()) continue;

            if (in_array(strtolower($gallery->getName()), $this->forbiddenArray)) continue;

            $arrayElem = $this->serializer->serialize($gallery);


            $arrayGalleries[] = $arrayElem;
        }
        return $arrayGalleries;
    }

    public function getModelLinksArray($name, $category) {
        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlugWithParents($category, array("models"));

        $arrayModelLinks = array(
            'allmodels' => '/models/' . $category,
            'previousmodel' => '',
            'nextmodel' => '',
        );


        $isnext = false;

        $first=null;
        $last=null;

        $previousmodel = null;
        $nextmodel = null;

        /** @var Section $model */
        foreach ($section->getSubsectionSortedByName() as $model) {
            if ($model->isDisabled()) continue;

            if ($first===null) $first = $model;

            if ($isnext) {
                $nextmodel=$model;
                $isnext=false;
                //$arrayModelLinks['nextmodel'] = '/models/' . $category . '/' . $model->getSlug();
            }

            if ($model->getSlug() == $name) {
                $previousmodel=$last;
                $isnext = true;
                //if ($previous != null) $arrayModelLinks['previousmodel'] = '/models/' . $category . '/' . $previous->getSlug();

            }
            $last=$model;

        }

        if ($nextmodel==null) $nextmodel=$first;;
        if ($previousmodel==null) $previousmodel = $last;
        $arrayModelLinks['previousmodel'] = '/models/' . $category . '/' . $previousmodel->getSlug();
        $arrayModelLinks['nextmodel'] = '/models/' . $category . '/' . $nextmodel->getSlug();

        return $arrayModelLinks;
    }

    public function getModelGalleryContent($category, $name, $gallery) {

        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlugWithParents($gallery, array($name, $category, 'models'));

        $path = "http://" . $section->getBucket() . ".s3.amazonaws.com/" . $section->getPath();
        $serial = $this->serializer;

        /**
         * @param $image Image
         * @return array
         */
        $filterf = function ($image) use ($serial, $path, $section) {
            $sImage = $serial->serialize($image);
            $sImage['linkpath'] = $path;

            return $sImage;


        };

        $array = array_map($filterf, array_values(array_filter($section->getImages(), function ($image) {
            if ($image->isEnabled()) return true;
            return false;
        })));

        return $array;

    }

    public function getModelInfo($modelName,$category){
        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlugWithParents($modelName, array($category, 'models'));

        $arrayDetails= array(
            'compcard' => '',
            'interview' => '',
            'info' => '',
            'instagramlink' => '',
        );

        if ($section == null) return $arrayDetails;

        if (($section->getLink()!=='')) $arrayDetails['instagramlink']=$section->getLink();

        if ($section->getText()!='') $arrayDetails['interview'] = $section->getText();
        /** @var Section $subsect */
        foreach ($section->getSubsections() as $subsect){
            if ($subsect->getSlug() == 'info'){
                $arrayDetails['info'] = '';
                if ($subsect->getFirstText()!=null)
                    $arrayDetails['info'] = $subsect->getFirstText()->getBody();
            }

            if (strtolower($subsect->getDescription4()) == 'comp card'){
                $arrayDetails['compcard'] = '/generate/pdf/'.$subsect->getId();
            }
        }

        return $arrayDetails;
    }

    public function getModelsCar() {
        $arrayCar = [];
        $arrayModels = [];

        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlugWithParents("models", array());

        /** @var Section $category */
        foreach ($section->getSubsections() as $category) {
            if ($category->isDisabled()) continue;
            if (in_array(strtolower($category->getName()), $this->forbiddenArray)) continue;


            /** @var Section $model */
            foreach ($category->getSubsections() as $model) {
                if ($model->isDisabled()) continue;

                $modelElem = [
                    'name' => $model->getName(),
                    'imagepath' => '',
                    'slug' => '/models/' . $category->getSlug() . '/' . $model->getSlug(),
                    'info' => []
                ];

                $infoSection = null;
                $previewSection = null;
                /** @var Section $info */
                foreach ($model->getSubsections() as $info) {
                    if ($info->getSlug() == "preview") {
                        $previewSection = $info;
                        continue;
                    }
                    if ($info->getSlug() == 'info') {
                        $infoSection = $info;
                        break;
                    }
                }

                if ($infoSection == null) continue;

                $texts = $infoSection->getTexts();
                $modelCar = '';
                $text = $infoSection->getFirstText();
                if ($text!==null) $modelCar = $text->getBody();

                //echo $modelCar."<br>\n";
                $modelInfo = $this->splitModelInfo($modelCar);


                foreach ($modelInfo as $key => $value) {
                    if ($key == '') echo $modelCar . "<br>\n";
                    if ($value == '') echo $modelCar . "<br>\n";
                    if (!array_key_exists($key, $arrayCar)) $arrayCar[$key] = [];

                    if (array_search($value, $arrayCar[$key]) === false) $arrayCar[$key][] = $value;
                }

                $modelElem['info'] = $modelInfo;

                if ($previewSection != null) {



                        $image = $previewSection->getFirstImage();
                    if ($image!==null) $modelElem['imagepath'] = "http://" . $previewSection->getBucket() . ".s3.amazonaws.com/" . $previewSection->getPath() . '/_thumbnails2/' . $image->getName();

                }

                $arrayModels[] = $modelElem;
            }


        }

        foreach ($arrayCar as $key => $value) {
            asort($value);
            $arrayCar[$key] = array_values($value);
        }



        return array(
            'criteria' => $arrayCar,
            'models' => $arrayModels
        );
    }

    private function splitModelInfo($modelInfo) {
        if (trim($modelInfo) == '') return [];
        $infoarr = explode('|', $modelInfo);
        $resAr = [];
        foreach ($infoarr as $part) {
            $partAr = explode(' ', trim($part));
            if (count($partAr) <= 1) continue;
            $infocar = strtolower($partAr[0]);
            $infoval = strtolower(implode(' ', array_slice($partAr, 1)));
            $resAr[$infocar] = $infoval;
        }

        return $resAr;
    }


}