<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Frontend\Infrastructure\Domain;


use Entities\Entity\Section;
use Entities\Mapper\SectionMapper;
use Entities\Util\Serializer;

class Intro {
    public $allowedArray= array('artists','emerging artists','models','production','spokespeople','food & drink','food&drink','influencers1');

    public $allowedSectionsArray= array('portfolio','press','profile','videos','social');

    public $forbiddenArray= array('become-a-model');
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

    public function getContentArray(){
        $arrayIEntities = $this->sectionMapper->findSectionWithParent(0);
        $arrayIntro = array();
        /** @var Section $introS */
        foreach ($arrayIEntities as $introS){
            if ($introS->isDisabled()) continue;
            if (!in_array(strtolower($introS->getName()),$this->allowedArray)) continue;

            $arrayElem = $this->serializer->serialize($introS);

            if (strtolower($introS->getName())!=='models') {

                $arraySubsections = array();
                /** @var Section $subsection */
                if (($introS->getSlug()==='food_drink')||($introS->getSlug()==='food-_-drink')||($introS->getSlug()==='influencers')) {
                    $arrayGetSubsections = $introS->getSubsectionSortedByName();
                } else {
                    $arrayGetSubsections = $introS->getSubsections();
                }
                foreach ($arrayGetSubsections as $subsection) {
                    if ($subsection->isDisabled()) continue;
                    if (strtolower($subsection->getDescription4()=='mobile')) continue;
                    $arraySubsection = $this->serializer->serialize($subsection);
                    $arraySSubs = array();
                    $arrayGetSSubsections = $subsection->getSubsectionSortedByName();
                    /** @var Section $artist */
                    foreach ($arrayGetSSubsections as $artist){
                        if ($artist->isDisabled()) continue;
                        $arrayArtist = $this->serializer->serialize($artist);
                        $arraySSubs[] = $arrayArtist;
                    }

                    $arraySubsection['subsections'] = $arraySSubs;

                    $arraySubsections[] = $arraySubsection;
                }

                $arrayElem['subsections'] = $arraySubsections;
            }

            $arrayIntro[] = $arrayElem;
        }
        return $arrayIntro;

    }

    public function redirects(){
        $arrayIEntities = $this->sectionMapper->findSectionWithParent(0);
        /** @var Section $introS */
        foreach ($arrayIEntities as $introS){
            if ($introS->isDisabled()) continue;
            if (!in_array(strtolower($introS->getName()),$this->allowedArray)) continue;

            if (strtolower($introS->getName())!=='asasasmodels') {

                $isfd = false;
                /** @var Section $subsection */
                if (($introS->getSlug()==='food_drink')||($introS->getSlug()==='food-_-drink')||($introS->getSlug()==='influencers')) {
                    $arrayGetSubsections = $introS->getSubsectionSortedByName();
                    $isfd=true;
                } else {
                    $arrayGetSubsections = $introS->getSubsections();
                }
                foreach ($arrayGetSubsections as $subsection) {
                    if ($subsection->isDisabled()) continue;
                    if (strtolower($subsection->getDescription4()=='mobile')) continue;
                    $addend = '';
                    if ($this->hasProfile($subsection)) $addend = '/profile';
                    if ($isfd) {
                        echo 'Redirect /'.$subsection->getSlug().'/ /'.$introS->getSlug().'/'.$subsection->getSlug().$addend."<br>\n";
                        echo 'Redirect /'.$subsection->getSlug().' /'.$introS->getSlug().'/'.$subsection->getSlug().$addend."<br>\n";
                        continue;
                    }
                    $arrayGetSSubsections = $subsection->getSubsectionSortedByName();
                    /** @var Section $artist */
                    foreach ($arrayGetSSubsections as $artist){
                        if ($artist->isDisabled()) continue;
                        $addend = '';
                        if ($this->hasProfile($artist)) $addend = '/profile';
                        if (!$isfd) echo 'Redirect /'.$artist->getSlug().'/ /'.$introS->getSlug().'/'.$subsection->getSlug().'/'.$artist->getSlug().$addend."<br>\n";
                        if (!$isfd) echo 'Redirect /'.$artist->getSlug().' /'.$introS->getSlug().'/'.$subsection->getSlug().'/'.$artist->getSlug().$addend."<br>\n";
                    }

                }

            }

        }
        return ;

    }

    /**
     * @param Section $section
     */
    private function hasProfile($section){
        /** @var Section $ssection */
        foreach ($section->getSubsections() as $ssection){
            if ($ssection->isDisabled()) continue;
            if (($ssection->getSlug() === 'profile')&&(strtolower($ssection->getDescription4())!=='first page')) return true;
        }

        return false;
    }

    public function getContentMobileArray(){
        $arrayIntro = array();

        $arrayIEntities = $this->sectionMapper->findSectionWithParent(0);

        $arrayrand = [
            'http://proofs.space-us-standard.s3.amazonaws.com/media_folder/ studio/PRODUCTION/PRODUCERS/ROSEANNA%20 studio/PORTFOLIO/CLIENTS/_images1/Covert-Affairs-Cast_7337.jpg',
            'http://proofs.space-us-standard.s3.amazonaws.com/media_folder/ studio/ARTISTS/SEAMSTRESS/MAY%20CHEN/PORTFOLIO/SEAMSTRESS/_images1/img_3379_7658.jpg',
            'http://proofs.space-us-standard.s3.amazonaws.com/media_folder/ studio/ARTISTS/SEAMSTRESS/MAY%20CHEN/PORTFOLIO/SEAMSTRESS/_images1/img_3423_1230.jpg',
            'http://proofs.space-us-standard.s3.amazonaws.com/media_folder/ studio/ARTISTS/MAKEUP&HAIR/CLAUDINE%20BALTAZAR/PORTFOLIO/CELEBRITIES/_images1/Screen%20shot%202012-11-26%20at%2010.35.02%20AM_6554.jpg',
            'http://proofs.space-us-standard.s3.amazonaws.com/media_folder/ studio/ARTISTS/STYLISTS/SERGE%20KERBEL/PORTFOLIO/WOMEN/_images2/Screen%20Shot%202014-09-03%20at%209.24.15%20PM_7518.jpg',
            'http://proofs.space-us-standard.s3.amazonaws.com/media_folder/ studio/ARTISTS/STYLISTS/PETER%20PAPAPETROU/PORTFOLIO/CELEBRITIES/_images2/peter_.Tre%20Armstrong_9905.jpg',
            'http://proofs.space-us-standard.s3.amazonaws.com/media_folder/ studio/ARTISTS/STYLISTS/PETER%20PAPAPETROU/PORTFOLIO/CELEBRITIES/_images2/Peter_.Javier%20Barden_4033.jpg',
        ];

        /** @var Section $introS */
        foreach ($arrayIEntities as $introS){
            if ($introS->isDisabled()) continue;
            if (!in_array(strtolower($introS->getName()),$this->allowedArray)) continue;
            $arrayElem = array();

            //$arrayElem['imagepath'] = $arrayrand[rand(0,count($arrayrand)-1)];

            /** @var Section $subsect */
            foreach ($introS->getSubsections() as $subsect){
                if (($subsect->getSlug()==="mobile-image")||(strtolower($subsect->getDescription4())==='mobile')){
                    $image = $subsect->getFirstImage();
                    if ($image!=null){
                        $arrayElem['imagepath'] = "http://" . $subsect->getBucket() . ".s3.amazonaws.com/" . $subsect->getPath() . '/_thumbnails2/' . $image->getName();
                    }

                }
            }


            $arrayElem['name'] = $introS->getName();

            $arrayElem['slug'] = '/'.$introS->getSlug();
            $arrayIntro[] = $arrayElem;
        }

        $arrayIEntities = $this->sectionMapper->findSectionBySlugWithParents('second-menu',array());

        /** @var Section $introS */
        foreach ($arrayIEntities->getSubsections() as $introS){
            if ($introS->isDisabled()) continue;
            if (in_array($introS->getSlug(),$this->forbiddenArray)) continue;

            $arrayElem = array();

            //$arrayElem['imagepath'] = $arrayrand[rand(0,count($arrayrand)-1)];

            /** @var Section $subsect */
            foreach ($introS->getSubsections() as $subsect){
                if (($subsect->getSlug()==="mobile-image")||(strtolower($subsect->getDescription4())==='mobile')){
                    $image = $subsect->getFirstImage();
                    if ($image!=null){
                        $arrayElem['imagepath'] = "http://" . $subsect->getBucket() . ".s3.amazonaws.com/" . $subsect->getPath() . '/_thumbnails2/' . $image->getName();
                    }

                }
            }


            $arrayElem['name'] = $introS->getName();

            $arrayElem['slug'] = '/'.$introS->getSlug();
            $arrayIntro[] = $arrayElem;
        }


        return $arrayIntro;
    }

    public function getContentIntroTypeArray($type){
        $section = $this->sectionMapper->findSectionBySlugWithParentId($type,0);
        $result = array('name' => '','slug'=>'','content' => array());
        if ($section == null) return $result;

        $arrayContent = array();
        /** @var Section $category */
        foreach ($section->getSubsections() as $category){
            if ($category->isDisabled()) continue;

            $arrayCategory = $this->serializer->serialize($category);

            if (($section->getSlug() =='food_drink')||($section->getSlug() =='food-_-drink')||($section->getSlug() =='influencers')) {
                $arrayContent[]=$arrayCategory;
                continue;
            }

            $arraySubsect = array();
            /** @var Section $professional */
            foreach ($category->getSubsections() as $professional){
                if ($professional->isDisabled()) continue;
                //if (!in_array($professional->getSlug(),$this->allowedSectionsArray)) continue;
                $arraySubsect[] = $this->serializer->serialize($professional);
            }

            $arrayCategory['subsections'] = $arraySubsect;
            $arrayContent[] = $arrayCategory;

        }
        $result = array(
            'name' => $section->getName(),
            'slug' => '/'.$type,
            'content' => $arrayContent
        );
        return $result;

    }
}