define([
    'jquery',
    'underscore',
    'backbone',
    'models/artists/portfolioContentCollection',
    'views/artists/mobile.gallery'
],function($,_,Backbone,PortfolioContentMain,GalleryViee){

    var PressPostGalleryView = GalleryViee.extend({
        className:'mainportfoliocontainer',
        id:"mainportfoliocontainer",
        initialize : function(options){
            this.options = options;
            this.template = Templates.getTemplate("template-artists-gallery");

            $(window).on('resize',this.resize);

            var portfoliomodel = new PortfolioContentMain({path:Backbone.history.getFragment().split('/').slice(0).join('/')});

            this.options.link = Backbone.history.getFragment().split('/').slice(0,1).join('/');


            var self = this;
            portfoliomodel.fetch({
                success : function(model){
                    self.model = model;
                    self.collection = model.get('content');
                    self.path= model.get('path');

                    self.render();

                }
            });
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



                    newElem.html($('<div id="innercontent'+imageid+'" class="innercontent videoplayer2" data-width="'+content.get('imageWidth')+'" data-height="'+content.get('imageHeight')+'" ></div>').html(videoElem));

                    newElem.data('height',content.get('imageHeight'));
                    newElem.data('width',content.get('imageWidth'));
                    newElem.data('content',contentLink);
                    newElem.data('id',content.get('id'));
                    newElem.data('type','video');


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
                    var imageElem = $('<img src="'+contentLink+'" data-width="'
                        +content.get('imageWidth1')+'" data-height="'+content.get('imageHeight1')+'" id="innercontent'+imageid+'" class="imageportfoliobig innercontent"/>');
                    newElem.html(imageElem);

                    newElem.data('content',contentLink);
                    newElem.data('id',content.get('id'));
                    newElem.data('type','image');

                    $('<img>').load(function(){ imageElem.animate({'opacity' : 1});

                    }).attr('src',imageElem.attr('src'));
                }





            });
        },



        close : function(){
            $(window).off('resize',this.resize);
            this.$el.remove();

        },

        show : function() {

        }
    })

    return PressPostGalleryView;

});

