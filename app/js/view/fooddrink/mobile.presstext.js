define([
    'jquery',
    'underscore',
    'backbone',
    'models/artists/portfolioContentCollection',
    'views/artists/gallery'
],function($,_,Backbone,PortfolioContentMain){

    var PressPostTextView = Backbone.View.extend({
        className:'mainpresstextcontainer',
        id:"mainpresstextcontainer",
        initialize : function(options){
            this.options = options;
            this.template = Templates.getTemplate("template-fooddrink-posttext");

            this.options.link = Backbone.history.getFragment().split('/').slice(0,1).join('/');


            var self = this;
            $.get('/api/'+Backbone.history.getFragment().split('/').slice(0).join('/'),
                {},
                function(model){
                    var content = model.content;

                    self.content = new Array();
                    self.content['header'] = content.header;
                    self.content['body'] = content.body;

                    self.render();

                }
            )

        },
        render : function(){

            var self = this;
            self.$el.html(self.template({'content' : self.content}));


            self.$el.fadeIn();
            return this.$el;
        },

                close : function(){
            this.$el.remove();

        },

        show : function() {

        }
    })

    return PressPostTextView;

});

