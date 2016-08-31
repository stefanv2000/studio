define([
    'jquery',
    'underscore',
    'backbone',
    'models/artists/portfolioContentCollection',
    'views/artists/gallery'
],function($,_,Backbone,PortfolioContentMain){

    var PressPostTextView = Backbone.View.extend({
        className:'mainpresstextcontainer',
        id:"mainpresstextcontainer",
        initialize : function(options){
            this.options = options;
            this.template = Templates.getTemplate("template-fooddrink-posttext");

            $(window).on('resize',this.resize);
            app.layout.hideCredit();
            //var portfoliomodel = new PortfolioContentMain({path:Backbone.history.getFragment().split('/').slice(0).join('/')});

            this.options.link = Backbone.history.getFragment().split('/').slice(0,1).join('/');


            var self = this;
            $.get('/api/'+Backbone.history.getFragment().split('/').slice(0).join('/'),
                {},
                function(model){
                    var content = model.content;

                    self.links = model.links;
                    Utils.addPrevNextPostArticleLinks(model.links);
                    self.content = new Array();
                    self.content[0] = content.header.toUpperCase();

                    $.merge(self.content,content.body.replaceAll('<br /> <br />','||aa||').replaceAll('<br /><br />','||aa||').split('||aa||'))
                    //$.merge(self.content,content.body.split('<br />'));
                    self.path= model.path;

                    self.render();

                }
            )

        },
        events:{
            "click .thumbnailimage" : "thumbnailClick",

        },

        populateContent:function(){
            var self=this;

            var presscont = this.$('.pressmainposttextcontainer');


            for (var i=0;i<50;i++){
                presscont.append('<div class="presstextcolumn" id="presstextcolumn'+i+'"></div>')
            }


            var column = this.$('#presstextcolumn0');
            for (var i=1;i<self.content.length;i++){
                if (self.content[i].trim()=='') continue;

                column.append('<div class="presstextblock" id="presstextblock'+i+'">'+self.content[i]+'</div>');
            }

            this.$('.presstextcolumn img').css('display','none');
            this.$('.presstextcolumn img').each(function(){
                var imgelem = $(this);
                $('<img>').load(function(){
                    imgelem.fadeIn();
                    self.resize();
                }).attr('src',imgelem.attr('src'));
            });

            this.$('.presstextcolumn iframe').each(function(){
                var elem = $(this);
                var ewidth = elem.attr('width');
                var eheight = elem.attr('height');

                if ((ewidth>0)&&(eheight>0)){
                    var nheight = Math.round(eheight/ewidth*400);
                    elem.attr('width',400);
                    elem.attr('height',nheight);

                } else {
                    $(this).css('width','100%');
                }

            });
        },

        render : function(){

            var self = this;
            self.$el.html(self.template({}));

            self.populateContent();

            $('.pressmainposttextscrollable').mCustomScrollbar({
                axis:"x",
                scrollInertia:0,
                theme:'my-theme',
                autoDraggerLength:false,
                setLeft:"0px",
                mouseWheel : {
                    enable:true
                },
                advanced:{
                    updateOnBrowserResize:true,
                    updateOnContentResize:true,
                    autoExpandHorizontalScroll:true
                }

            });

            self.$el.fadeIn();
            self.resize();
            NProgress.done();
            return this.$el;
        },

        resize : function(event){

            var self=this;
            var height = Utils.getWindowHeight()-$('#menucontainer').height()-$('.portfoliotop').height()-11;

            this.$('.pressmainposttextscrollable,.pressmainposttextcontainer').height(height);

            height-=45;
            this.$('.portfoliomaingallerycontainer,.presstextcolumn').height(height);


            var currentColumn = 0;
            var currentHeight = 0;
            this.$('.presstextblock').each(function(index,elem){
                var currBlock = $(this);
                var cHeight = currBlock.outerHeight(true);
                if (currentHeight+cHeight > height) {
                    currentHeight = 0;
                    currentColumn++;
                }
                currentHeight+=cHeight;

                currBlock.detach();
                self.$('#presstextcolumn'+currentColumn).append(currBlock);
            });

            var widthtotal = 0;
            this.$('.presstextcolumn').each(function(index,elem){
                var elemCon = $(this);

                if (elemCon.html()!='') widthtotal+=435;



            });


            widthtotal+=35;



            this.$('.pressmainposttextcontainer').width(widthtotal);

            $('.pressmainposttextscrollable').mCustomScrollbar('update');
        },



        populateContent1:function(){
            var self=this;

            var presscont = this.$('.pressmainposttextcontainer');

            /*
             for (var i=0;i<20;i++){
             presscont.append('<div class="presstextcolumn" id="presstextcolumn'+i+'"></div>')
             }
             */

            var column = this.$('#presstextcolumn0');
            for (var i=0;i<self.content.length;i++){
                if (self.content[i].trim()=='') continue;

                presscont.append('<p class="presstextblock" id="presstextblock'+i+'">'+self.content[i]+'</p>');
            }

            this.$('.pressmainposttextcontainer img').css('display','none');
            this.$('.pressmainposttextcontainer img').each(function(){
                var imgelem = $(this);
                $('<img>').load(function(){
                    imgelem.fadeIn();
                    self.resize();
                }).attr('src',imgelem.attr('src'));
            });
        },

        render1 : function(){

            var self = this;
            self.$el.html(self.template({}));

            self.populateContent();

            //*
            $('.pressmainposttextscrollable').mCustomScrollbar({
                axis:"x",
                scrollInertia:0,
                theme:'my-theme',
                autoDraggerLength:false,
                setLeft:"0px",
                mouseWheel : {
                    enable:true
                },
                advanced:{
                    updateOnBrowserResize:true,
                    updateOnContentResize:true,
                    autoExpandHorizontalScroll:true
                }

            });
            //*/

            self.$el.fadeIn();
            self.resize();
            NProgress.done();
            return this.$el;
        },

        resize1 : function(event){

            var self=this;
            $('.pressmainposttextscrollable').mCustomScrollbar('disable');

            this.$('.pressmainposttextcontainer').css('height','auto');
            this.$('.pressmainposttextcontainer').width(400);
            var height = Utils.getWindowHeight()-$('#menucontainer').height()-$('.portfoliotop').height()-11;

            this.$('.pressmainposttextscrollable').height(height);
            var totalheight = this.$('.pressmainposttextcontainer').height();
            console.log(totalheight);
            console.log(Math.ceil(totalheight/(height-45)));
            var mwidth = Math.ceil(totalheight/(height-45))*435-35;

            this.$('.pressmainposttextcontainer').width(mwidth);
            this.$('.pressmainposttextcontainer').height(height-45);
            //$('.pressmainposttextscrollable').mCustomScrollbar('update');
            this.$('.pressmainposttextcontainer').css('width','auto');

            return;
            height-=45;
            this.$('.portfoliomaingallerycontainer,.presstextcolumn').height(height);


            var currentColumn = 0;
            var currentHeight = 0;
            this.$('.presstextblock').each(function(index,elem){
                var currBlock = $(this);
                var cHeight = currBlock.outerHeight(true);
                if (currentHeight+cHeight > height) {
                    currentHeight = 0;
                    currentColumn++;
                }
                currentHeight+=cHeight;

                currBlock.detach();
                self.$('#presstextcolumn'+currentColumn).append(currBlock);
            });

            var widthtotal = 0;
            this.$('.presstextcolumn').each(function(index,elem){
                var elemCon = $(this);

                if (elemCon.html()!='') widthtotal+=435;



            });


            widthtotal+=35;



            this.$('.pressmainposttextcontainer').width(widthtotal);

            $('.pressmainposttextscrollable').mCustomScrollbar('update');
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

    return PressPostTextView;

});

