define([
	"jquery",
	"backbone",
	"underscore",
	"models/User",
	"models/UserList",
	"bootstrap3-typeahead"
], function($, Backbone, _, User, UserList, typeahead) {
	var newGradebook = {};
	var error = "";
	var uploadModal = Backbone.View.extend({
		id: "upload-csv",
		className: "modal fade",
		events: {
			"shown.bs.modal": "renderUploader",
			"hidden.bs.modal": "editCancel"
		},
		initialize: function(options) {
			this.course = options.course;
			this.gradebook = options.gradebook;
			return this;
		},
		render: function() {
			var self = this;
			var template = _.template($("#upload-csv").html());
			var compiled = template({});
			self.$el.html(compiled);
			this.$el.modal("show");
			return self.el;
		},
		renderUploader: function() {
			var self = this;

			$("#upload-csv-input")
				.fileinput({
					uploadUrl:
						oplbGradebook.ajaxURL +
						"?action=oplb_gradebook_upload_csv&nonce=" +
						oplbGradebook.nonce +
						"&gbid=" +
						this.course.get("id"),
					maxFileCount: 1,
					hideThumbnailContent: true,
					msgUploadThreshold: "Adding to Gradebook..."
				})
				.on("fileselect", function(e, numfiles, label) {
					error = "";
				})
				.on("fileuploaded", function(e, params) {
					$(".file-preview-status.text-center.text-success").html(
						params.response.message
					);
					newGradebook = params.response.content;
					self.updateGradebook();
				})
				.on("filecleared", function(e) {
					if (error !== "") {
						self.$el.find(".file-input .kv-fileinput-error").after(error);
					}
				})
				.on("fileuploaderror", function(e, params) {
					error = params.response.error;
					$("#upload-csv-input").fileinput("clear");
				});
		},
		editCancel: function() {
			this.$el.data("modal", null);
			this.remove();
			Backbone.pubSub.trigger("closeUploadCSV", newGradebook);
			return false;
		},
		updateGradebook: function() {
			console.log("self.newGradebook in updateGradebook", newGradebook);
			Backbone.pubSub.trigger("newGradebookCSV", newGradebook);
		}
	});

	return uploadModal;
});
