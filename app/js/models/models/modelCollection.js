define(['underscore','backbone'],
    function(_,Backbone){

        var ModelContent = Backbone.Model.extend({

        });

        var ModelContentCollection = Backbone.Collection.extend({
            model:ModelContent,
        });
        var ModelMain = Backbone.Model.extend({
            initialize :function(options){
                this.urlRoot = '/api/'+options.path;

            },
            urlRoot:'/api',
            parse : function(response){
                response.content = new ModelContentCollection(response.content);
                return response;
            },
        });
        return ModelMain;
    });


