define([
    'jquery',
    'underscore',
    'backbone',
    'models/artists/profileContentModel'
],function($,_,Backbone,ProfileContentModel){

    var ProfileView = Backbone.View.extend({
        className:'profilecontainer',
        id:"profilecontainer",
        initialize : function(){
            var self = this;
            this.template = Templates.getTemplate("template-artists-profile");
            NProgress.inc();
            self.model = new ProfileContentModel({path:Backbone.history.getFragment()});


            var self = this;
            self.model.fetch({
                success : function(model){
                    NProgress.inc();
                    self.render();
                }
            });
        },
        events:{
        },
        render : function(){

            var self = this;
            self.$el.html(self.template({'model' : self.model.toJSON()}));
            NProgress.inc();
            backgroundview.clearBackground();
            self.$('.imageprofile img').each(function(){
                var imageElem = $(this);

                $('<img>').load(function(){imageElem.animate({'opacity' : 1});}).attr('src',imageElem.attr('src'));
            });
            NProgress.done();
            self.$el.fadeIn();
            return this.$el;
        },
        close : function(){
            this.$el.remove();
        },


        show : function() {

        }
    })

    return ProfileView;

});
