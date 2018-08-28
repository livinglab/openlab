oplbgb_require_config = {
	//By default load any module IDs from js/lib
	paths: {
		models: "app/models",
		views: "app/views",
		router: "app/router",
		jquery: oplbGradebook.depLocations.jquery,
		"jquery-ui": oplbGradebook.depLocations.jqueryui,
		backbone: oplbGradebook.depLocations.backbone,
		underscore: oplbGradebook.depLocations.underscore,
		bootstrap: "lib/bootstrap/js/bootstrap.min",
		chart: "lib/chart/Chart.min",
		"bootstrap3-typeahead": "lib/bootstrap3-typeahead/bootstrap3-typeahead.min",
		jscrollpane: "lib/jscrollpane/jscrollpane.dist"
	},
	shim: {
		bootstrap: {
			deps: ["jquery"]
		}
	}
};

require.config(oplbgb_require_config);

define("jquery", [], function() {
	return jQuery;
});

define("jquery-ui", [], function() {
	return jQuery;
});

define("backbone", [], function() {
	return Backbone;
});

define("underscore", [], function() {
	return _;
});

window.oplbGlobals = window.oplbGlobals || {};
window.oplbGlobals.total_weight = 0;

var oldBackboneSync = Backbone.sync;

// Override Backbone.Sync
Backbone.sync = function(method, model, options) {
    let status = jQuery('#savingStatus');

    if (status.length) {
        status.removeClass('hidden');
    }

	//globally add nonce
	options.url = model.url() + "&nonce=" + oplbGradebook.nonce;

	return oldBackboneSync.apply(this, [method, model, options]);

};

var currentSync = Backbone.sync;

var loggingSync = function(method, model, options){
    var promise = currentSync(method, model, options);
    let status = jQuery('#savingStatus');

    console.log('promise in Backbone.sync', promise);
    
	promise.done(function() {
        console.log('done ajax', this);
        if (status.length) {
            status.addClass('hidden');
        }
	});
	promise.fail(function() {
        console.log('problem ajax', this);
        if (status.length) {
            status.addClass('hidden');
        }
    });
    
	return promise;
}
Backbone.sync = loggingSync;

require(["jquery", "router/GradeBookRouter", "bootstrap"], function(
	$,
	GradeBookRouter,
	bootstrap
) {
	$.fn.serializeObject = function() {
		var o = {};
		var a = this.serializeArray();
		$.each(a, function() {
			if (o[this.name] !== undefined) {
				if (!o[this.name].push) {
					o[this.name] = [o[this.name]];
				}
				o[this.name].push(this.value || "");
			} else {
				o[this.name] = this.value || "";
			}
		});
		return o;
	};
	var App = new GradeBookRouter();
});
