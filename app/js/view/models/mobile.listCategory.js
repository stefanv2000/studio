define([
    'jquery',
    'underscore',
    'backbone',
    'models/models/categoryContentCollection'
],function($,_,Backbone,CategoryContentMain){

    var ListCategoryView = Backbone.View.extend({
        className:'maincategorycontainer',
        id:"maincategorycontainer",
        initialize : function(options){
            this.options = options;
            this.template = Templates.getTemplate("template-models-listCategory");


            var categorymodel = new CategoryContentMain({path:Backbone.history.getFragment().split('/').slice(0).join('/')});

            if (options.index != null) this.imageIndex=parseInt(options.index); else this.imageIndex=0;


            var self = this;
            categorymodel.fetch({
                success : function(model){
                    self.collection = model.get('content');
                    self.path= model.get('path');
                    self.render();
                }
            });
        },
        events:{
            "click .modelimage" : "imageModelClick",
            "click .modellink" : "imageModelClick",
        },
        render : function(){
            var self = this;
            self.$el.html(self.template({
                'con' : self.collection.toJSON(),
                'path' : this.path
            }));


            //self.resize();

            self.$el.fadeIn();
            self.showImages();

            return this.$el;
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

        imageModelClick : function(event){
            event.preventDefault();
            router.navigate($(event.currentTarget).data('slug'),{trigger:true});
        },


        close : function(){
            this.$el.remove();

        },

        show : function() {

        }
    })

    return ListCategoryView;

});


