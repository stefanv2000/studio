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

class Professionals {
    public $allowedArray= array('portfolio','press','profile','videos','social');

    public $forbiddenArray= array('text','preview','info','bio');

    /**
     * @var SectionMapper
     */
    private $sectionMapper;
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @param $sectionMapper
     */
    public function __construct($sectionMapper, $serializer) {
        $this->sectionMapper = $sectionMapper;
        $this->serializer = $serializer;
    }

    public function getProfileArray($name,$type,$main) {

        return $this->getProfileParentsArray($name,array($type,$main));
    }

    public function getProfileFDArray($name,$type) {
        $result = $this->getProfileParentsArray($name,array($type));
        $result[0]['slug'] = '';
        return $result;
    }

    public function getProfileParentsArray($name,$arrayparents) {

        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlugWithParents($name,$arrayparents);

        $parent=$section;
        for ($i=0;$i<count($arrayparents);$i++){
            $parent= $parent->getParent();
        }

        $arrayProfile = array();
        $arrayProfile[] = array(
            'slug' => $parent->getSlug(),
            'name' => $section->getName(),
            'id' => -1,
        );

        /** @var Section $profileS */
        foreach ($section->getSubsections() as $profileS) {
            if ($profileS->isDisabled()) continue;
            if (!in_array(strtolower($profileS->getName()), $this->allowedArray)) continue;

            if (($profileS->getSlug()==='profile')&&(strtolower($profileS->getDescription4())==='first page')) continue;

            $arrayElem = $this->serializer->serialize($profileS);

            $arraySubsect = array();
                /** @var Section $subsect */
                foreach ($profileS->getSubsections() as $subsect){
                    if ($subsect->isDisabled()) continue;
                    if (in_array(strtolower($subsect->getName()), $this->forbiddenArray)) continue;

                    $subsectarray = $this->serializer->serialize($subsect);

                    if (($profileS->getSlug()==='press')){
                        $res = array_filter($subsect->getSubsections(),function($section){ if ($section->getSlug()=='gallery') return true;if ($section->getSlug()=='text') return true;return false;});
                        if (count($res)>0) {
                            $resSect = $res[0];
                            $presstype = 'gallery';
                            if ($resSect->getSlug()=='text') $presstype = 'text';
                            $subsectarray = array(
                                'name' => $subsect->getName(),
                                'slug' => $subsect->getSlug(),
                                'presstype' => $presstype
                            );

                        }
                    }

                    $arraySubsect[] = $subsectarray;
                }



            if (count($arraySubsect)>0) {
                //add for press to identify
                if ($profileS->getSlug()==='press') $arrayElem['subsectionspress'] = $arraySubsect; else
                $arrayElem['subsections'] = $arraySubsect;
            }
            $arrayProfile[] = $arrayElem;
            //echo $profileS->getName().' '.count($profileS->getSubsections()).' ';
        }

        //exit;
        return $arrayProfile;
    }

    public function getFirstPageProfileAllArray($name,$type,$main){
        return $this->getFirstPageProfileArray($name,array($type,$main));
    }

    public function getFirstPageProfileAllFDArray($name,$type){
        return $this->getFirstPageProfileArray($name,array($type));
    }

    public function getFirstPageProfileArray($name,$parentsArray){



        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlugWithParents($name,$parentsArray);



        if ($section == null) {
            return null;
        }

        //if ($section->getStatus()==0) return null;

        $arrayFirstPage = null;

        /** @var Section $subsect */
        foreach ($section->getSubsections() as $subsect){
            if ($subsect->getSlug()!=='profile') continue;

            if (strtolower($subsect->getDescription4())!=='first page') continue;



            $arrayFirstPage=['text' => '','image' => ''];
            /** @var Section $detail */
            foreach ($subsect->getSubsections() as $detail){
                if (($detail->getSlug()=='bio')){
                    $text = $detail->getFirstText();
                    if ($text!=null) $arrayFirstPage['text'] = $text->getBody();
                }

                if (($detail->getSlug()=='preview')){
                    $image = $detail->getFirstImage();
                    if ($image!==null){
                        $path ="http://".$detail->getBucket().".s3.amazonaws.com/".$detail->getPath();
                        $arrayFirstPage['image'] = $this->serializer->serialize($image);
                        $arrayFirstPage['image']['linkpath'] = $path.'/';
                    }
                }
            }

        }


        return $arrayFirstPage;
    }

    public function getMoreProfessionalsArray($name,$type,$main){
        $parents= array($main);
        $path = $main.'/'.$type;

        if ($main==null) {
            $parents = array();
            $path = $type;
        }

        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlugWithParents($type,$parents,true);

        $arrayMore = array();

        $arrayGetSubsections = $section->getSubsectionSortedByName();


        /** @var Section $moreProfile */
        foreach ($arrayGetSubsections as $moreProfile) {
            if ($moreProfile->isDisabled()) continue;
            if ($moreProfile->getSlug() === $name) continue;


            $arrayElem = array(
                'name' => $moreProfile->getName(),
                'slug' => $moreProfile->getSlug(),
                'path' => $path,
            );


            $arrayMore[] = $arrayElem;
        }

        return $arrayMore;
    }



    public function getContentPortfolioGallery($name,$type,$main,$portfolio,$gallery){
        $slug = $portfolio;
        $parentArray = array();
        if ($gallery!=null) {
            $slug = $gallery;
            $parentArray[] = $portfolio;
        }


        $parentArray[] = $name;
        if ($type!=null) $parentArray[] = $type;
        if ($main!=null) $parentArray[] = $main;



        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlugWithParents($slug,$parentArray,false,true);

        $path ="http://".$section->getBucket().".s3.amazonaws.com/".$section->getPath();
        $serial = $this->serializer;

        /**
         * @param $image Image
         * @return array
         */
        $filterf = function($image) use ($serial,$path,$section ){
            $sImage = $serial->serialize($image);
            $sImage['linkpath']=$path;

            return $sImage;
            //getimagesize('http://'.$section->getBucket().".s3.amazonaws.com/".$section->getPath()."/_thumbnails/".$image->getThumbname());



        };

        $array = array_map($filterf,array_values(array_filter($section->getImages(),function($image){ if ($image->getStatus() === 1) return true; return false;})));

        return $array;

    }

    public function getProfilePageArray($name,$type,$main){
        $parentArray= array($name);
        if ($type!=null) $parentArray[] = $type;
        if ($main!=null) $parentArray[] = $main;

        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlugWithParents("profile",$parentArray);
        if (strtolower($section->getDescription4()) === "first page") {
            return [];
        }


        $arrayProfile = array();

        $path ="http://".$section->getBucket().".s3.amazonaws.com/".$section->getPath();
        $serial = $this->serializer;

        /**
         * @param $image Image
         * @return array
         */
        $filterf = function($image) use ($serial,$path,$section ){
            $sImage = $serial->serialize($image);
            $sImage['linkpath']=$path;
            return $sImage;
        };

        $arrayProfile['images'] = array_map($filterf,array_values(array_filter($section->getImages(),function($image){if ($image->getStatus()==0) return false;return true;})));


        $section = $this->sectionMapper->findSectionBySlugWithParents("text",array_merge(array("profile"),$parentArray));
        /**
         * @param $text Text
         * @return mixed
         */
        $filterf = function($text) {
            $stext = array();
            $stext['body'] = $text->getBody();
            return $stext;
        };

        $arrayProfile['texts'] = array_map($filterf,array_values(array_filter($section->getTexts(),function($text){if ($text->getStatus()==0) return false;return true;})));

        //exit;
        return $arrayProfile;
    }





}