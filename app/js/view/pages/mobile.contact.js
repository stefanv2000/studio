define([
    'jquery',
    'underscore',
    'backbone',
],function($,_,Backbone){

    var ContactView = Backbone.View.extend({
        className:'pagecontainer',
        id:"pagecontainer",
        initialize : function(){
            var self = this;
            this.template = Templates.getTemplate("template-pages-contact");

            $.get('/api/contact',function(response){
                self.model = response;
                self.render();
            },'json');

        },
        render : function(){
            var self = this;

            this.$el.html(this.template({'content' : this.model}));

            self.$('.contacttextwrapper1,.contactfirsttext').each(function(index,elem){
                var email_regex = /([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi;
                var bodyText = $(this).html();
                var result = bodyText.replace(email_regex,'<a href="mailto:$1">$1</a>');
                $(this).html(result);
            });
            this.$el.fadeIn();
            FB.XFBML.parse(document.getElementById('facebooklikecontainer'));

            return this.$el;
        },

        events : {
            "click .profilemainnamelink" : "goToIntro"
        },

        goToIntro : function(event){
            event.preventDefault();
            router.navigate('/',{trigger : true});
        },

        close : function(){
            this.$el.remove();
        },

        show : function() {

        }
    })

    return ContactView;

});