<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Application\Infrastructure\DatabaseUpdate;


use Application\Infrastructure\Slug;
use Doctrine\ORM\EntityManager;
use Entities\Entity\Image;
use Entities\Entity\Section;
use Entities\Entity\Text;
use ErrorException;
use Exception;

class DatabaseUpdate {

    /**
     * @var EntityManager
     */
    private $em;
    private $pdoDB;
    private $pdoSqlite;
    private $user_id;

    /**
     * DatabaseUpdate constructor.
     * @param $entityManager EntityManager
     */
    public function __construct($entityManager,$user_id,$servername,$dbname,$username,$password) {
        $this->em = $entityManager;
        $this->user_id = $user_id;
        $this->pdoDB = new \PDO("mysql:host=$servername;dbname=$dbname",$username,$password);
        $this->pdoSqlite = new \PDO("sqlite:data/ studiotemp.db");
    }

    public function create(){
        $starttime = microtime(true);
        if ($this->tryLock()===false){
            echo "database locked<br/>\n";
            echo "error";
            exit;
        }

        $this->clear();
        echo "cleared ".(microtime(true)-$starttime)."<br />\n";
        flush();
        $starttime = microtime(true);
        $this->createFolders();
        echo "sections created ".(microtime(true)-$starttime)."<br />\n";
        flush();
        $starttime = microtime(true);
        $this->createImages();
        echo "images created ".(microtime(true)-$starttime)."<br />\n";
        flush();
        $starttime = microtime(true);
        $this->createTexts();
        echo "texts created ".(microtime(true)-$starttime)."<br />\n";
        flush();
        $this->releaseLock();
        $this->createSEOInfo();
        return true;
    }

    private function tryLock(){
        $query = $this->pdoSqlite->query("select * from lock where id=1");
        if ($query === false) return false;
        $row=$query->fetch();
        if ($row['locked']==0) {
            $this->pdoSqlite->exec("update lock set locked=1,datelocked=".time());
            return true;
        }
        $timelocked = time();
        $diff = $timelocked - $row['datelocked'];
        echo $diff;
        if ($timelocked - $row['datelocked']<60) return false;
        $this->pdoSqlite->exec("update lock set locked=1,datelocked=".time());
        return true;
    }

    private function releaseLock(){
        $ret = $this->pdoSqlite->exec("update lock set locked=0");
        if ($ret===false) echo "unlock failed<br />\n";
    }

    protected function clear(){
        $ret = $this->pdoSqlite->exec("delete from sections");
        if ($ret===false) echo "sections failed<br />\n";
        $ret = $this->pdoSqlite->exec("delete from images");
        if ($ret===false) echo "images failed<br />\n";
        $ret = $this->pdoSqlite->exec("delete from texts");
        if ($ret===false) echo "texts failed<br />\n";
        return;
        $q = $this->em->createQuery("delete from Entities\Entity\Section s");
        $q->execute();

        $q = $this->em->createQuery("delete from Entities\Entity\Image i");
        $q->execute();

        $q = $this->em->createQuery("delete from Entities\Entity\Text t");
        $q->execute();
        return true;
    }

    protected function createFolders(){
        $querystr = "insert into sections
                      (id,parent_id,path,name,position,bucket,description,description1,description2,description3,description4,
                      status,tags,text,link,slug)
                        values ";


        $queryreplace = "(%s,%s,'%s','%s',%s,'%s','%s','%s','%s','%s','%s',%s,'%s','%s','%s','%s')";

        $stringToInsert = "";

        $starttime = microtime(true);
        $query = $this->pdoDB->query("select * from dirs where user_id=".$this->user_id." order by dir asc");
        echo "query took ".(microtime(true)-$starttime)."<br />\n";
        if (!$query) return false;
        $i=1;
        $batchSize = 500;
        //$repo = $this->em->getRepository("Entities\Entity\Section");

        $rootid = -1;

        while ($row = $query->fetch()){
            if ("media_folder/ studio" === $row["dir"]) {
                $rootid = $row['id'];
                continue;
            }
            $section = new Section();
            $section->setId($row["id"]);
            //$section->setName($this->replaceChars($this->getNameFolder($row["dir"])));
            $section->setName($this->replaceChars($row["alias"]));
            $section->setSlug(Slug::slugify($section->getName()));
            $section->setPath($row["dir"]);
            $section->setBucket($row["bucket"]);
            $section->setLink($row["link"]);
            $section->setPosition($row["pos"]);
            $section->setDescription($row["description"]);
            $section->setDescription1($row["description1"]);
            $section->setDescription2($row["description2"]);
            $section->setDescription3($row["description3"]);
            $section->setDescription4($row["description4"]);
            $section->setStatus($row["status"]);
            $section->setTags($row["tags_text"]);
            $section->setText($row["text"]);

            /** @var Section $parent */
            //$parent = $repo->find($row["parent_folder_id"]);
            //$parent = $repo->findOneBy(array('path' => $this->getParentPath($row["dir"])));

            //$section->setParent($parent);

            $parentid = $row['parent_folder_id'];
            if (($row['parent_folder_id']===0)||($parentid===$rootid)) $parentid='null';
            $insertSectionString = sprintf($queryreplace,
                $this->escapeString($section->getId()),
                $parentid,
                $this->escapeString($section->getPath()),
                $this->escapeString($section->getName()),
                $this->escapeString($section->getPosition()),
                $this->escapeString($section->getBucket()),
                $this->escapeString($section->getDescription()),
                $this->escapeString($section->getDescription1()),
                $this->escapeString($section->getDescription2()),
                $this->escapeString($section->getDescription3()),
                $this->escapeString($section->getDescription4()),
                $this->escapeString($section->getStatus()),
                $this->escapeString($section->getTags()),
                $this->escapeString($section->getText()),
                $this->escapeString($section->getLink()),
                $this->escapeString($section->getSlug())
                );


            if ($stringToInsert!=="") $stringToInsert.=",";

            $stringToInsert.=$insertSectionString;


            //$this->em->persist($image);
            //$this->em->persist($section);
            //$this->em->flush();
            if (($i % $batchSize) === 0) {
                //$this->em->flush();
                //$this->em->clear();
                $res = $this->pdoSqlite->exec($querystr.$stringToInsert);
                if ($res===false) {
                    echo "error <br/>";
                    print_r($this->pdoSqlite->errorInfo());
                    //echo $querystr.$stringToInsert;
                }
                $stringToInsert="";

                flush();

            }


            /*
            $this->em->persist($section);
            $this->em->flush();
            if (($i % $batchSize) === 0) {
                //$this->em->flush();
                $this->em->clear();
            }
            */
            $i++;
            //if ($i>50) break;
        }
        $this->pdoSqlite->exec($querystr.$stringToInsert);
        //$this->em->flush();
        return true;
    }

    protected function createImages(){
        /*$preparedstat = $this->pdoSqlite->prepare("insert into images
                      (id,name,section_id,originalName,position,imageWidth,imageHeight,imageWidth1,imageHeight1,imageWidth2,imageHeight2,imageWidth3,imageHeight3,imageWidth4,imageHeight4,imageWidth5,imageHeight5,imageWidth6,imageHeight6
                          ,thumbWidth,thumbHeight,thumbWidth1,thumbHeight1,thumbWidth2,thumbHeight2,thumbWidth3,thumbHeight3,thumbWidth4,thumbHeight4,thumbWidth5,thumbHeight5,thumbWidth6,thumbHeight6
                          ,status,tags,text,caption1,caption2,caption3,caption4,contentType,link,thumbname)
                        values(:id,:name,:sectionid,:originalName,:position,:imageWidth,:imageHeight,:imageWidth1,:imageHeight1,:imageWidth2,:imageHeight2,:imageWidth3,:imageHeight3,:imageWidth4,:imageHeight4,:imageWidth5,:imageHeight5,:imageWidth6,:imageHeight6
                          ,:thumbWidth,:thumbHeight,:thumbWidth1,:thumbHeight1,:thumbWidth2,:thumbHeight2,:thumbWidth3,:thumbHeight3,:thumbWidth4,:thumbHeight4,:thumbWidth5,:thumbHeight5,:thumbWidth6,:thumbHeight6
                          ,:status,:tags,:text,:caption1,:caption2,:caption3,:caption4,:contentType,:link,:thumbname)");
*/

        $querystr = "insert into images
                      (id,name,section_id,originalName,position,imageWidth,imageHeight,imageWidth1,imageHeight1,imageWidth2,imageHeight2,imageWidth3,imageHeight3,imageWidth4,imageHeight4,imageWidth5,imageHeight5,imageWidth6,imageHeight6
                          ,thumbWidth,thumbHeight,thumbWidth1,thumbHeight1,thumbWidth2,thumbHeight2,thumbWidth3,thumbHeight3,thumbWidth4,thumbHeight4,thumbWidth5,thumbHeight5,thumbWidth6,thumbHeight6
                          ,status,tags,text,caption1,caption2,caption3,caption4,contentType,link,thumbname)
                        values";


        $queryreplace = "(%s,'%s',%s,'%s',%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s
        ,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,'%s','%s','%s','%s','%s','%s','%s','%s','%s')";

        $stringToInsert = "";



        $arrayVideoSharing = array();
        if (file_exists('data/jsonvideo.json')){
            $arrayVideoSharing = json_decode(file_get_contents('data/jsonvideo.json'),true);
        }

        $starttime = microtime(true);
        $query = $this->pdoDB->query("select * from images where user_id=".$this->user_id." order by id asc");


        if (!$query) return false;
        echo "query took ".(microtime(true)-$starttime)."<br />\n";
        $i=1;
        $batchSize = 500;
        $repo = $this->em->getRepository("Entities\Entity\Section");
        set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
            // error was suppressed with the @-operator
            if (0 === error_reporting()) {
                return false;
            }

            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        });




        while ($row = $query->fetch()){
            /** @var Section $section */
            $section = $repo->find($row["dir"]);
            if ($section ===null) continue;

            $image = new Image();
            $image->setId($row["id"]);
            $image->setName($row["orig"]);
            $image->setOriginalName($row["img"]);
            $image->setLink($row["link"]);
            $image->setPosition($row["pos"]);
            $image->setStatus($row["status"]);
            $image->setTags($row["tags"]);
            $image->setText($row["text"]);
            $image->setImageHeight($row["height"]);
            $image->setImageWidth($row["width"]);
            $image->setThumbHeight($row["th_height"]);
            $image->setThumbWidth($row["th_width"]);



            for ($j=1;$j<=6;$j++){
                $funcwidth = "setImageWidth".$j;
                $image->$funcwidth($row["img".$j."_width"]);
                $funcheight = "setImageHeight".$j;
                $image->$funcheight($row["img".$j."_height"]);

                $funcwidth = "setThumbWidth".$j;
                $image->$funcwidth($row["th".$j."_width"]);
                $funcheight = "setThumbHeight".$j;
                $image->$funcheight($row["th".$j."_height"]);
            }

            for ($j=1;$j<=4;$j++){
                $funcCaption = "setCaption".$j;
                $image->$funcCaption($row["caption".$j]);

            }


            $image->setContentType('image');
            $image->setThumbname($image->getOriginalName());
            if (($this->endsWith($image->getName(),"mov"))||($this->endsWith($image->getName(),"mp4"))||($this->endsWith($image->getName(),"flv"))||($this->endsWith($image->getName(),"avi"))){
                $image->setContentType('video');
                $image->setThumbname(preg_replace('/\\.[^.\\s]{3,4}$/', '.jpg', $image->getName()));
                try {
                    //$sizes = getimagesizefromstring(file_get_contents("http://" . $section->getBucket() . ".s3.amazonaws.com/" . urlencode($section->getPath()) . "/_thumbnails1/" . urlencode($image->getThumbname())));
                    //$image->setThumbHeight1($sizes[1]);
                    //$image->setThumbWidth1($sizes[0]);
                } catch (ErrorException $e){
                    echo 'error';
                }
            }

            if (($image->getLink()!=='')&&($image->getLink()!==null)){
                $key = $image->getLink();
                if (array_key_exists($image->getLink(),$arrayVideoSharing)&&(array_key_exists('width',$arrayVideoSharing[$key]))){
                    $image->setImageWidth($arrayVideoSharing[$key]['width']);
                    $image->setImageHeight($arrayVideoSharing[$key]['height']);
                    $image->setContentType($arrayVideoSharing[$key]['type']);
                } else if (array_key_exists($image->getLink(),$arrayVideoSharing)&&(array_key_exists('error',$arrayVideoSharing[$key]))){
                    //echo $arrayVideoSharing[$key]['error'];
                    if (strpos($arrayVideoSharing[$key]['error'],'vimeo')!==false){
                        echo 'Error: vimeo id '.$image->getLink().' doesn\'t exist. SKIPPED. in path: '.$section->getPath()." name: ".$image->getName()."<br>\n";
                    }
                    if (strpos($arrayVideoSharing[$key]['error'],'youtube')!==false){
                        echo 'Error: youtube id '.$image->getLink().' doesn\'t exist. SKIPPED. in path: '.$section->getPath()." name: ".$image->getName()."<br>\n";
                    }
                    continue;
                } else
                if (is_numeric ($image->getLink())){//vimeo
                    try {
                        $videolink = "https://vimeo.com/api/oembed.json?url=" . urlencode("https://vimeo.com/" . $image->getLink());
                        $res = json_decode(file_get_contents($videolink));
                        $image->setImageWidth($res->width);
                        $image->setImageHeight($res->height);
                        $image->setContentType('vimeo');

                        $arrayVideoSharing[$key] = array();
                        $arrayVideoSharing[$key]['width'] = $res->width;
                        $arrayVideoSharing[$key]['height'] = $res->height;
                        $arrayVideoSharing[$key]['type'] = 'vimeo';

                    } catch (Exception $e){
                        $arrayVideoSharing[$key] = array();
                        $arrayVideoSharing[$key]['error']= 'Error: vimeo id '.$image->getLink().' doesn\'t exist. SKIPPED. in path: '.$section->getPath()." name: ".$image->getName()."<br>\n";
                        echo $arrayVideoSharing[$key]['error'];
                        continue;
                    }

                } else
                    if ((strtolower($image->getLink()) !== "single")&&(strtolower($image->getLink()) !== "b"))
                    { //is youtube
                    try {

                        $videolink = "https://www.youtube.com/oembed?url=" . urlencode('http://youtube.com/watch?v=' . $image->getLink()) . "&format=json";
                        $res = json_decode(file_get_contents($videolink));
                        $image->setImageWidth($res->width);
                        $image->setImageHeight($res->height);
                        $image->setContentType('youtube');

                        $key = $key = $image->getLink();
                        $arrayVideoSharing[$key] = array();
                        $arrayVideoSharing[$key]['width'] = $res->width;
                        $arrayVideoSharing[$key]['height'] = $res->height;
                        $arrayVideoSharing[$key]['type'] = 'youtube';

                    } catch (Exception $e){
                        $arrayVideoSharing[$key] = array();
                        $arrayVideoSharing[$key]['error']='Error: youtube id '.$image->getLink().' doesn\'t exist. SKIPPED. in path: '.$section->getPath()." name: ".$image->getName()."<br>\n";
                        echo $arrayVideoSharing[$key]['error'];
                        continue;
                    }
                }

            }



            if ($image->getImageWidth()==0){
                echo 'Error: image width is 0 . SKIPPED. in path: '.$section->getPath()." name: ".$image->getName()."<br>\n";
                //continue;
            }

            if ($image->getImageHeight()==0){
                echo 'Error: image height is 0 . SKIPPED. in path: '.$section->getPath()." name: ".$image->getName()."<br>\n";
                //continue;
            }

            if ($image->getThumbWidth()==0){
                echo 'Error: thumbnail width is 0 . SKIPPED. in path: '.$section->getPath()." name: ".$image->getName()."<br>\n";
                //continue;
            }

            if ($image->getThumbHeight()==0){
                echo 'Error: thumbnail height is 0 . SKIPPED. in path: '.$section->getPath()." name: ".$image->getName()."<br>\n";
                //continue;
            }


            $image->setSection($section);



            $insertImagestring = sprintf($queryreplace,
                $this->escapeString($image->getId()),
                $this->escapeString($image->getName()),
                $this->escapeString($section->getId()),
                $this->escapeString($image->getOriginalName()),
                $this->escapeString($image->getPosition()),
                $this->escapeString($image->getImageWidth()),
                $this->escapeString($image->getImageHeight()),
                $this->escapeString($image->getImageWidth1()),
                $this->escapeString($image->getImageHeight1()),
                $this->escapeString($image->getImageWidth2()),
                $this->escapeString($image->getImageHeight2()),
                $this->escapeString($image->getImageWidth3()),
                $this->escapeString($image->getImageHeight3()),
                $this->escapeString($image->getImageWidth4()),
                $this->escapeString($image->getImageHeight4()),
                $this->escapeString($image->getImageWidth5()),
                $this->escapeString($image->getImageHeight5()),
                $this->escapeString($image->getImageWidth6()),
                $this->escapeString($image->getImageHeight6()),
                $this->escapeString($image->getThumbWidth()),
                $this->escapeString($image->getThumbHeight()),
                $this->escapeString($image->getThumbWidth1()),
                $this->escapeString($image->getThumbHeight1()),
                $this->escapeString($image->getThumbWidth2()),
                $this->escapeString($image->getThumbHeight2()),
                $this->escapeString($image->getThumbWidth3()),
                $this->escapeString($image->getThumbHeight3()),
                $this->escapeString($image->getThumbWidth4()),
                $this->escapeString($image->getThumbHeight4()),
                $this->escapeString($image->getThumbWidth5()),
                $this->escapeString($image->getThumbHeight5()),
                $this->escapeString($image->getThumbWidth6()),
                $this->escapeString($image->getThumbHeight6()),
                $this->escapeString($image->getStatus()),
                $this->escapeString($image->getTags()),
                $this->escapeString($image->getText()),
                $this->escapeString($image->getCaption1()),
                $this->escapeString($image->getCaption2()),
                $this->escapeString($image->getCaption3()),
                $this->escapeString($image->getCaption4()),
                $this->escapeString($image->getContentType()),
                $this->escapeString($image->getLink()),
                $this->escapeString($image->getThumbname())
            );

            if ($stringToInsert!=="") $stringToInsert.=",";

            $stringToInsert.=$insertImagestring;


            //$this->em->persist($image);
            //$this->em->persist($section);
            //$this->em->flush();
            if (($i % $batchSize) === 0) {
                //$this->em->flush();
                //$this->em->clear();
                $res = $this->pdoSqlite->exec($querystr.$stringToInsert);
                if ($res===false) {
                    echo "error <br/>";
                    print_r($this->pdoSqlite->errorInfo());
                    echo $stringToInsert;
                }
                $stringToInsert="";

                flush();

            }
            $i++;
            //if ($i>50) break;
        }
        $this->pdoSqlite->exec($querystr.$stringToInsert);
        file_put_contents('data/jsonvideo.json',json_encode($arrayVideoSharing));
        //$this->em->flush();
        return true;
    }

        private function endsWith($haystack, $needle) {
            // search forward starting from end minus needle length characters
            return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
        }

    protected function createTexts(){
        $starttime = microtime(true);
        $query = $this->pdoDB->query("select * from text_cells where user_id=".$this->user_id." order by id asc");
        echo "query took ".(microtime(true)-$starttime)."<br />\n";
        if (!$query) return false;
        $i=1;
        $batchSize = 200;
        $repo = $this->em->getRepository("Entities\Entity\Section");
        while ($row = $query->fetch()){


            $text = new Text();
            $text->setId($row["id"]);
            $text->setHeader($row["header"]);
            $body = str_replace(["&rdquo;","&ldquo;","&rsquo;","&lsquo;"],array("&quot;","&quot;","'","'"),$row["body"]);
            $text->setBody(htmlspecialchars_decode(html_entity_decode(html_entity_decode($body),ENT_QUOTES | ENT_HTML5)));
            $text->setTags($row["tags"]);
            $text->setPosition($row["pos"]);
            $text->setStatus($row["status"]);

            /** @var Section $section */
            $section = $repo->find($row["dir_id"]);

            if ($section ===null) continue;


            $text->setSection($section);

            $this->em->persist($text);
            //$this->em->persist($section);
            //$this->em->flush();
            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }
            $i++;
            //if ($i>50) break;
        }
        $this->em->flush();
        return true;
    }

    public function createKerning(){

        /** @var Section $section */
        $section = $this->em->getRepository('Entities\Entity\Section')->findOneBy(array(
            'slug' => 'kerning',
            'parent' => null
        ));
        $arrayKerning = array();
        foreach (explode('<br />',$section->getFirstText()->getBody()) as $kernText){
            $kernText = trim($kernText);
            $kernElements = explode(' ',$kernText);
            if (count($kernElements)<2) continue;
            if (strlen($kernElements[0])!==2) continue;
            $element = array(
                'firstletter' => $kernElements[0][0],
                'lastletter' => $kernElements[0][1],
                'size' => $kernElements[1]
            );
            $arrayKerning[]=$element;

        }

        //get text and convert it to array

        $strKern = '';

        foreach ($arrayKerning as $kern){
            $kernvalue = 0.01*floatval($kern['size']);
            $strKern.=".kern-letter-".$kern['firstletter']." + .kern-letter-".$kern['lastletter']." {".
                "margin-left:".$kernvalue."em;".
                "}\n";
        }


        file_put_contents('app/css/kern.css',$strKern);
    }


    /**
     * @param $path string
     * @return string
     */
    private function getNameFolder($path){
        $arraypath = explode("/",$path);
        return $arraypath[count($arraypath)-1];
    }

    /**
     * @param $path string
     * @return string
     */
    private function getParentPath($path){
        return join("/", array_slice(explode("/", $path), 0, -1));
    }

    /**
     * replaces [number] with their respective html char
     * @param $string
     * @return mixed     *
     */
    private function replaceChars($string){
        $retString = preg_replace('/\[([0-9]*)\]/',"&#$1;",$string);
        $retString = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $retString);
        //$retString = preg_replace_callback("/\[([0-9]*)\]/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $string);
        return $retString;
    }

    private function escapeString($string){
        return str_replace("'","''",$string);
    }


    public function checksForShitCreatedByTheMoronInTheDB(){
        //$query = "select id from sections as s where not exists (select 1 from sections as s1 where s1.id=s.parent_id)";
        $query = "update sections set parent_id=null where id in (select id from sections as s where not exists (select 1 from sections as s1 where s1.id=s.parent_id))";
        //$result = $this->pdoSqlite->query($query);
        $this->pdoSqlite->exec($query);
        //print_r($result->fetchAll());
    }

    public function createSEOInfo(){
        $query = $this->pdoDB->query("select * from seo where user_id=".$this->user_id);
        if (!$query) return false;
        $row = $query->fetch();
        $seoinfo = array(
            'title' => $row['title'],
            'description' => $row['description'],
            'keywords' => $row['keywords'],
            'analitycs' => $row['code']
        );
        file_put_contents("data/seoinfo.json",json_encode($seoinfo));

    }


}