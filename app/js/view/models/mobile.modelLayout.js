define([
    'jquery',
    'underscore',
    'backbone',
    'models/models/modelCollection'
],function($,_,Backbone,ModelMain){

    var ModelLayoutView = Backbone.View.extend({
        className:'modelspagecontainer',
        id:"modelspagecontainer",
        innerView : null,
        initialize : function(options){

            this.options = options;
            this.setupEvents();

            this.template = Templates.getTemplate("template-models-modelPageLayout");


            var model = new ModelMain({path:Backbone.history.getFragment().split('/').slice(0,3).join('/')});


            var self = this;
            model.fetch({
                success : function(model){

                    self.collection = model.get('content');
                    self.path = model.get('path');
                    self.linksModel = model.get('links');


                    var xtype = Backbone.history.getFragment().split('/').slice(3,4).join('');



                    if ((typeof xtype=="undefined")||(xtype==null)||(xtype=='')) {
                        xtype = self.collection.at(1).get('slug');
                        router.navigate('/'+Backbone.history.getFragment().split('/').slice(0,3).join('/')+'/'+xtype,{trigger : true});
                    }


                    self.render();





                    var xxx = self.collection.find(function(item){
                        if (item.get('slug') == xtype) return true;
                        return false;
                    });

                    var optionsS = {};

                    if (xxx.get('slug') !== 'xxprofile')  {
                        optionsS.sectiontype = 'gallery';
                        optionsS.selectedId = xxx.get('id');
                    }


                    self.trigger("showview:main",optionsS);
                }
            });
        },
        setupEvents : function(){
            var self = this;

            self.on("showview:main",function(options){


                if (self.innerView) self.innerView.close();

                self.$('.modelpagemenuitemselected').removeClass('modelpagemenuitemselected');
                self.$('#modelpagemenuitem'+options.selectedId).addClass('modelpagemenuitemselected');



                if (options.sectiontype == "gallery") {
                    self.trigger('showview:gallery',options);
                }
            });

            self.on('showview:gallery',function(options){
                require(['views/models/mobile.gallery'],function(GalleryView){
                    var view = new GalleryView(options);
                    self.innerView = view;
                    self.$('.modelpagesectincontainer').html(view.$el);
                });
            });

        },
        events:{
            "click #allmodelslink" : "goToHref",
            "click .modelpagemenuitem" : "menuItemClick",
        },
        goToHref : function(event){
            event.preventDefault();
            if ($(event.currentTarget).attr('href') =='') return;
            router.navigate($(event.currentTarget).attr('href'),{'trigger':true});
        },
        menuItemClick : function(event){
            event.preventDefault();

            router.navigate($(event.currentTarget).attr('href'),{'trigger':false});

            var options = {'sectiontype' : $(event.currentTarget).data('type'),'selectedId':$(event.currentTarget).data('id')};
            this.trigger("showview:main",options);
        },

        render : function(){
            var self = this;
            self.$el.html(self.template({
                'con' : self.collection.toJSON(),
                'path' : self.path,
                'links' : self.linksModel,
            }));

            self.$el.fadeIn();

            return this.$el;
        },
        close : function(){
            if (this.innerView) this.innerView.close();
            this.$el.remove();
        },

        show : function() {

        }
    })

    return ModelLayoutView;

});

