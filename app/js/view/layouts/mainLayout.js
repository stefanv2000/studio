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
        events:{
            "mouseenter .ihousecontainer" : "mouseentercredit",
            "mouseleave .ihousecontainer" : "mouseleavecredit",
            "click .ihousecontainer" : "clickcredit",
        },
        mouseentercredit : function(){
            this.$('.ihousecontainer').stop().animate({'width':'118px'});
        },
        mouseleavecredit : function(){
            this.$('.ihousecontainer').stop().animate({'width':'14px'});
        },
        clickcredit : function() {
            window.open('http://ihousedesign.com');
        },
        render : function(){
            var self = this;

            this.$el.html(this.template({}));

            require(['views/menu/menu'],function(MenuView){
                var menuView = new MenuView();
                self.menuview = menuView;
                self.$el.find('#maincontainer').prepend(menuView.$el);
            })

            return this.$el;
        },

        showCredit:function(){
            this.$('.ihousecontainer').show();
        },

        hideCredit:function(){
            this.$('.ihousecontainer').hide();
        },
    })

    return MainLayoutView;

});