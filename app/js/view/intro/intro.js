define([
    'jquery',
    'underscore',
    'backbone',
    'models/intro/introCollection',

],function($,_,Backbone,IntroMain){

    var IntroView = Backbone.View.extend({
        className:'introcontainer',
        id:"introcontainer",
        initialize : function(){
            this.template = Templates.getTemplate("template-intro");

            NProgress.inc();
            var intromodel = new IntroMain();

            this.menulink = $('a.linkmenu[href="/"]');

            var self = this;
            intromodel.fetch({
                success : function(model){
                    NProgress.inc();
                    self.introcollection = model.get('content');
                    self.background = model.get('background');
                    self.render();
                }
            });
        },
        events:{
            "mouseenter .mainname" : "mouseentermainlink",
            "mouseleave .mainname" : "mouseleavemainlink",
            "mouseenter .introsubsectionname" : "mouseentersubsectionlink",
            "mouseleave .introsubsectionname" : "mouseleavesubsectionlink",

            "click .mainname" : "showsubsectionsmainlink",
            "click .introsubsectionnamelink" : "subsectionsclick",
            "click .introsubsectionslinks" : "subsectionlinksclick",
        },
        render : function(){
            NProgress.inc();
            var self = this;
            self.$el.html(self.template({'con' : self.introcollection.toJSON()}));
            NProgress.inc();
            self.menulink.addClass('linkmenuselected');

            self.$el.find('.mainnamelink').each(function(index,elem){
                if ($(this).data('slug') == "/"+Backbone.history.getFragment()){
                    $(this).trigger('click');
                }
            });
            self.$el.find('.mainnamelink span').kern();
            self.$el.find('.introsubsectionnamelink span').kern();

            backgroundview.reinit(this.background,self,false);
            NProgress.done();
            //self.$el.fadeIn();
            return this.$el;
        },
        close : function(){
            this.menulink.removeClass('linkmenuselected');
            this.$el.remove();
        },

        mouseentermainlink : function(event){
            var element = $(event.currentTarget);
            if (element.find('.mainnamelinkdisabled').length>0) return;
            //$('.mainnametext').slideUp();
            $('#mainnametext'+element.data('id')).stop().slideDown(function(){$(this).css('height','auto')});
            var str = '';

        },

        mouseleavemainlink : function(event){
            var element = $(event.currentTarget);
            $('#mainnametext'+element.data('id')).stop().slideUp(function(){$(this).css('height','auto')});
        },

        mouseentersubsectionlink:function(event){
            var element = $(event.currentTarget);
            $('#introsubsectionlinks'+element.data('id')).stop().slideDown(function(){$(this).css('height','auto')});
        },

        mouseleavesubsectionlink : function(event){
            var element = $(event.currentTarget);
            $('#introsubsectionlinks'+element.data('id')).stop().slideUp(function(){$(this).css('height','auto')});

        },

        subsectionlinksclick : function(event){
            event.preventDefault();
            var element = $(event.currentTarget);
            router.navigate(element.attr('href'),{trigger:true});
        },
        subsectionsclick : function(event){
            event.preventDefault();
        },
        showsubsectionsmainlink : function(event){
            event.preventDefault();
            var element = $(event.currentTarget);

            if (element.data('slug')=="/models") {
                router.navigate('/models',{trigger:true});
                return;
            }

            if ((element.data('slug')=="/food_drink")||(element.data('slug')=="/food-_-drink")||(element.data('slug')=='influencers1')) {
                return;
            }



            var mainlink = element.find('.mainnamelink');
            if (mainlink.hasClass('mainnamelink')) {
                mainlink.removeClass('mainnamelink').addClass('mainnamelinkdisabled');
            } else {
                mainlink.removeClass('mainnamelinkdisabled').addClass('mainnamelink');
            }

            this.$('.mainnamelinkdisabled').not(mainlink).removeClass('mainnamelinkdisabled').addClass('mainnamelink');

            this.$('.introsubsectionscontainer').not('#introsubsectionscontainer'+element.data('id')).slideUp(function(){$(this).css('height','auto')});

            var imgsElem=this.$('.introsubsectionscontainer').not('#introsubsectionscontainer'+element.data('id')).find('img');
            if (imgsElem.hasClass('rotateDown')) imgsElem.removeClass('rotateDown'); else imgsElem.addClass('rotateDown');

            $('.mainnametext').slideUp(function(){$(this).css('height','auto')});
            $('#mainnametext'+element.data('id')).stop().slideUp(function(){$(this).css('height','auto')});
            $('#introsubsectionscontainer'+element.data('id')).stop().slideToggle(function(){$(this).css('height','auto')});

            var imgElem = element.find('img');

            if (imgElem.hasClass('rotateDown')) imgElem.removeClass('rotateDown'); else imgElem.addClass('rotateDown');
            this.$('img').not(imgElem).removeClass('rotateDown');


            if (element.data('slug') == "/"+Backbone.history.getFragment()) router.navigate("",{trigger:false});
            else router.navigate(element.data('slug'),{trigger:false});
        },

        show : function() {
            this.$el.fadeIn();
        }
    })

    return IntroView;

});