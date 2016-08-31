define(['underscore','backbone'],
    function(_,Backbone){

        var ProfileIntroContent = Backbone.Model.extend({

        });

        var ProfileIntroContentCollection = Backbone.Collection.extend({
            model:ProfileIntroContent,
        });
        var ProfileIntroMain = Backbone.Model.extend({
            initialize :function(options){
                this.urlRoot = '/api/'+options.path;
            },
            urlRoot:'/api',
            parse : function(response){
                response.content = new ProfileIntroContentCollection(response.content);
                return response;
            },
        });
        return ProfileIntroMain;
    });

