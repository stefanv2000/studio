define([
    'jquery',
    'underscore',
    'backbone',
],function($,_,Backbone){

    var InternationalView = Backbone.View.extend({
        className:'pagecontainer',
        id:"pagecontainer",
        initialize : function(){
            var self = this;
            this.template = Templates.getTemplate("template-pages-international");
            NProgress.inc();
            self.menulink = $('a.linkmenu[href="/international"]');

            $.get('/api/international',function(response){
                NProgress.inc();
                self.model = response;
                self.render();
            },'json');

        },
        render : function(){
            backgroundview.clearBackground();
            var self = this;

            this.$el.html(this.template({'content' : this.model}));

            self.$('.modelpagemaintitle span').kern();
            self.menulink.addClass('linkmenuselected');
            this.$el.fadeIn();
            NProgress.inc();
            this.showImages();
            NProgress.done();
            return this.$el;
        },

        events : {
            "mouseenter .modelimage" : "mouseenterimage",
            "mouseleave .modelimage" : "mouseleaveimage",
            "click .modelimage" : "imageModelClick",
            "click #internationaltitle" : "goToIntro"
        },

        mouseenterimage : function(event){
            var element = $(event.currentTarget);
            element.addClass('modelimageover');
        },

        mouseleaveimage : function(event){

            var element = $(event.currentTarget);
            element.removeClass('modelimageover');
        },
        imageModelClick : function(event){
            event.preventDefault();

            if ($(event.currentTarget).data('slug').startsWith('http')) {
                window.open($(event.currentTarget).data('slug'));
                //location.href = $(event.currentTarget).data('slug');
            } else
            router.navigate($(event.currentTarget).data('slug'),{trigger:true});
        },
        showImages:function(){
            var self = this;
            this.$('.modelimage').each(function(index,elem){
                var that = $(this);
                $('<img>').load(function(){
                    that.fadeIn(function(){that.parent().addClass('containermodelimageloaded');});

                }).attr('src',that.attr('src'));
            });

        },

        goToIntro : function(event){
            event.preventDefault();
            router.navigate('/',{trigger : true});
        },

        close : function(){
            this.menulink.removeClass('linkmenuselected');
            this.$el.remove();
        },

        show : function() {

        }
    })

    return InternationalView;

});