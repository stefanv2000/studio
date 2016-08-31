define([
    'jquery',
    'underscore',
    'backbone',
    'models/artists/portfolioContentCollection',
],function($,_,Backbone,PortfolioContentMain){

    var OldPortfolioView = Backbone.View.extend({
        className:'mainportfoliocontainer',
        id:"mainportfoliocontainer",
        imageIndex : 0,
        scrolling:false,
        direction:null,
        initialize : function(options){
            this.options = options;

            this.template = Templates.getTemplate("template-artists-oldportfolio");
            NProgress.inc();

            $(window).on('resize',this.resize);

            var portfoliomodel = new PortfolioContentMain({path:Backbone.history.getFragment().split('/').slice(0).join('/')});

            this.options.link = Backbone.history.getFragment().split('/').slice(0,1).join('/');

            if (options.index != null) this.imageIndex=parseInt(options.index); else this.imageIndex=0;

            var self = this;
            portfoliomodel.fetch({
                success : function(model){
                    NProgress.inc();
                    self.collection = model.get('content');
                    self.path= model.get('path');
                    self.render();

                }
            });
        },
        events:{
            "mouseenter .thumbnailimage" : "mouseenterthumb",
            "mouseleave .thumbnailimage" : "mouseleavethumb",
            "mouseleave .portfoliothumbnailscontainerscrollable" : "mouseLeaveThumbnailsContainer",
            "click .thumbnailimage" : "thumbnailClick",
            "click .imagecontrols" : "prevNextConbtent",
        },
        render : function(){
            var self = this;
            self.$el.html(self.template({
                'con' : self.collection.toJSON(),
            }));
            NProgress.inc();
            self.$('.portfoliothumbnailscontainerscrollable').scrollLeft(0);

            if ((self.imageIndex>=0)&&(self.imageIndex<self.collection.length))
                self.showContentByIndex(self.imageIndex);
            self.resize();

            backgroundview.clearBackground();
            self.$el.fadeIn(function(){
                self.$('.portfoliothumbnailscontainerscrollable').scrollLeft(0);
            });
            self.showFadeThumbnails();
            NProgress.done();
            return this.$el;
        },

        resize : function(event){
            var self=this;
            var widthtotal = 0;
            this.$('.thumbnailcontainer').each(function(index,elem){
                widthtotal+=$(this).data('width')+1;
            });
            widthtotal+=70;

            this.$('.portfoliothumbnailscontainer').width(widthtotal);

            var height = Utils.getWindowHeight()-$('#menucontainer').height()-$('.portfoliotop').height()-52-10-28;
            this.$('.portfolioimagedisplaycontainer,.portfolioimagecontainer').height(height);

            this.$('.portfoliocontentcontent').each(function(index,elem){
                var currentElem = $(elem);
                var containerHeight = height;
                var elemCaption = currentElem.find('.captioninnercontent');

                if (elemCaption.length>0) containerHeight=containerHeight-19-14;

                var elemCon = currentElem.find('.innercontent');

                var nHeight = elemCon.data('height');
                var nWidth = elemCon.data('width');
                if (nHeight > containerHeight){
                    nHeight = containerHeight;
                    nWidth = containerHeight/elemCon.data('height')*elemCon.data('width');
                }



                elemCon.width(nWidth).height(nHeight);
                elemCaption.width(nWidth);
            });

        },

        mouseenterthumb : function(event){
            var element = $(event.currentTarget);
            element.addClass('portfoliothumbnailover');
        },

        mouseleavethumb : function(event){

            var element = $(event.currentTarget);
            element.removeClass('portfoliothumbnailover');
        },

        mouseLeaveThumbnailsContainer : function(event){
            this.scrolling = false;
        },

        showFadeThumbnails:function(){
            var self = this;
            this.$('.thumbnailimage').each(function(index,elem){
                var that = $(this);
                $('<img>').load(function(){
                    that.fadeIn(function(){that.parent().addClass('thumbnailcontainerbackground');});

                }).attr('src',that.attr('src'));
            });

            this.$('.portfoliothumbnailscontainerscrollable').mousemove(function(e){

                var parentOffset = $(this).parent().offset();
                var relX = e.pageX - parentOffset.left;

                if (relX < 200){
                    self.direction = "left";
                    if (self.scrolling == false){
                        self.scrolling = true;
                        self.scrollThumbanils();
                    }
                } else
                if ($(this).parent().width()-relX<200){
                    self.direction = "right";
                    if (self.scrolling == false){
                        self.scrolling = true;
                        self.scrollThumbanils();
                    }
                } else {
                    self.scrolling = false;
                }

            });


        },

        prevNextConbtent : function(event){
          var id = $(event.currentTarget).attr('id');
            if (id=='previousimagecontrol') this.showContentByIndex(this.imageIndex-1);
            if (id=='nextimagecontrol') this.showContentByIndex(this.imageIndex+1);
        },
        thumbnailClick : function(event){
            var index = parseInt($(event.currentTarget).data('index'))
            this.showContentByIndex(index);
        },
        showContentByIndex:function(index){

            index = parseInt(index);
            var self=this;
            self.imageIndex = index;
            var isFirst = false;
            var isLast = false;
            if (index==0) { isFirst=true}
            if (index==(self.collection.length-1)) {isLast = true}



            this.$('.imagecontrolsdisabled').hide();
            if (isFirst) this.$('#previousimagecontroldisabled').show();
            if (isLast) this.$('#nextimagecontroldisabled').show();

            self.$('.thumbnailimage').removeClass('portfoliothumbnailselected');
            self.$('#thumbnailimage'+index).addClass('portfoliothumbnailselected');

            var routeL = '/'+self.imageIndex;
            if (self.imageIndex==0) routeL="";

            router.navigate(self.path+routeL,{trigger:false});

            var content = self.collection.at(index);
            var contentLink = content.get('linkpath')+'/_images1/'+content.get('name');
            var contentType = content.get('contentType');

            //if (content.name)
            var precElem = this.$('.portfolioimagecontainer').find('div.portfoliocontentcontent');
            precElem.fadeOut(function(){$(this).remove()});


            var newElem = $('<div class="portfoliocontentcontent"></div>');

            if (contentType=='video'){
                contentLink = content.get('linkpath')+'/_media/'+content.get('name');

                var videoElem = $('<video class="mejs-portfolioplayer" controls preload="none" data-width="'+content.get('imageWidth')+'" data-height="'+content.get('imageHeight')+'" src="'+contentLink+'" width="'
                    +content.get('imageWidth')+'" height="'+content.get('imageHeight')+'" id="innercontentvideo'+self.imageIndex+'" style="width: 100%; height: 100%;" ></video>');

                var coverlink = content.get('linkpath')+'/_media/'+content.get('originalName');

                newElem.html($('<div id="innercontent'+self.imageIndex+'" class="innercontent videoplayer1" data-width="'+content.get('imageWidth')+'" data-height="'+content.get('imageHeight')+'" ></div>').html(videoElem));
                videoElem.mediaelementplayer({
                    loop:false,
                    startVolume: 0.3,
                    alwaysShowControls : true,
                    features: ['playpause','progress','current','separator','duration'],
                    success: function(player, node){
                        self.resize();
                    }
                });

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
                var videoElem = $('<iframe data-width="'+content.get('imageWidth')+'" data-height="'+content.get('imageHeight')+'" class="innercontent" id="innercontent'+self.imageIndex+'" src="//player.vimeo.com/video/'+content.get('link')+'" width="'+content.get('imageWidth')+'" height="'+content.get('imageHeight')+'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>');
                newElem.html(videoElem);
            } else
            if (contentType == 'youtube') {
                    var videoElem = $(' <iframe data-width="'+content.get('imageWidth')+'" data-height="'+content.get('imageHeight')+'" class="innercontent" id="innercontent'+self.imageIndex+'" width="'+content.get('imageWidth')+'" height="'+content.get('imageHeight')+'" src="http://www.youtube.com/embed/'+content.get('link')+'"></iframe>');
                    newElem.html(videoElem);
            } else
            {
                var imageElem = $('<img src="'+contentLink+'" data-width="'
                    +content.get('imageWidth1')+'" data-height="'+content.get('imageHeight1')+'" id="innercontent'+self.imageIndex+'" class="imageportfoliobig innercontent"/>');
                newElem.html(imageElem);

                $('<img>').load(function(){
                    imageElem.animate({'opacity' : 1});
                    //self.resize();

                    self.preloadImage(index,2);
                }).attr('src',imageElem.attr('src'));
            }

            if (content.get('caption1')!=''){
                var captionElem = $('<div class="captioninnercontent">'+content.get('caption1')+'</div>');
                newElem.append(captionElem);
            }

            this.$('.portfolioimagecontainer').append(newElem);
            this.resize();

        },

        scrollThumbanils:function(){

            var self = this;

                var amount = (self.direction === "left" ? "-=15px" : "+=15px");

                this.$('.portfoliothumbnailscontainerscrollable').animate({
                    scrollLeft: amount
                }, 5, function() {
                    if (self.scrolling) {
                        self.scrollThumbanils();
                    }
                });

        },

        preloadImage:function(index,number){
            var self = this;
            index = parseInt(index);
            number = parseInt(number);
            for (var i=index+1;i<=index+number;i++){
                if ((i>=0)&&(i<this.collection.length)){
                    var content = self.collection.at(i);
                    if (content.get('contentType')!='image') continue;
                    var contentLink = content.get('linkpath')+'/_images1/'+content.get('name');
                    $('<img>').load(function(){}).attr('src',contentLink);
                }
            }
            for (var i=index-1;i>=index-number;i--){

                if ((i>=0)&&(i<this.collection.length)){
                    var content = self.collection.at(i);
                    if (content.get('contentType')!='image') continue;
                    var contentLink = content.get('linkpath')+'/_images1/'+content.get('name');
                    $('<img>').load(function(){}).attr('src',contentLink);
                }
            }
        },
        close : function(){
            $(window).off('resize',this.resize);
            this.$el.remove();

        },

        show : function() {

        }
    })

    return OldPortfolioView;

});

