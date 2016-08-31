define([
    'jquery',
    'underscore',
    'backbone',
    'models/models/searchCollection'
],function($,_,Backbone,SearchCollection){

    var SearchModelsView = Backbone.View.extend({
        className:'searchmodelscontainer',
        id:"searchmodelscontainer",
        initialize : function(){
            this.template = Templates.getTemplate("template-models-search");
            this.templateList = Templates.getTemplate("template-models-searchList");
            NProgress.inc();
            var model = new SearchCollection();

            var self = this;
            model.fetch({
                success : function(model){
                    NProgress.inc();
                    self.criteria = model.get('criteria');
                    self.models = model.get('models');

                    self.render();
                }
            });
        },
        events:{
        },
        render : function(){

            var self = this;
            var arrayOptions = ['height','bust','waist','hips','dress','shoe','eyes','shirt','suit','inseam'];
            var xxx = self.criteria.toJSON();

            self.$el.html(self.template({'criteria' : self.criteria.toJSON()[0],'criteriaoptions' : arrayOptions}));
            NProgress.inc();
            self.$('.searchdropdown').dropit();

            self.$('.searchdropdownoption').click(function(event){
                var elem = $(event.currentTarget);
                self.$('#'+elem.data('parentid')).html(elem.html());
                self.$('#'+elem.data('parentid')).data('selectedvalue',elem.html());
                self.$('#'+elem.data('parentid')).addClass('mainsearchlinkselected');
                self.showResults();
            });

            self.$('#searchname').keyup(function(){self.showResults();});

            self.$('.searchmodelsleft').css({
                'top' : $('.menucontainer').height()+$('.modelscategorytop').height()+'px',
            })

            backgroundview.clearBackground();
            self.$el.fadeIn();
            NProgress.done();
            return this.$el;
        },

        showResults:function(){
            var self = this;
            var arraySelected = {};
            if (this.$('#searchname').val()!='') arraySelected['name'] =this.$('#searchname').val();
            self.$('.mainsearchlink').each(function(index,elem){
                var elemDropdown = $(this);
                if (elemDropdown.data('selectedvalue')!='') arraySelected[elemDropdown.data('criteria')]=elemDropdown.data('selectedvalue');
            });

            var modelsList = self.searchModels(arraySelected);

            this.$('.searchmodelsright').hide();
            this.displayResults(modelsList);
            this.$('.searchmodelsright').fadeIn();
            this.showImagesFade();

        },

        showImagesFade:function(){
            var self = this;
            this.$('.modelimage').each(function(index,elem){
                var that = $(this);

                $('<img>').load(function(){
                    that.fadeIn(function(){that.parent().addClass('containermodelimageloaded');});

                }).attr('src',that.attr('src'));

                $(this).mouseenter(function(event){
                    var element = $(event.currentTarget);
                    element.addClass('modelimageover');
                }).mouseleave(function(event){
                    var element = $(event.currentTarget);
                    element.removeClass('modelimageover');
                }).click(function(event){
                    event.preventDefault();
                    router.navigate($(event.currentTarget).data('slug'),{trigger:true});
                });
            });

        },

        searchModels:function(arrayCriteria){
            var self = this;
            var arrayModels = self.models.toJSON();
            var arRes = [];
            for (var i=0;i<arrayModels.length;i++){
                var model = arrayModels[i];

                var iscriteria = true;
                for (key in arrayCriteria){
                    if (key=='name'){
                        if (model['name'].toLowerCase().indexOf(arrayCriteria[key].toLowerCase())<0) {iscriteria=false;break}
                        continue;
                    }

                    if (!(key in model['info'])) {iscriteria=false;break;}
                    if (model['info'][key]!=arrayCriteria[key]){iscriteria=false;break;}
                }

                if (iscriteria) arRes.push(model);
            }

            return arRes;

        },

        displayResults:function(listModels){
            this.$('.searchmodelsright').html(this.templateList({'models':listModels}));
        },
        close : function(){
            this.$el.remove();
        },



         show : function() {

        }
    })



    return SearchModelsView;

});