define(['underscore','backbone'],
    function(_,Backbone){

        var TypeContent = Backbone.Model.extend({

        });

        var TypeContentCollection = Backbone.Collection.extend({
            model:TypeContent,
        });
        var TypeModel = Backbone.Model.extend({
            initialize :function(options){
                this.urlRoot = '/api/'+options.path;

            },
            urlRoot:'/api/intro',
            parse : function(response){
                response.content = new TypeContentCollection(response.content);
                return response;
            },
        });
        return TypeModel;
    });
