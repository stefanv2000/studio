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
            var menuLinks = new Array();


            this.$el.html(this.template({menulinks:menuItems,sociallinks:socialMediaLinks }));
            var self = this;
            setTimeout(function(){return;self.makeSearch('ali');
                self.showSearch();},1000);

            return this.$el;
        },
        search:function(event){
            if (event.keyCode == 27) {
                this.hideSearch();
                return;
            }

            //var c = String.fromCharCode(event.which)
            var inputvalue = $(event.currentTarget).val();
            if (inputvalue.length>=2) {
                this.makeSearch(inputvalue);
                this.showSearch();
            } else {
                this.hideSearch();
            }

        },

        makeSearch:function(searchString){
            var searchSt = searchString.toLowerCase();
            var maxItems = 10;
            var arrayRes = [];var j=0;
            var self=this;
            for (var i=0;i<searchItems.length;i++){
                if (searchItems[i].name.toLowerCase().indexOf(searchSt)>=0){
                    arrayRes[j] = searchItems[i];j++;
                    if (j>=maxItems) break;
                }
            }
            this.$('.searchcontentholder').html('');
            for (var i=0;i<arrayRes.length;i++){
                var elem=$('<div class="searchitemwrapper"></div>');
                elem.html('<a href="'+arrayRes[i].slug+'" class="searchitemlink"><span class="searchitemname">'+arrayRes[i].name+'</span><span class="searchitemparents">'+arrayRes[i].parent+'</span></a>');
                this.$('.searchcontentholder').append(elem);
            }

            this.$('.searchitemlink').click(function(event){
                event.preventDefault();
                self.hideSearch();
                self.$('#inputmenusearch').val('');
                router.navigate($(event.currentTarget).attr('href'),{trigger : true});
            });
            //this.$('.searchcontentholder').html(searchString);
        },

        showSearch:function(){
            var elem = this.$('#inputmenusearch');
            var searchContainer = this.$('.searchcontentholder');

            if (searchContainer.html() == '') {this.hideSearch();return;}

            var offset = elem.offset();

            searchContainer.css(
                {top:offset.top+10+elem.height()+'px',left:offset.left+'px'});


            searchContainer.slideDown();
        },

        showLogoModels:function(){

            this.$('.logo studiogroup').hide();
            this.$('.logomodels').show();
        },

        showLogoGroup:function(){
            this.$('.logomodels').hide();
            this.$('.logo studiogroup').show();
        },

        hideSearch : function(){
            this.$('.searchcontentholder').slideUp();
            //this.$('.searchcontentholder').html('');
        }
    })

    return MenuView;

});