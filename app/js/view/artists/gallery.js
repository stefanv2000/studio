define([
    'jquery',
    'underscore',
    'backbone',
    'models/artists/portfolioContentCollection'
],function($,_,Backbone,PortfolioContentMain){

    var GalleryView = Backbone.View.extend({
        className:'mainportfoliocontainer',
        id:"mainportfoliocontainer",
        initialize : function(options){
            this.options = options;
            this.template = Templates.getTemplate("template-artists-gallery");
            NProgress.inc();

            $(window).on('resize',this.resize);

            app.layout.hideCredit();

            var portfoliomodel = new PortfolioContentMain({path:Backbone.history.getFragment().split('/').slice(0).join('/')});

            this.options.link = Backbone.history.getFragment().split('/').slice(0,1).join('/');


            var self = this;
            portfoliomodel.fetch({
                success : function(model){
                    NProgress.inc();
                    self.model = model;
                    self.collection = model.get('content');
                    self.path= model.get('path');
                    self.render();

                }
            });
        },
        events:{
            "click .thumbnailimage" : "thumbnailClick",


        },


        render : function(){
            var self = this;
            self.$el.html(self.template({
                'con' : self.collection.toJSON(),
            }));

            NProgress.inc();
            self.populateContent();
            NProgress.inc();

            backgroundview.clearBackground();
            self.$('.portfoliomaingalleryscrollable').mCustomScrollbar({
                axis:"x",

                theme:'my-theme',
                autoDraggerLength:true,
                setLeft:"0px",
                mouseWheel : {
                    enable:true
                },
                advanced:{
                    updateOnBrowserResize:true,
                    updateOnContentResize:true,
                    autoExpandHorizontalScroll:3
                }

            });

            self.showContent();
            self.resize();
            NProgress.done();
            self.$el.fadeIn(function(){
                self.resize();
            });

            self.resize();

            return this.$el;
        },

        populateContent:function(){
            var self=this;
            this.$('.maingalleryimagecontainer').each(function(index,elem){
                var content = self.collection.at(index);

                var contentLink = content.get('linkpath')+'/_images1/'+content.get('name');
                var contentType = content.get('contentType');
                var imageid = content.get('id');

                var newElem = $(this);

                var captionElem = null;
                if (content.get('caption1')!=''){
                    var captionElem = $('<div class="captioninnercontent" data-id="'+imageid+'">'+content.get('caption1')+'</div>');

                }

                if (contentType=='video'){
                    contentLink = content.get('linkpath')+'/_media/'+content.get('name');
                    var coverlink = content.get('linkpath')+'/_media/'+content.get('originalName');

                    var videoElem = $('<video posterx="'+coverlink+'" class="mejs-portfolioplayer" controls preload="none" data-width="'+content.get('imageWidth')+'" data-height="'+content.get('imageHeight')+'" src="'+contentLink+'" width="'
                        +content.get('imageWidth')+'" height="'+content.get('imageHeight')+'" id="innercontentvideo'+imageid+'" style="width: 100%; height: 100%;" >' +
                        //'<object width="'+content.get('imageWidth')+'" height="'+content.get('imageHeight')+'" type="application/x-shockwave-flash" data="/flashmediaelement.swf">'+
                        //'<param name="movie" value="/flashmediaelement.swf">'+
                        //'<param name="flashvars" value="controls=true&file='+contentLink+'">'+
                        //'</object>' +
                        '</video>');





                    newElem.html($('<div id="innercontent'+imageid+'" class="innercontent videoplayer1" data-width="'+content.get('imageWidth')+'" data-height="'+content.get('imageHeight')+'" ></div>').html(videoElem));



                    videoElem.mediaelementplayer({
                        loop:false,
                        startVolume: 0.3,
                        alwaysShowControls : true,
                        enableAutosize: true,
                        //flashName: '/flashmediaelement.swf',
                        //enablePluginDebug: true,
                        features: ['playpause','progress','current','separator','duration'],
                        success: function(player, node){
                            self.resize();
                        }
                    });

                    newElem.data('height',content.get('imageHeight'));
                    newElem.data('width',content.get('imageWidth'));

                    $('<img>').load(function(){
                        //videoElem.attr('poster',coverlink);
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
                    contentLink = content.get('linkpath')+'/_images1/'+content.get('originalName');
                    var imageElem = $('<img src="'+contentLink+'" data-width="'
                        +content.get('imageWidth1')+'" data-height="'+content.get('imageHeight1')+'" id="innercontent'+imageid+'" class="imageportfoliobig innercontent"/>');
                    newElem.html(imageElem);

                    //$('<img>').load(function(){

                        //imageElem.animate({'opacity' : 1});
                        //if (captionElem!=null) captionElem.css('visibility','visible');
                    //}).attr('src',imageElem.attr('src'));
                    //if (captionElem!=null) captionElem.css('visibility','hidden');
                }


                if (captionElem!=null) newElem.append(captionElem);



            });
        },

        showContent:function(){
            this.imagesArray = this.$('img.imageportfoliobig');
            this.showFadeContent(0);
        },

        showFadeContent:function(index){
            var self=this;
            if (index>= self.imagesArray.length) return;
            var imageElem = $(self.imagesArray[index]);
            $('<img>').load(function(){
                imageElem.animate({'opacity' : 1});
                self.showFadeContent(index+1);
                //if (captionElem!=null) captionElem.css('visibility','visible');
            }).error(function(){
                self.showFadeContent(index+1);
            }).attr('src',imageElem.attr('src'));
        },

        resize : function(event){






            var self=this;
            var height = Utils.getWindowHeight()-$('#menucontainer').height()-$('.portfoliotop').height()-11;

            this.$('.portfoliomaingalleryscrollable,.portfoliomaingallerycontainer').height(height);
            height-=45;
            this.$('.portfoliomaingallerycontainer').height(height);

            height = Math.min(850,height);//maximum gallery height is 850px


            var maxHeightCaptions = 0;var cond=true;

            while (cond) {
                var maxHeight = 0;
                $('.captioninnercontent').each(function (index, elem) {
                    var captionheight = $(this).outerHeight(true);
                    maxHeight = Math.max(captionheight, maxHeight);
                });

                var newHeight = height - maxHeight;
                $('.captioninnercontent').each(function (index, elem) {
                    var id = $(this).data('id');
                    var elemInner = self.$('#innercontent' + id);

                    nWidth = newHeight / elemInner.data('height') * elemInner.data('width');

                    $(this).width(nWidth);

                });

                if (maxHeight>maxHeightCaptions) {
                    maxHeightCaptions = maxHeight;
                } else cond=false;
            }

            var height=height-maxHeightCaptions;



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

                elemCon.width(nWidth).height(nHeight+maxHeightCaptions);
                elemCon.children().not('div.captioninnercontent').width(nWidth).height(nHeight);

                widthtotal+=nWidth+12;
            });


            widthtotal+=23;



            this.$('.portfoliomaingallerycontainer').width(widthtotal);

            //$('.portfoliomaingalleryscrollable').mCustomScrollbar({'setWidth':widthtotal});
            $('.portfoliomaingalleryscrollable').mCustomScrollbar('update');
        },

        close : function(){
            app.layout.showCredit();
            $(window).off('resize',this.resize);
            this.$el.remove();

        },

        show : function() {

        }
    })

    return GalleryView;

});

