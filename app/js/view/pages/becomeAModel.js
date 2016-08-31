define([
    'jquery',
    'underscore',
    'backbone',
    'jquery.ui.widget',
    'js/vendor/jquery.iframe-transport',
    'js/vendor/jquery.fileupload'
],function($,_,Backbone){

    var BecomeView = Backbone.View.extend({
        className:'pagecontainer',
        id:"pagecontainer",
        initialize : function(){
            var self = this;
            this.template = Templates.getTemplate("template-pages-becomeamodel");
            NProgress.inc();
            self.menulink = $('a.linkmenu[href="/become-a-model"]');

            $.get('/api/become-a-model',function(response){
                NProgress.inc();
                self.content = response;
                self.render();
            });

        },
        render : function(){
            backgroundview.clearBackground();
            var self = this;

            this.$el.html(this.template({'content' : this.content}));
            NProgress.inc();
            self.$('.modelpagemaintitle span').kern();
            self.menulink.addClass('linkmenuselected');

            this.$('#attachphoto').click(function(event){
                event.preventDefault();
                $('#fileupload').trigger('click');
            });
            $('#fileupload').fileupload({
                dataType: 'json',
                done: function (e, data) {

                    var result = data.result;
                    if (result.code == "success"){

                        self.$('.becomeimageinner').fadeOut(function(){
                            self.$('.becomeimageinner').attr('src','/'+result.imagepath);
                            $('<img>').load(function(){
                                self.$('.becomeimageinner').fadeIn();
                            }).attr('src','/'+result.imagepath);
                        });
                        self.$('.imagenameholder').html(result.origname);

                        var listfield = self.$('#filelistfield');
                        var value = listfield.val();
                        if (value!=='') value+='|::|';
                        value+=result.imagepath;
                        listfield.val(value);
                    }

                }
            });

            this.$('.formbecomemodellegend').each(function(index,elem){
                var element = $(this);
                element.data('originalvalue',element.html());
                element.attr('id','legend'+element.html().trim().toLowerCase());
            });

            this.$el.fadeIn();
            this.$('.becomeimageinner').each(function(){
                var elemImage = $(this);
                $('<img>').load(function(){ elemImage.fadeIn();}).attr('src',elemImage.attr('src'));
            });
            NProgress.done();
            this.centerRequirements();
            return this.$el;
        },


        centerRequirements : function(){
            //(19+6+15)*4
            //margin-top -6px


            var height = this.$('.requirementstextcontainer').height();
            var margin = ((19+6+15)*4 - height)/2;
            if (margin<6) margin = 6;
            this.$('.requirementstextcontainer').css({
                'margin-top' : margin-6+'px',
                'margin-bottom' : margin+'px'
            });

        },
        events : {
            "click #becomeamodeltitle" : "goToIntro",
            "click #sendform" : "submitForm"
        },

        submitForm : function(event){
            var self= this;
            event.preventDefault();
            $(event.currentTarget).css('opacity',0.2);
            $.post('/api/become-a-model/send',
                self.$('#becomeamodel').serialize(),
                function(response){
                    self.$('.formbecomemodellegend').each(function(){
                        var elem = $(this);
                        elem.removeClass('formbecomemodellegenderror');
                        elem.html(elem.data('originalvalue'));
                    })
                    if (response.code!="success"){


                        for (var key in response.message){
                            var elem = self.$('#legend'+key.trim().toLowerCase());
                            elem.addClass('formbecomemodellegenderror');

                            for (var keyk in response.message[key]){
                                elem.html(response.message[key][keyk]);
                                break;
                            }
                        }
                    } else {
                        $('.confirmmessagesend').html('Submission sent.');
                        $('input').val('');

                    }
                    $(event.currentTarget).css('opacity',1);
                },
                'json'
            );
        },

        goToIntro : function(event){
            event.preventDefault();
            router.navigate('/',{trigger : true});
        },

        close : function(){
            this.menulink.removeClass('linkmenuselected');
            this.$el.remove();
        },

        show : function() {

        }
    })

    return BecomeView ;

});