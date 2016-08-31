define([
    'jquery',
    'underscore',
    'backbone',
    'models/artists/portfolioContentCollection',
    'views/artists/gallery'
],function($,_,Backbone,PortfolioContentMain,GalleryParentView){

    var GalleryView = GalleryParentView.extend({
        className:'mainportfoliocontainer',
        id:"mainportfoliocontainer",


        populateContent:function(){

            var self=this;

            self.$el.addClass('modelportfoliocontainer');

            this.$('.maingalleryimagecontainer').each(function(index,elem){
                var content = self.collection.at(index);

                var contentLink = content.get('linkpath')+'/_images1/'+content.get('name');
                var contentType = content.get('contentType');
                var imageid = content.get('id');

                var newElem = $(this);

                if (contentType=='video'){
                    contentLink = content.get('linkpath')+'/_media/'+content.get('name');
                    var videoElem = $('<video class="mejs-portfolioplayer" controls preload="none" data-width="'+content.get('imageWidth')+'" data-height="'+content.get('imageHeight')+'" src="'+contentLink+'" width="'
                        +content.get('imageWidth')+'" height="'+content.get('imageHeight')+'" id="innercontentvideo'+imageid+'" style="width: 100%; height: 100%;" ></video>');

                    var coverlink = content.get('linkpath')+'/_media/'+content.get('originalName');


                    newElem.html($('<div id="innercontent'+imageid+'" class="innercontent videoplayer1" data-width="'+content.get('imageWidth')+'" data-height="'+content.get('imageHeight')+'" ></div>').html(videoElem));
                    videoElem.mediaelementplayer({
                        loop:false,
                        startVolume: 0.3,
                        alwaysShowControls : true,
                        features: ['playpause','progress','current','separator','duration'],
                        success: function(player, node){
                            self.resize();
                        }
                    });

                    newElem.data('height',content.get('imageHeight'));
                    newElem.data('width',content.get('imageWidth'));

                    $('<img>').load(function(){
                        var postercont = newElem.find('.mejs-poster');
                        var imgElem = $('<img src="'+coverlink+'"  class="posterimage"/>');
                        postercont.html(imgElem);
                        postercont.show();
                        imgElem.show();
                        self.resize();
                    }).attr('src',coverlink);


                } else
                if (contentType == 'vimeo') {
                    var videoElem = $('<iframe data-width="'+content.get('imageWidth')+'" data-height="'+content.get('imageHeight')+'" class="innercontent" id="innercontent'+imageid+'" src="//player.vimeo.com/video/'+content.get('link')+'" width="'+content.get('imageWidth')+'" height="'+content.get('imageHeight')+'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>');
                    newElem.html(videoElem);
                    newElem.data('height',content.get('imageHeight'));
                    newElem.data('width',content.get('imageWidth'));
                } else
                if (contentType == 'youtube') {
                    var videoElem = $(' <iframe data-width="'+content.get('imageWidth')+'" data-height="'+content.get('imageHeight')+'" class="innercontent" id="innercontent'+imageid+'" width="'+content.get('imageWidth')+'" height="'+content.get('imageHeight')+'" src="http://www.youtube.com/embed/'+content.get('link')+'"></iframe>');
                    newElem.html(videoElem);
                    newElem.data('height',content.get('imageHeight'));
                    newElem.data('width',content.get('imageWidth'));
                } else
                {
                    contentLink = content.get('linkpath')+'/_images1/'+content.get('originalName')
                    var imageElem = $('<img src="'+contentLink+'" data-width="'
                        +content.get('imageWidth1')+'" data-height="'+content.get('imageHeight1')+'" id="innercontent'+imageid+'" class="imageportfoliobig innercontent"/>');
                    newElem.html(imageElem);

                    //$('<img>').load(function(){imageElem.animate({'opacity' : 1});
                    //}).attr('src',imageElem.attr('src'));
                }



            });



            self.modelinfo = self.model.get('modelinfo');

            if (self.modelinfo.interview!=''){//insert interview after the first image
                var elemInterview = $(' <div class="modelinterviewtextcontainer" ><div class="modelinterviewcontainerscrollable">'+
                    ' <div class="modelinterviewtextcontent">'+self.modelinfo.interview+'</div></div></div>');

                this.$(".portfoliomaingallerycontainer div:first-child").after(elemInterview);

                self.$('.modelinterviewcontainerscrollable').mCustomScrollbar({
                    theme: 'verticalarrows',
                    scrollInertia:1600,
                    scrollButtons: {enable: true,
                        scrollAmount:260,
                        scrollType:"stepped",
                    },
                    axis: 'y',
                    callbacks:{
                        whileScrolling: function(){

                            self.$('.mCS-verticalarrows .mCSB_scrollTools .mCSB_buttonUp').css('opacity',1);
                            self.$('.mCS-verticalarrows .mCSB_scrollTools .mCSB_buttonDown').css('opacity',1);

                            if (this.mcs.topPct==0){
                                self.$('.mCS-verticalarrows .mCSB_scrollTools .mCSB_buttonUp').css('opacity',0.2);
                            }

                            if (this.mcs.topPct==100){
                                self.$('.mCS-verticalarrows .mCSB_scrollTools .mCSB_buttonDown').css('opacity',0.2);
                            }
                        }
                    }

                });
                self.$('.mCS-verticalarrows .mCSB_scrollTools .mCSB_buttonUp').css('opacity',0.2);
            }

            var captionElem = $('<div class="modelcaption"></div>');

            if (self.modelinfo.instagramlink!='') {
                var instaElem = $('<a class="instaelem" href="'+self.modelinfo.instagramlink+'" target="_blank"><img src="/images/models_personal_insta.svg" class="modelinstagram"/></a>')
                captionElem.append(instaElem);
            }


            if (self.modelinfo.compcard!='') {
                var compcardElem = $('<a class="compcard" href="'+self.modelinfo.compcard+'">comp card</a>')
                captionElem.append(compcardElem);
            }

            if (self.modelinfo.info!='') {
                var mcaption = self.modelinfo.info.replaceAll(" | ",'</span><img src="/images/ studio_models_line.svg" class="modelscaptionline"><span class="modelcaptionelement">');
                mcaption = '<span class="modelcaptionelement">'+mcaption+'</span>';

                var compcardElem = $('<div class="modelinfocontainer" >'+mcaption+'</div>')
                captionElem.append(compcardElem);
            }
            //self.$('.portfoliomaingallerycontainer').append(captionElem);
            self.$el.append(captionElem);
        },

        resize : function(event){

            var self=this;
            var height = Utils.getWindowHeight()-$('#menucontainer').height()-$('.modelpagetop').height()-11;

            this.$('.portfoliomaingalleryscrollable,.portfoliomaingallerycontainer').height(height);
            height-=40;
            this.$('.portfoliomaingallerycontainer').height(height);

            height-=this.$('.modelcaption').outerHeight(true);

            var widthtotal = 0;
            this.$('.maingalleryimagecontainer').each(function(index,elem){
                var elemCon = $(this);
                var containerHeight = height;
                if (containerHeight>850) containerHeight=850;

                var nHeight = elemCon.data('height');
                var nWidth = elemCon.data('width');

                nHeight = containerHeight;
                nWidth = containerHeight/elemCon.data('height')*elemCon.data('width');


                nHeight=Math.round(nHeight);
                nWidth=Math.round(nWidth);
                elemCon.width(nWidth).height(nHeight);
                elemCon.find('img').width(nWidth).height(nHeight);

                widthtotal+=nWidth+12;
            });

            this.$('.modelinterviewtextcontainer').height(height-70);
            this.$('.modelcaption').css('top',Math.min(height,850)+'px');


            widthtotal+=23;

            if (this.$('.modelinterviewtextcontainer').length>0) widthtotal+=470+35+23;




            this.$('.portfoliomaingallerycontainer').width(widthtotal);

            $('.portfoliomaingalleryscrollable').mCustomScrollbar({'setWidth':widthtotal});
            $('.portfoliomaingalleryscrollable').mCustomScrollbar('update');
        },

    })

    return GalleryView;

});

