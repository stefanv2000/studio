define([
    'jquery',
    'underscore',
    'backbone',
    'models/artists/portfolioContentCollection',
    'views/artists/mobile.gallery'
],function($,_,Backbone,PortfolioContentMain,GalleryParentView){

    var GalleryView = GalleryParentView.extend({
        className:'mainportfoliocontainer',
        id:"mainportfoliocontainer",


        populateContent:function(){

            var self=this;

            self.modelinfo = self.model.get('modelinfo');

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
                    $('<img>').load(function(){
                        videoElem.attr('poster',coverlink);
                    }).attr('src',coverlink);

                    newElem.html($('<div id="innercontent'+imageid+'" class="innercontent videoplayer1" data-width="'+content.get('imageWidth')+'" data-height="'+content.get('imageHeight')+'" ></div>').html(videoElem));


                    newElem.data('height',content.get('imageHeight'));
                    newElem.data('width',content.get('imageWidth'));


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

                    $('<img>').load(function(){ imageElem.animate({'opacity' : 1});
                    }).attr('src',imageElem.attr('src'));
                }


                var captionElem = $('<div class="modelcaption"></div>');


                if (self.modelinfo.info!='') {
                    var contenttext = '<span>'+self.modelinfo.info.split('|').join('</span> | <span>')+'</span>';


                    var compcardElem = $('<div class="modelinfocontainer" >'+contenttext+'</div>')
                    captionElem.append(compcardElem);
                }

                if ((self.modelinfo.compcard!='')&&(index==0)) {
                    var compcardElem = $('<a class="compcard" href="'+self.modelinfo.compcard+'">comp card</a>')
                    captionElem.append(compcardElem);
                }

                newElem.append(captionElem);

                if (index==0){
                    self.modelinfo = self.model.get('modelinfo');

                    if (self.modelinfo.interview!=''){//insert interview after the first image
                        var elemInterview = $(' <div class="modelinterviewtextcontainer" ><div class="modelinterviewcontainerscrollable">'+
                            ' <div class="modelinterviewtextcontent">'+self.modelinfo.interview+'</div></div></div>');

                        newElem.after(elemInterview);

                    }
                }
            });





        },


    })

    return GalleryView;

});

