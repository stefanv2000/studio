<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Frontend\Infrastructure\Cache;


use DirectoryIterator;

class CacheJson {

    /**
     * @var array
     */
    private $configCache;

    /**
     * CacheJson constructor.
     * @param array $configCache
     */
    public function __construct(array $configCache) {
        $this->configCache = $configCache;
    }

    public function isCached($arrayRequest){
        if ($this->configCache['shouldcache'])
            return file_exists($this->getFilePath($arrayRequest));
        return false;
    }

    public function getCachedContent($arrayRequest){
        if ($this->isCached($arrayRequest)) return file_get_contents($this->getFilePath($arrayRequest));
        return false;
    }

    public function getCachedContentAsArray($arrayRequest){
        $cacheContent = $this->getCachedContent($arrayRequest);
        if ($cacheContent!==false) return json_decode($cacheContent,true);
        return false;
    }

    public function cacheContent($arrayRequest,$arrayContent){
        $this->cacheContentString($arrayRequest,json_encode($arrayContent));
    }

    public function cacheContentString($arrayRequest,$stringContent){
        if ($this->configCache['shouldcache']) file_put_contents($this->getFilePath($arrayRequest),$stringContent);
    }

    public function clearCache(){
        foreach (new DirectoryIterator($this->configCache['folder']) as $fileInfo) {
            if(!$fileInfo->isDot()) {
                unlink($fileInfo->getPathname());
            }
        }
    }

    private function getFilePath($arrayRequest){
        $filepath = '';
        foreach ($arrayRequest as $elem){
            $filepath.='_'.$elem;
        }
        if ($this->configCache['displayname']) $filepath=md5($filepath);
        return $this->configCache['folder'].$filepath.'.json';
    }


}