define([
    'jquery',
    'underscore',
    'backbone',
    'models/intro/typeCollection'
],function($,_,Backbone,TypeModel){

    var TypeView = Backbone.View.extend({
        className:'typecontainer',
        id:"typecontainer",
        initialize : function(){

            this.template = Templates.getTemplate("template-type");

            var typemodel = new TypeModel({'path':Backbone.history.getFragment()});

            var self = this;
            typemodel.fetch({
                success : function(model){
                    self.typecollection = model.get('content');
                    self.name = model.get('name');
                    self.slug = model.get('slug');
                    self.render();
                }
            });
        },
        events:{
            "click .typetoplinks" : "clickTopLinks",
            "click .typesublinks" : "clickSubLinks",
        },
        render : function(){

            var self = this;
            self.$el.html(self.template({'con' : self.typecollection.toJSON(),'name' : self.name,'slug' : self.slug}));

            self.$el.fadeIn();

            //self.$el.show();
            //self.showImages();

            return this.$el;
        },

        clickTopLinks:function(event){
            event.preventDefault();
            var element = $(event.currentTarget);
            this.$('.displaylinkscontainer').hide();
            if (this.$('#displaylinkscontainer'+element.data('id')).length>0) {
                this.$('#displaylinkscontainer' + element.data('id')).fadeIn();
                this.$('.typetoplinks').removeClass('typelinksselected');
                element.addClass('typelinksselected');
            } else router.navigate(element.attr('href'),{trigger:true});
        },

        clickSubLinks:function(event){
            event.preventDefault();
            var element = $(event.currentTarget);
            router.navigate(element.attr('href'),{trigger:true});

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

    return TypeView;

});