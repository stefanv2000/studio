define([
    'jquery',
    'underscore',
    'backbone',
    'models/intro/introCollection'
],function($,_,Backbone,IntroMain){

    var IntroView = Backbone.View.extend({
        className:'introcontainer',
        id:"introcontainer",
        initialize : function(){

            this.template = Templates.getTemplate("template-intro");

            var intromodel = new IntroMain({'mobile':true});

            var self = this;
            intromodel.fetch({
                success : function(model){
                    self.introcollection = model.get('content');
                    self.render();
                }
            });
        },
        events:{
            "click .introsectioncontainer" : "goToLink",
        },
        render : function(){

            var self = this;
            self.$el.html(self.template({'con' : self.introcollection.toJSON()}));



            //self.$el.show();
            self.showImages();

            return this.$el;
        },

        showImages:function(){
            var self = this;
            this.$('.introimage').each(function(index,elem){
                var that = $(this);
                $('<img>').load(function(){

                    //that.parent().addClass('introsectioncontainerblack');
                    that.fadeIn(function(){
                        that.parent().addClass('introsectioncontainerblack');
                        that.animate({'opacity':0.7});
                    });

                }).attr('src',that.attr('src'));
            });

        },

        goToLink:function(event){
            event.preventDefault();
            router.navigate($(event.currentTarget).data('slug'),{trigger:true});
        },

        close : function(){
            this.$el.remove();
        },


        show : function() {
            this.$el.fadeIn();
        }
    })

    return IntroView;

});