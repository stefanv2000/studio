define(['underscore','backbone'],
    function(_,Backbone){

        var CategoryContent = Backbone.Model.extend({

        });

        var CategoryContentCollection = Backbone.Collection.extend({
            model:CategoryContent,
        });
        var CategoryMain = Backbone.Model.extend({
            initialize :function(options){
                //this.urlRoot = '/api/'+options.path;
            },
            urlRoot:'/api/models',
            parse : function(response){
                response.content = new CategoryContentCollection(response.content);
                return response;
            },
        });
        return CategoryMain;
    });

