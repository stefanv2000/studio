define([
    'jquery',
    'underscore',
    'backbone',
    'models/artists/portfolioContentCollection',
    'views/artists/gallery'
],function($,_,Backbone,PortfolioContentMain,GalleryViee){

    var PressPostGalleryView = GalleryViee.extend({
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
                    self.links = model.get('links');

                    Utils.addPrevNextPostArticleLinks(self.links);
                    self.render();


                }
            });
        },
        events:{
            "click .thumbnailimage" : "thumbnailClick",

        },

        populateContent:function(){
            var self=this;
            this.$('.maingalleryimagecontainer').each(function(index,elem){

                var content = self.collection.at(index);

                var contentLink = content.get('linkpath')+'/_images1/'+content.get('name');
                var contentType = content.get('contentType');
                var imageid = content.get('id');

                var newElem = $(this);

                newElem.addClass('pressgalleryimagecontainer');

                if (contentType=='video'){
                    contentLink = content.get('linkpath')+'/_media/'+content.get('name');
                    var videoElem = $('<video class="mejs-portfolioplayer" controls preload="none" data-width="'+content.get('imageWidth')+'" data-height="'+content.get('imageHeight')+'" src="'+contentLink+'" width="'
                        +content.get('imageWidth')+'" height="'+content.get('imageHeight')+'" id="innercontentvideo'+imageid+'" style="width: 100%; height: 100%;" ></video>');

                    //var coverlink = content.get('linkpath')+'/_media/'+content.get('originalName');
                    var coverlink = contentLink.substr(0, contentLink.lastIndexOf(".")) + ".jpg";



                    newElem.html($('<div id="innercontent'+imageid+'" class="innercontent videoplayer2" data-width="'+content.get('imageWidth')+'" data-height="'+content.get('imageHeight')+'" ></div>').html(videoElem));

                    videoElem.mediaelementplayer({
                        loop:false,
                        startVolume: 0.3,
                        alwaysShowControls : true,
                        enableAutosize: true,
                        features: ['playpause','progress','current','separator','duration','clicktozoom'],
                        success: function(player, node){
                            self.resize();
                        }
                    });

                    newElem.data('height',content.get('imageHeight'));
                    newElem.data('width',content.get('imageWidth'));
                    newElem.data('content',contentLink);
                    newElem.data('id',content.get('id'));
                    newElem.data('type','video');

                    $('<img>').load(function(){
                        //videoElem.attr('poster',coverlink);
                        var postercont = newElem.find('.mejs-poster');
                        var imgElem = $('<img src="'+coverlink+'"  class="posterimage"/>');
                        postercont.html(imgElem);
                        postercont.show();
                        imgElem.show();
                        self.resize();
                    }).attr('src',coverlink);

                    videoElem.data('callback',function(){self.openZoomViewer(newElem);});

                } else
                if (contentType == 'vimeo') {
                    var videoElem = $('<iframe data-width="'+content.get('imageWidth')+'" data-height="'+content.get('imageHeight')+'" class="innercontent" id="innercontent'+imageid+'" src="//player.vimeo.com/video/'+content.get('link')+'" width="100%" height="100%" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>');
                    newElem.html(videoElem);
                    newElem.data('height',content.get('imageHeight'));
                    newElem.data('width',content.get('imageWidth'));
                } else
                if (contentType == 'youtube') {
                    var videoElem = $(' <iframe data-width="'+content.get('imageWidth')+'" data-height="'+content.get('imageHeight')+'" class="innercontent" id="innercontent'+imageid+'" width="100%" height="100%" src="http://www.youtube.com/embed/'+content.get('link')+'"></iframe>');
                    newElem.html(videoElem);
                    newElem.data('height',content.get('imageHeight'));
                    newElem.data('width',content.get('imageWidth'));
                } else
                {
                    contentLink = content.get('linkpath')+'/_images1/'+content.get('originalName');
                    var imageElem = $('<img src="'+contentLink+'" data-width="'
                        +content.get('imageWidth1')+'" data-height="'+content.get('imageHeight1')+'" id="innercontent'+imageid+'" class="imageportfoliobig innercontent imagefooddrinkgallery"/>');
                    newElem.html(imageElem);

                    newElem.data('content',contentLink);
                    newElem.data('id',content.get('id'));
                    newElem.data('type','image');

                    var overlayElem = $('<div class="pressgalleryoverlay"></div>');
                    overlayElem.html('<div class="overlayzoom">click to zoom</div>');
                    newElem.append(overlayElem);

                    overlayElem.click(function(event){
                        self.openZoomViewer(newElem);
                    });

                    $('<img>').load(function(){ imageElem.animate({'opacity' : 1});

                    }).attr('src',imageElem.attr('src'));
                }





            });
        },

        openZoomViewer : function(element){
            var arrayContent = [];
            this.$('.maingalleryimagecontainer').each(function(index,elem){
                var elem = $(this);
                arrayContent[index] = {
                    'type' : elem.data('type'),
                    'width' : elem.data('width'),
                    'height' : elem.data('height'),
                    'id' : elem.data('id'),
                    'content' : elem.data('content'),
                }
            });

            var currElement = element.data('id');
            options = {
                'content' : arrayContent,
                'currentId' : currElement,
            }

            require(['views/fooddrink/zoomViewer'],function(ZoomViewerView){
                var zoomViewerView = new ZoomViewerView(options);
                $('body').append(zoomViewerView.$el);
                zoomViewerView.resize();
            });
        },

        resize : function(event){






            var self=this;
            var height = Utils.getWindowHeight()-$('#menucontainer').height()-$('.portfoliotop').height()-11;

            this.$('.portfoliomaingalleryscrollable,.portfoliomaingallerycontainer').height(height);
            height-=45;
            this.$('.portfoliomaingallerycontainer').height(height);

            height = Math.min(850,height);//maximum gallery height is 850px




            var widthtotal = 0;
            this.$('.maingalleryimagecontainer').each(function(index,elem){
                var elemCon = $(this);
                var containerHeight = height;

                var nHeight = elemCon.data('height');
                var nWidth = elemCon.data('width');

                    nHeight = containerHeight;
                    nWidth = containerHeight/elemCon.data('height')*elemCon.data('width');


                nHeight=Math.round(nHeight);
                nWidth=Math.round(nWidth);

                elemCon.width(nWidth).height(nHeight);

                widthtotal+=nWidth+12;
            });


            widthtotal+=23;



            this.$('.portfoliomaingallerycontainer').width(widthtotal);

            $('.portfoliomaingalleryscrollable').mCustomScrollbar('update');
        },

        close : function(){
            app.layout.showCredit();
            $(window).off('resize',this.resize);
            Utils.removePrevNextPostArticleLinks();
            this.$el.remove();

        },

        show : function() {

        }
    })

    return PressPostGalleryView;

});

