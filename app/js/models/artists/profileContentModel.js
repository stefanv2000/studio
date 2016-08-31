define(['underscore','backbone'],
    function(_,Backbone){

        var ProfileContentModel = Backbone.Model.extend({
            initialize :function(options){
                this.urlRoot = '/api/'+options.path;
            },
            urlRoot:'/api',
        });
        return ProfileContentModel;
    });

