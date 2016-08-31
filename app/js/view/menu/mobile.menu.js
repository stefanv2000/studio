define([
    'jquery',
    'underscore',
    'backbone',
],function($,_,Backbone){

    var MenuView = Backbone.View.extend({
        className:'menucontainer',
        id:"menucontainer",
        events:{
            "click .linkmenu" : "goToLink",
            "click .logolink" : "goToLink",
            "keyup #inputmenusearch" : "search",
        },
        goToLink : function(event){
            event.preventDefault();
            router.navigate($(event.currentTarget).attr('href'),{'trigger' : true});
        },
        initialize : function(){
            this.template = Templates.getTemplate("template-menu");

            this.render();
        },
        render : function(){

            this.$el.html(this.template({'sociallinks':socialMediaLinks}));

            return this.$el;
        },

        showLogoModels:function(){
            this.$('.logo studiogroup').hide();
            this.$('.logomodels').show();
        },

        showLogoGroup:function(){
            this.$('.logomodels').hide();
            this.$('.logo studiogroup').show();
        },

    })

    return MenuView;

});