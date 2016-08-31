<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Frontend\Infrastructure\Domain;


use Entities\Entity\Image;
use Entities\Entity\Section;
use Entities\Entity\Text;
use Entities\Mapper\SectionMapper;
use Entities\Util\Serializer;

class Pages {
    public $allowedArray= array('portfolio','press','profile','videos','social');

    public $forbiddenArray= array('intro','inactive','text','preview','info','comp card');

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

    public function getBecomeamodelContent(){
        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlugWithParents('become-a-model',array());

        $becomeArray = array(
            'requirements' => '',
            'image' => '',
        );

        foreach ($section->getSubsections() as $subsect){
            if ($subsect->getSlug() == 'requirement-text'){
                $texts = $subsect->getTexts();
                if (count($texts) > 0) {
                    /** @var Text $text */
                    $text = $texts[0];
                    $becomeArray['requirements'] = $text->getBody();
                }
            }
        }

        $images = $section->getImages();
        if (count($images)>0) {
            $nImage = $images[0];
            $becomeArray['image'] ="http://".$section->getBucket().".s3.amazonaws.com/".$section->getPath().'/_images1/'.$nImage->getName();
        }


        return $becomeArray;
    }

    public function getInternationalContent(){
        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlugWithParents("international", array("second-menu"));


        $arrayInternational = array();

        $arrayGetSubsections = $section->getSubsectionSortedByName();
        /** @var Section $model */
        foreach ($arrayGetSubsections as $model) {
            if ($model->getStatus() == 0) continue;

            $arrayElem = $this->serializer->serialize($model);

            ///** @var Section $previewSection */
            $previewSection = null;
            $listSection = null;
            /** @var Section $subsect */
            foreach ($model->getSubsections() as $subsect) {
                if ($subsect->getSlug() == "preview") {
                    $previewSection = $subsect;
                    continue;
                }

                if ($subsect->getSlug() == "list") {
                    $listSection = $subsect;
                    continue;
                }
             }


            if ($previewSection != null) {

                $arrayImages = $previewSection->getImages();

                if (count($arrayImages) !== 0) {

                    /** @var Image $image */
                    $image = $arrayImages[0];
                    $arrayElem['imagepath'] = "http://" . $previewSection->getBucket() . ".s3.amazonaws.com/" . $previewSection->getPath() . '/_thumbnails2/' . $image->getName();
                }

            }

            if ($listSection != null) {

                $arrayTexts = $listSection->getTexts();

                if (count($arrayTexts) !== 0) {
                    /** @var Text $text */
                    $text = $arrayTexts[0];
                    $arrayElem['list'] = $text->getBody();
                }

            }

            $arrayInternational[] = $arrayElem;
        }

        //exit;
        return $arrayInternational;
    }


    public function getSpecialOccasionContent(){
        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlugWithParents("special-occasion", array("second-menu"));


        $arraySpecial = array();

        $arrayGetSubsections = $section->getSubsectionSortedByName();
        /** @var Section $model */
        foreach ($arrayGetSubsections as $model) {
            if ($model->isDisabled()) continue;
            if (in_array(strtolower($model->getSlug()),$this->forbiddenArray)) continue;

            $arrayElem = $this->serializer->serialize($model);

            ///** @var Section $previewSection */
            $previewSection = null;
            /** @var Section $subsect */
            foreach ($model->getSubsections() as $subsect) {
                if ($subsect->getSlug() == "preview") {
                    $previewSection = $subsect;
                    continue;
                }

            }


            if ($previewSection != null) {

                $arrayImages = $previewSection->getImages();

                if (count($arrayImages) !== 0) {

                    /** @var Image $image */
                    $image = $arrayImages[0];
                    $arrayElem['imagepath'] = "http://" . $previewSection->getBucket() . ".s3.amazonaws.com/" . $previewSection->getPath() . '/_thumbnails2/' . $image->getName();
                }

            }

            $arraySpecial[] = $arrayElem;
        }

        //exit;
        return $arraySpecial;
    }

    public function getSpecialOccasionText(){
        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlugWithParents('special-occasion',array('second-menu'));

        /** @var Section $subsection */
        foreach ($section->getSubsections() as $subsection){
            if ($subsection->getSlug()!=='text') continue;

            $texts = $subsection->getTexts();
            if (count($texts)>0){
                /** @var Text $text */
                $text = $texts[0];
                return $text->getBody();
            }
        }

        return '';
    }

    public function getContactContent(){
        //general-contact-info
        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlugWithParents('contact',array('second-menu'));

        $partText ='';

        $texts = array();

        /** @var Section $subsection */
        foreach ($section->getSubsections() as $subsection){
            if ($subsection->isDisabled()) continue;

            if ($subsection->getSlug()==='general-contact-info') {
                $text = $subsection->getFirstText();
                if ($text!=null) $partText = $text->getBody();
                continue;
            }


            $text = $subsection->getFirstText();
            if ($text!=null) $texts[]=$text->getBody();
        }

        return array('firsttext' => $partText,'texts' => $texts);


    }


}