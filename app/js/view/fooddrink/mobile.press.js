define([
    'jquery',
    'underscore',
    'backbone',
    'models/fooddrink/pressContentModel'
],function($,_,Backbone,PressContentModel){

    var PressView = Backbone.View.extend({
        className:'presscontainer',
        id:"presscontainer",
        initialize : function(options){
            var self = this;
            this.template = Templates.getTemplate("template-fooddrink-press");

            this.parentView = options.parent;

            $(window).on('resize',this.resize);

            self.model = new PressContentModel({path:Backbone.history.getFragment()});


            var self = this;
            self.model.fetch({
                success : function(model){
                    self.render();
                }
            });
        },
        events:{
            "click .mainpressimagecontainer" : 'showPressPost'
        },

        render : function(){

            var self = this;
            self.$el.html(self.template({'con' : self.model.get('content').toJSON()}));


            self.resize();

            self.$el.fadeIn();
            return this.$el;
        },

        resize : function(event){




        },

        showPressPost : function(event){
            event.preventDefault();
            var element = $(event.currentTarget);
            router.navigate(element.data('slug'),{trigger : false});
            this.parentView.trigger('showview:main');

        },
        close : function(){
            $(window).off('resize',this.resize);
            this.$el.remove();
        },


        show : function() {

        }
    })

    return PressView;

});
