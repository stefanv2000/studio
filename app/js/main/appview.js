define([
  'jquery',
], function($){
return function AppView(){
	  this.showView = function showView(view) {
	    if (this.currentView){
	      this.currentView.close();
	    }
	 
	    this.currentView = view;
	  };
	 return this;
	};	
});

