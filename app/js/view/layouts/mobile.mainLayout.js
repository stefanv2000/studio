define([
    'jquery',
    'underscore',
    'backbone',
],function($,_,Backbone){

    var MainLayoutView = Backbone.View.extend({
        className:'container',
        id:"container",
        initialize : function(){
            this.template = Templates.getTemplate("template-layout");

        },
        render : function(){
            var self = this;

            this.$el.html(this.template({}));


            require(['views/menu/mobile.menu'],function(MenuView){
                var menuView = new MenuView();
                self.menuview = menuView;
                self.$el.find('#maincontainer').prepend(menuView.$el);
            })

            return this.$el;
        },
    })

    return MainLayoutView;

});