define([
    'jquery',
    'underscore',
    'backbone',
    'js/main/utils'
],function($,_,Backbone){

    var ZoomViewerView = Backbone.View.extend({
        className:'zoomviewercontainer',
        id:"zoomviewercontainer",
        initialize : function(options){
            this.template = Templates.getTemplate("template-fooddrink-zoomviewer");

            $(window).on('resize',this.resize);
            app.layout.hideCredit();
            $('body,html').css('overflow','hidden');

            this.content = options.content;
            this.currentId = options.currentId;
            this.currentIndex = 0;

            for (var i=0;i<this.content.length;i++){
                if (this.content[i].id === this.currentId) {this.currentIndex=i;break;}
            }

            this.render();

        },
        events:{
            "click .zoomviewerarrowleft" : "prevImage",
            "click .zoomviewerarrowright" : "nextImage",
            "click .pressgalleryoverlay" :"close",
            "click .zoomviewerclosecontainer" : "close",
        },
        render : function(){

            var self = this;
            self.$el.html(self.template({}));


            if (self.content.length<=1) self.$('.zoomviewerarrow').remove();
            self.$el.fadeIn();
            self.showContent(self.currentIndex);
            return this.$el;
        },

        prevImage:function(){
            this.showContent(this.currentIndex-1);
        },
        nextImage:function(){
            this.showContent(this.currentIndex+1);
        },

        showContent:function(index){

            index = parseInt(index);
            var self=this;



            var isFirst = false;
            var isLast = false;
            if (index<0) index = self.content.length-1;
            if (index>=(self.content.length)) index=0;

            self.currentIndex = index;

            var content = self.content[index];

            var contentLink = self.content[index].content;
            var contentType = self.content[index].type;
            var imageid = self.content[index].id;



            var precElem = this.$('.zoomviewercontentcontainer').find('div.zoomviewercontentholder');
            precElem.fadeOut(function(){$(this).remove()});


            var newElem = $('<div class="zoomviewercontentholder"></div>');


            if (contentType=='video'){
                var videoElem = $('<video class="mejs-portfolioplayer" controls preload="none" data-width="'+content.width+'" data-height="'+content.height+'" src="'+contentLink+'" width="'
                    +content.width+'" height="'+content.height+'" id="innercontentvideo'+imageid+'" style="width: 100%; height: 100%;" ></video>');

                var coverlink = contentLink.substr(0, contentLink.lastIndexOf(".")) + ".jpg";
                console.log(coverlink);
                $('<img>').load(function(){
                    videoElem.attr('poster',coverlink);
                }).attr('src',coverlink);

                newElem.html($('<div id="innercontent'+imageid+'" class="innercontent videoplayer2" data-width="'+content.width+'" data-height="'+content.height+'" ></div>').html(videoElem));

                videoElem.mediaelementplayer({
                    loop:false,
                    startVolume: 0.3,
                    alwaysShowControls : true,
                    features: ['playpause','progress','current','separator','duration','zoomout'],
                    success: function(player, node){
                        self.resize();
                    }
                });

                newElem.data('height',content.height);
                newElem.data('width',content.width);
                newElem.data('content',contentLink);
                newElem.data('id',content.id);
                newElem.data('type',content.type);

                videoElem.data('callback',function(){ self.close()});


            } else

            {

                var imageElem = $('<img src="'+contentLink+'" data-width="'
                    +content.width+'" data-height="'+content.height+'" id="innercontent'+imageid+'" class="imagezoomviewerbig innercontent"/>');
                newElem.html(imageElem);

                newElem.data('content',contentLink);
                newElem.data('id',content.id);
                newElem.data('type',content.type);
                newElem.data('height',content.height);
                newElem.data('width',content.width);

                var overlayElem = $('<div class="pressgalleryoverlay"></div>');
                overlayElem.html('<div class="overlayzoom">zoom out</div>');
                newElem.append(overlayElem);

                overlayElem.click(function(event){
                    self.close();
                });

                $('<img>').load(function(){ imageElem.animate({'opacity' : 1});

                }).attr('src',imageElem.attr('src'));
            }


            this.$('.zoomviewercontentcontainer').append(newElem);
            this.resize();
        },
        resize : function(event){

            var container = this.$('.zoomviewercontentcontainer');
            var windowwidth = $(window).width();
            var windowheight = $(window).height();
            var width = windowwidth-200;
            var height = windowheight-40;

            $('.zoomviewercontainer').width(windowwidth).height(windowheight);
            this.$('.zoomviewercontentholder').each(function(index,elem){
                var element = $(this);

                var scaled = Utils.scaleToFit([width,height],[element.data('width'),element.data('height')]);

                element.css({
                    width:scaled.size.width+"px",
                    height:scaled.size.height+"px",
                    top:scaled.margins.top+"px",
                    left:scaled.margins.left+"px",
                });
            });
        },
        close : function(){
            $('body,html').css('overflow','auto');
            //app.layout.showCredit();
            $(window).off('resize',this.resize);
            var self = this;
            this.$el.fadeOut(function(){self.$el.remove();});
        },

        show : function() {
            this.$el.fadeIn();
        }
    })

    return ZoomViewerView;

});