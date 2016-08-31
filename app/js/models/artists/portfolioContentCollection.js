define(['underscore','backbone'],
    function(_,Backbone){

        var PortfolioContent = Backbone.Model.extend({

        });

        var PortfolioContentCollection = Backbone.Collection.extend({
            model:PortfolioContent,
        });
        var PortfolioContentMain = Backbone.Model.extend({
            initialize :function(options){
                this.urlRoot = '/api/'+options.path;
            },
            urlRoot:'/api',
            parse : function(response){
                response.content = new PortfolioContentCollection(response.content);
                return response;
            },
        });
        return PortfolioContentMain;
    });

