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
            NProgress.inc();
            self.menulink = $('a.linkmenu[href="/contact"]');

            $.get('/api/contact',function(response){
                NProgress.inc();
                self.model = response;
                self.render();
            },'json');

        },
        render : function(){
            backgroundview.clearBackground();
            var self = this;

            this.$el.html(this.template({'content' : this.model}));
            NProgress.inc();
            self.$('.contacttextwrapper1,.contactfirsttext').each(function(index,elem){
                var email_regex = /([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi;
                var bodyText = $(this).html();
                var result = bodyText.replace(email_regex,'<a href="mailto:$1">$1</a>');
                $(this).html(result);
            });
            self.$('.modelpagemaintitle span').kern();

            self.menulink.addClass('linkmenuselected');
            FB.XFBML.parse(document.getElementById('facebooklikecontainer'));
            this.$el.fadeIn();
            NProgress.done();
            return this.$el;
        },

        events : {
            "click #contacttitle" : "goToIntro"
        },

        goToIntro : function(event){
            event.preventDefault();
            router.navigate('/',{trigger : true});
        },

        close : function(){
            this.menulink.removeClass('linkmenuselected');
            this.$el.remove();
        },

        show : function() {

        }
    })

    return ContactView;

});