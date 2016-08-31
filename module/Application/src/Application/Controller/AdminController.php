<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Application\Controller;


use Application\Infrastructure\Cache\MenuContent;
use Application\Infrastructure\Cache\Minifier;
use Application\Infrastructure\Cache\SearchContent;
use Application\Infrastructure\Cleaning;
use Application\Infrastructure\DatabaseUpdate\DatabaseUpdate;
use Application\Infrastructure\Templates\Packer;
use Entities\Mapper\SectionMapper;
use Frontend\Infrastructure\Cache\CacheJson;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AdminController extends AbstractActionController{

    /**
     * @var DatabaseUpdate
     */
    private $databaseUpdate;

    /**
     * @var CacheJson
     */
    private $cacheJson;

    /**
     * @var SectionMapper
     */
    private $sectionMapper;

    /**
     * AdminController constructor.
     * @param $databaseUpdate
     */
    public function __construct($databaseUpdate,$sectionMapper,$cacheJson) {
        $this->databaseUpdate = $databaseUpdate;
        $this->cacheJson = $cacheJson;
        $this->sectionMapper = $sectionMapper;
    }


    public function updateDatabaseAction(){
        set_time_limit(0);
        $time_start = microtime(true);



        $this->databaseUpdate->create();
        $this->databaseUpdate->createKerning();

        echo "update database finished<br />\n";
        $time_end = microtime(true);
        $execution_time = $time_end - $time_start;
        $this->databaseUpdate->checksForShitCreatedByTheMoronInTheDB();

        echo "rename<br />\n";flush();

        $renamed = rename('data/ studio.db','data/ studioren.db');
        while ($renamed ===false){
            $renamed = rename('data/ studio.db','data/ studioren.db');
        }

        copy('data/ studiotemp.db','data/ studio.db');
        unlink('data/ studioren.db');
        $this->cacheJson->clearCache();

        $sc = new SearchContent($this->sectionMapper);
        $sc->createSearchContent('cache/js/searchcontent.js');

        $mc = new MenuContent($this->sectionMapper);
        $mc->create('cache/js/menucontent.js');
        echo $execution_time."<br />\n";
        Cleaning::clean();
        echo "done";
        exit;
        return new ViewModel();
    }


    public function cleaningAction(){
        set_time_limit(0);
        $time_start = microtime(true);


        Cleaning::clean();


        $time_end = microtime(true);
        $execution_time = $time_end - $time_start;

        echo $execution_time."<br />\n";
        echo "done";
        exit;
    }
}