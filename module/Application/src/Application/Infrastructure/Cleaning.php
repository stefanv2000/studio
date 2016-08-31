<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Application\Infrastructure;


use Application\Infrastructure\Cache\Minifier;
use Application\Infrastructure\Templates\Packer;

class Cleaning {

    public static function clean(){
        $packer = new Packer();
        $packer->createCachedFile("desktoptemplates.js",'app/templates/main/');
        $packer->createCachedFile("mobiletemplates.js",'app/templates/mobile/');




        $listFiles= [
            'app/js/vendor/nprogress.js'
            ,'app/js/vendor/require.js'
            ,'app/js/vendor/underscore.js'
            ,'app/js/vendor/jquery.js'
            ,'app/js/main/utils.js'
            ,'app/js/main/social.js'
            ,'app/js/vendor/jquery.kern.min.js'
            ,'app/js/vendor/jquery.mCustomScrollbar.min.js'
            ,'app/js/vendor/jquery.mousewheel-3.0.6.min.js'
            ,'app/js/vendor/dropit.js'

            ,'app/js/main/templates.js'
            //,'app/js/vendor/mediaelement/mediaelement-and-player.min.js'
            //,'app/js/mediaelement/custom.js'
            ,'app/js/main/main.js'
            ,'cache/templates/desktoptemplates.js'
            ,'cache/js/searchcontent.js'
            ,'cache/js/menucontent.js'
        ];
        Minifier::minifyJS($listFiles,'cache/js/desktopjs.js');
        //unlink('cache/js/searchcontent.js');
        //unlink('cache/js/menucontent.js');



        $listFiles= ['app/js/vendor/require.js'
            ,'app/js/vendor/underscore.js'
            ,'app/js/vendor/jquery.js'
            ,'app/js/main/social.js'
            ,'app/js/main/utils.js'
            ,'app/js/main/templates.js'
            ,'app/js/main/mobile.main.js'
            ,'cache/templates/mobiletemplates.js'
        ];
        Minifier::minifyJS($listFiles,'cache/js/mobilejs.js');


        $listFiles= ['app/css/dropit.css'
            ,'app/css/jquery.mCustomScrollbar.css'
            //,'app/js/vendor/mediaelement/mediaelementplayer.min.css'
            ,'app/css/reset.css'
            ,'app/css/style.css'
            ,'app/css/videoplayer1.css'
            ,'app/css/videoplayer2.css'

        ];
        //Minifier::minifyCSS($listFiles,'app/css/styledesk.css');


        $listFiles= ['app/css/dropit.css'
            ,'app/css/jquery.mCustomScrollbar.css'
            ,'app/css/reset.css'
            ,'app/css/style.css'
            ,'app/css/videoplayer1.css'
            ,'app/css/videoplayer2.css'
            ,'app/css/nprogress.css'
        ];
        Minifier::minifyCSS($listFiles,'app/css/styledesk.css');


        $listFiles= [
            'app/css/reset.css'
            ,'app/css/mobile.style.css'
        ];
        Minifier::minifyCSS($listFiles,'app/css/stylemobile.css');
    }
}