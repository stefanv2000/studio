define(['underscore','backbone'],
    function(_,Backbone){

        var SearchContent = Backbone.Model.extend({

        });

        var SearchContentCollection = Backbone.Collection.extend({
            model:SearchContent,
        });
        var SearchModelsMain = Backbone.Model.extend({
            urlRoot:'/api/models/search',
            parse : function(response){
                response.criteria = new SearchContentCollection(response.criteria);
                response.models = new SearchContentCollection(response.models);
                return response;
            },
        });
        return SearchModelsMain;
    });


