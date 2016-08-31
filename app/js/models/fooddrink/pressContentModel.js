define(['underscore','backbone'],
    function(_,Backbone){

        var PressContent = Backbone.Model.extend({

        });

        var PressContentCollection = Backbone.Collection.extend({
            model:PressContent,
        });
        var PressContentMain = Backbone.Model.extend({
            initialize :function(options){
                this.urlRoot = '/api/'+options.path;
            },
            urlRoot:'/api',
            parse : function(response){
                response.content = new PressContentCollection(response.content);
                return response;
            },
        });
        return PressContentMain;
    });


