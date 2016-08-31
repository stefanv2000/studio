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
            NProgress.inc();
            var categorymodel = new CategoryModelsMain({});



            var self = this;
            categorymodel.fetch({
                success : function(model){
                    NProgress.inc();
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

                    if (xtype == "search") {
                        optionsS.sectiontype = 'search';
                        optionsS.selectedId = -1;
                    } else {

                        var xxx = self.collection.find(function (item) {
                            if (item.get('slug') == xtype) return true;
                            return false;
                        });

                        if (xxx.get('slug') !== 'xxprofile') {
                            optionsS.sectiontype = 'category';
                            optionsS.selectedId = xxx.get('id');
                        }
                    }


                    self.trigger("showview:main",optionsS);
                },
                error: function(){
                    alert('fetch error');
                }
            });
        },
        setupEvents : function(){
            var self = this;

            self.on("showview:main",function(options){


                NProgress.inc();
                if (self.innerView) self.innerView.close();

                self.$('.modelscategorymenuitemselected').removeClass('modelscategorymenuitemselected');
                self.$('#modelscategorymenuitem'+options.selectedId).addClass('modelscategorymenuitemselected');



                if ((options.sectiontype == "category")){
                    self.trigger('showview:category',options);
                } else
                if (options.sectiontype == "search") {
                    self.trigger('showview:search',options);
                }
            });

            self.on('showview:category',function(options){
                    require(['views/models/listCategory'],function(ListCategoryView){
                        NProgress.inc();
                        var listCatView = new ListCategoryView(options);
                        self.innerView = listCatView;
                        self.$('.modelscategorysectincontainer').html(listCatView.$el);
                    });
            });

            self.on('showview:search',function(options){
                //self.$('.modelscategorylayoutmenucontainer').hide();
                require(['views/models/search'],function(SearchView){
                    NProgress.inc();
                    var view = new SearchView(options);
                    self.innerView = view;
                    self.$('.modelscategorysectincontainer').html(view.$el);
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
            NProgress.inc();
            this.$('.modelscategorymaintitle span').kern();
            backgroundview.clearBackground();
            self.$el.fadeIn();

            $('#menucontainer').css({
                'position' : 'fixed',
                'top' : 0,
                'background-color' : '#FFFFFF'
            })
            self.$('.modelscategorytop').css({
                'top' : $('#menucontainer').height()+'px',
            });

            self.$('.modelscategorysectincontainer').css('margin-top',$('#menucontainer').height()+self.$('.modelscategorytop').height()+'px');

            return this.$el;
        },
        close : function(){
            if (this.innerView) this.innerView.close();
            $('#menucontainer').css({
                'position' : 'static',
                'background-color' : 'transparent',
            })
            this.$el.remove();
        },

        show : function() {

        }
    })

    return CategoryLayoutView;

});
