
var App = {};
var Templates = {
    templates : [],
    getTemplate:function(name){
        if (name in this.templates) return this.templates[name];
        var templatecontent = arrayTemplates[name];
        this.templates[name] = _.template(templatecontent());
        return this.templates[name];
    }
};








