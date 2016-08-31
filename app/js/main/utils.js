var Utils = {
	scaleHeight:function(originalWidth,originalHeight,width){
		if (originalWidth<width) return [originalWidth,originalHeight];
		var ratio = width/originalWidth;
		return [width,originalHeight*ratio];
	},
	/**
	 * 
	 */
	scaleToFill:function(containerSize,originalSize){
		var ratiox = originalSize[0]/containerSize[0];
		var ratioy = originalSize[1]/containerSize[1];
		var returnvalue = {
				size : {width:containerSize[0],
						height:containerSize[1],
						},
				margins : {top:0,
						left:0,
					},				
		};
		
		if (ratiox > ratioy) {//resize width
			returnvalue.size.width = Math.floor(originalSize[0]/ratioy);
		} else { //resize height
			returnvalue.size.height = Math.floor(originalSize[1]/ratiox);
		}
		
		returnvalue.margins.left = (containerSize[0] -returnvalue.size.width)/2;
		returnvalue.margins.top = (containerSize[1] -returnvalue.size.height)/2;
		return returnvalue;
	},
	scaleToFillObject:function(containerSize,originalSize,object){
		var scaled = Utils.scaleToFill(containerSize, originalSize);
		object.css({
			width:scaled.size.width+"px",
			height:scaled.size.height+"px",
			top:scaled.margins.top+"px",
			left:scaled.margins.left+"px",
		});
	},

	scaleToFit:function(containerSize,originalSize){
		var scale = Math.min( containerSize[0]/originalSize[0], containerSize[1]/originalSize[1]);
		if ((originalSize[0]<containerSize[0])&&(originalSize[1]<containerSize[1])) {
			scale=1;
		}
		var returnvalue = {
			size : {width:originalSize[0]*scale,
				height:originalSize[1]*scale,
			},
			margins : {top:0,
				left:0,
			},
		};

		returnvalue.margins.left = (containerSize[0] -returnvalue.size.width)/2;
		returnvalue.margins.top = (containerSize[1] -returnvalue.size.height)/2;

		return returnvalue;
	},
	/**
	 * 
	 */
	scaleToFit1:function(containerSize,originalSize){
		var ratiox = originalSize[0]/containerSize[0];
		var ratioy = originalSize[1]/containerSize[1];
		var returnvalue = {
				size : {width:containerSize[0],
						height:containerSize[1],
						},
				margins : {top:0,
						left:0,
					},				
		};
		
		if ((ratiox<1)&&(ratioy<1)) {//don't scale up
			returnvalue.size ={width:originalSize[0],
					height:originalSize[1],
			};
		} else
		
		if (ratiox < ratioy) {//resize width
			returnvalue.size.width = Math.floor(originalSize[0]/ratioy);
		} else { //resize height
			returnvalue.size.height = Math.floor(originalSize[1]/ratiox);
		}
		
		returnvalue.margins.left = (containerSize[0] -returnvalue.size.width)/2;
		returnvalue.margins.top = (containerSize[1] -returnvalue.size.height)/2;
		return returnvalue;
	},
	scaleToFitObject:function(containerSize,originalSize,object){
		var scaled = Utils.scaleToFit(containerSize, originalSize);
		object.css({
			width:scaled.size.width+"px",
			height:scaled.size.height+"px",
			top:scaled.margins.top+"px",
			left:scaled.margins.left+"px",
		});
		return scaled;
	},	
	
	changeTitle: function(title){
		document.title = ""+title;
	},

	getWindowHeight : function(){
		var height = $(window).height()
		if (height<700) height = 700;
		return height;
	},

	getWindowWidth : function(){
		var width = $(window).width()
		if (width<1170) width = 1170;
		return width;
	},


	addPrevNextPostArticleLinks : function(arrayLinks) {
		$('.previousnextpostpress').remove();
		var previousLink = null;
		if (arrayLinks['previousarticle']!=''){
			previousLink = $('<a href="'+arrayLinks['previousarticle']+'" class="portfoliomenuitem seemoreprofessionals showonfd previousnextpostpress" style="display: inline-block">prev article</a>');

		}

		var nextLink = null;
		if (arrayLinks['nextarticle']!=''){
			nextLink = $('<a href="'+arrayLinks['nextarticle']+'" class="portfoliomenuitem seemoreprofessionals showonfd previousnextpostpress" style="display: inline-block">next article</a>');

		}

		var separator = null;
		if ((nextLink!==null)||(previousLink!=null)){
			separator = $('<img style="margin-left: 0px; display: inline-block;" class="menuseparator showonfd previousnextpostpress" src="/images/05_portfolio_menu_line.png">');
		}

		var container = $('.portfoliolayoutmenucontainer');
		if (separator!=null) container.append(separator);
		if (previousLink!=null) container.append(previousLink);
		if (nextLink!=null) container.append(nextLink);

	},

	removePrevNextPostArticleLinks : function() {
		//$('.previousnextpostpress').remove();
	},


};