define([
    'jquery',
    'underscore',
    'backbone',

],function($,_,Backbone){

    var BackgroundView = Backbone.View.extend({
        className:'backgroundcontainer',
        id:"backgroundcontainer",
        timer:null,
        resTimeout:null,
        showElement:function(elemView){
            this.$('.backgroundoverlay').css({'visibility':'visible',opacity:0}).animate({'opacity':'0.8'},500);

            elemView.show();
        },
        reinit:function(contentArray,elemView,showIntro){

            var self= this;
            self.$('.backgroundoverlay').css({'visibility':'hidden',opacity:0});
            if (!showIntro) {
                this.showElement(elemView);
            }

            var mediacontainer = self.$('.backgroundmediacontainer');
            mediacontainer.html('');

            if (contentArray.length == 0 ) { this.clearBackground();return;}
            $('.ihousecontainer').css('opacity','1');

            var indexElem = Math.floor(Math.random() * (contentArray.length));
            var backsrc = contentArray[indexElem]['link'];

            if (backsrc.endsWith('mp4')||backsrc.endsWith('flv')||backsrc.endsWith('avi')||backsrc.endsWith('mov')){

                var videoElem = $('<video class="mejs-backgroundplayer" controls="none" preload="none" autoplay loop="loop" src="'+backsrc+'" width="'
                    +contentArray[indexElem]['width']+'" height="'+contentArray[indexElem]['height']+'" id="videoplayer" style="width:100%;height:100%;" ></video>');
                var mediaWrap = $('<div class="backgroundplayerwrap" data-width="'+contentArray[indexElem]['width']+'" data-height="'+contentArray[indexElem]['height']+'" ></div>')


                mediaWrap.append(videoElem);
                mediacontainer.html(mediaWrap);
                var media = videoElem.mediaelementplayer({
                    features: [],
                    startVolume: 0.5,
                    videoWidth:'100%',
                    videoHeight:'100%',

                    success: function(player, node){

                        player.addEventListener('ended', function(e){
                            player.play();
                        });

                        if (!showIntro) {
                            player.setVolume(0);
                            return;
                        }
                        var that=self;

                            player.addEventListener("click", function (e) {

                                if (e.stopPropagation){
                                    e.stopPropagation();
                                }
                                if (that.timer!=null) clearTimeout(that.timer);
                                player.setVolume(0);
                                that.showElement(elemView);
                                player.play();
                            },false);
                        that.timer = setTimeout(function(){
                            player.setVolume(0);
                            self.showElement(elemView);
                        },5000);
                    }
                });


                self.resizeView();


                return;
            }
            mediacontainer.html('<img src="'+backsrc+'" data-width="'+contentArray[indexElem]['width']+'" data-height="'+contentArray[indexElem]['height']+'" style="display: none">');

            $('<img>').load(function(){
                self.resizeView();
                mediacontainer.find('img').fadeIn();
                if (!showIntro) return;
                self.timer = setTimeout(function(){
                    self.showElement(elemView);
                },3000);
            }).attr('src',backsrc);
            if (!showIntro) {
                return;
            }
            mediacontainer.click(function(e){

                e.preventDefault();
                if (self.timer!=null) clearTimeout(self.timer);
                self.showElement(elemView);
            });
        },
        initialize : function(){
            $(window).on("resize",{that:this}, this.resizeevent);

            this.render();
        },
        resizeevent : function(event){
            var that = event.data.that;
            that.resizeView();return;
            if (that.resTimeout!= null ) clearTimeout(that.resTimeout);
            that.resTimeout = setTimeout(function(){that.resizeView();},80);
        },
        resizeView : function(){
            var container = this.$('.backgroundmediacontainer');
            container.find('img,div.backgroundplayerwrap').each(function(index,elem){
                var imageElem = $(this);
                var containerSize = [imageElem.parent().width(),imageElem.parent().height()];
                var imageSize = [imageElem.data('width'),imageElem.data('height')];



                var result = fillImageToContainer(imageSize,containerSize);


                imageElem.width(result['width']).height(result['height']).css({
                    'margin-top' : result['top']+'px',
                    'margin-left' : result['left']+'px'
                });
            });


        },
        clearBackground : function(){
            $('.ihousecontainer').css('opacity','0.6');
            this.$('.backgroundmediacontainer').html('');
        },
        events:{
        },
        render : function(){
            var templateB = Templates.getTemplate("template-background");
            this.$el.html(templateB({}));
            return this.$el;
        },
        close : function(){
            this.$el.remove();
        },

        show : function() {

        }
    })

    return BackgroundView;

});