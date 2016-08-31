define(['underscore','backbone'],
    function(_,Backbone){

        var IntroContent = Backbone.Model.extend({

        });

        var IntroContentCollection = Backbone.Collection.extend({
            model:IntroContent,
        });
        var IntroMain = Backbone.Model.extend({
            initialize :function(options){
                if ((options)&&(options.mobile)){
                    this.urlRoot = '/api/intro/mobile';
                }
            },
            urlRoot:'/api/intro',
            parse : function(response){
                response.content = new IntroContentCollection(response.content);
                return response;
            },
        });
        return IntroMain;
    });
