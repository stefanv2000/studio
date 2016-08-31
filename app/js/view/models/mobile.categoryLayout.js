define([
    'jquery',
    'underscore',
    'backbone',
    'models/models/categoryCollection'
],function($,_,Backbone,CategoryModelsMain){

    var CategoryLayoutView = Backbone.View.extend({
        className:'modelscategorycontainer',
        id:"modelscategorycontainer",
        innerView : null,
        initialize : function(options){
            this.options = options;
            this.setupEvents();

            this.template = Templates.getTemplate("template-models-categoryLayout");

            var categorymodel = new CategoryModelsMain({});



            var self = this;
            categorymodel.fetch({
                success : function(model){
                    self.collection = model.get('content');
                    self.path = model.get('path');

                    var xtype = Backbone.history.getFragment().split('/').slice(1,2).join('');
                    if ((typeof xtype=="undefined")||(xtype==null)||(xtype=='')) {
                        xtype = self.collection.at(0).get('slug');
                        router.navigate("/models/"+xtype,{trigger : false});
                        routesEventsManager.trigger('routes:showmodels',{'category':xtype});
                    }

                    self.render();


                    var optionsS = {};



                        var xxx = self.collection.find(function (item) {
                            if (item.get('slug') == xtype) return true;
                            return false;
                        });

                        if (xxx.get('slug') !== 'xxprofile') {
                            optionsS.sectiontype = 'category';
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

                self.$('.modelscategorymenuitemselected').removeClass('modelscategorymenuitemselected');
                self.$('#modelscategorymenuitem'+options.selectedId).addClass('modelscategorymenuitemselected');



                if ((options.sectiontype == "category")){
                    self.trigger('showview:category',options);
                }
            });

            self.on('showview:category',function(options){
                require(['views/models/mobile.listCategory'],function(ListCategoryView){
                    var listCatView = new ListCategoryView(options);
                    self.innerView = listCatView;
                    self.$('.modelscategorysectincontainer').html(listCatView.$el);
                });
            });


        },
        events:{
            "click .modelscategorymainnamelink" : "goToIntro",
            "click .modelscategorymenuitem" : "menuItemClick",
        },
        goToIntro : function(event){
            event.preventDefault();
            router.navigate('/',{'trigger':true});
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

    return CategoryLayoutView;

});
