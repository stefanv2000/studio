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
            NProgress.inc();

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
                        if (xxx.get('description1') == '2') optionsS.sectiontype = 'portfolio_old';
                        optionsS.selectedId = xxx.get('id');
                        optionsS.index = options.index;
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


                if (self.innerView) self.innerView.close();

                self.$('.modelpagemenuitemselected').removeClass('modelpagemenuitemselected');
                self.$('#modelpagemenuitem'+options.selectedId).addClass('modelpagemenuitemselected');



                if ((options.sectiontype == "portfolio_old")){
                    self.trigger('showview:portfolio_old',options);
                } else
                if (options.sectiontype == "gallery") {
                    self.trigger('showview:gallery',options);
                }
            });

            self.on('showview:portfolio_old',function(options){
                require(['views/models/oldportfolio'],function(OldPortfolioView){
                    var view = new OldPortfolioView(options);
                    self.innerView = view;
                    self.$('.modelpagesectincontainer').html(view.$el);
                });
            });

            self.on('showview:gallery',function(options){
                require(['views/models/gallery'],function(GalleryView){
                    var view = new GalleryView(options);
                    self.innerView = view;
                    self.$('.modelpagesectincontainer').html(view.$el);
                });
            });

        },
        events:{
            "click #previousmodellink,#nextmodellink,#allmodelslink" : "goToHref",
            "click .modelpagemenuitem" : "menuItemClick",
        },
        goToHref : function(event){
            event.preventDefault();
            if ($(event.currentTarget).attr('href') =='') return;
            router.navigate($(event.currentTarget).attr('href'),{'trigger':true});
        },
        menuItemClick : function(event){
            event.preventDefault();
            if ($(event.currentTarget).data('type') == 'social'){
                window.open($(event.currentTarget).data('slug'));
                return;
            }
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

            this.$('.modelpagemaintitle span').kern();
            backgroundview.clearBackground();
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

