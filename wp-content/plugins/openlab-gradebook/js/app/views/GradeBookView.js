define(['jquery', 'backbone', 'underscore', 'views/StudentView', 'views/AssignmentView', 'views/EditStudentView', 'views/EditAssignmentView', 'models/Course'],
        function ($, Backbone, _, StudentView, AssignmentView, EditStudentView, EditAssignmentView, Course) {

            Backbone.pubSub = _.extend({}, Backbone.Events);

            var GradebookView = Backbone.View.extend({
                initialize: function (options) {
                    var self = this;
                    var _request = 0;
                    var currentScrollSize;
                    this.studentHeader = '';
                    this.scrollSize = 0;
                    this.resizeTimer;
                    this.xhrs = [];
                    this._subviews = [];
                    this.scrollObj = {};
                    this.options = options;
                    this.filter_option = "-1";
                    this.course = options.course;
                    this.renderControl = 0;
                    this.gradebook = options.gradebook;
                    this.listenTo(self.gradebook.students, 'add remove', self.handleAssignmentUpdates);
                    this.listenTo(self.gradebook.cells, 'add remove', self.handleAssignmentUpdates);
                    this.listenTo(self.gradebook.assignments, 'add remove', self.handleAssignmentUpdates);
                    this.listenTo(self.gradebook.assignments, 'change', self.handleAssignmentChanges);
                    this.listenTo(self.gradebook.assignments, 'change:sorted', self.sortByAssignment);

                    Backbone.pubSub.on('updateAverageGrade', this.updateAverageGrade, this);

                    this.initRender();
                    this.render();

                    $(window).on('resize', function (e) {

                        clearTimeout(this.resizeTimer);
                        this.resizeTimer = setTimeout(function () {

                            self.adjustCellWidths();

                        }, 250);

                    });

                    return this;
                },
                clearSubViews: function () {
                    var self = this;
                    _.each(self._subviews, function (view) {
                        view.close();
                    });
                    this._subviews = [];
                },
                events: {
                    'click button#add-student': 'addStudent',
                    'click button#download-csv': 'downloadCSV',
                    'click button#download-csv-mobile': 'downloadCSV',
                    'click button#add-assignment': 'addAssignment',
                    'click button#filter-assignments': 'filterAssignments',
                    'click [class^=gradebook-student-column-]': 'sortGradebookBy',
                },
                initRender: function(){

                    console.log('Init Render');

                    this.scrollSize = 0;
                    var self = this;
                    this.clearSubViews();
                    this.renderControl = 0;
                    var _x = _.map(self.gradebook.assignments.models, function (model) {
                        return model.get('assign_category').trim();
                    });
                    var _assign_categories = _.without(_.uniq(_x), "") || null;
                    var template = _.template($('#gradebook-interface-template').html());

                    var totalWeight = self.getTotalWeight();

                    var compiled = template({course: self.course, assign_categories: _assign_categories, role: this.gradebook.role, total_weight: totalWeight, assign_length: self.gradebook.assignments.length});
                    $('#wpbody-content').append(self.$el.html(compiled));
                    
                    var studentHeaderTemplate = _.template($('#gradebook-interface-template-student-header').html());
                    var compiledStudentHeader = studentHeaderTemplate({role: this.gradebook.role});
                    self.$el.find('#students-header tr').append(compiledStudentHeader);
                    this.studentHeader = compiledStudentHeader;

                    $('#filter-assignments-select').val(this.filter_option);

                    new ResizeSensor(jQuery('#an-gradebook-container #students-header'), function(){
                        console.log('table resize');
                        self.handleTableResize();

                    });

                    return this;
                },
                handleTableResize: function(){

                    this.adjustCellWidths();
                    if (typeof this.scrollObj.data !== 'undefined') {
                        var jsAPI = this.scrollObj.data('jsp');

                        if (typeof jsAPI !== 'undefined') {

                            currentScrollSize = $('#an-gradebook-container').width();

                            console.log('container widths after resize', parseInt(this.scrollSize), parseInt(currentScrollSize));

                                if(parseInt(this.scrollSize) !== parseInt(currentScrollSize)){
                                    console.log('reinitialize', jsAPI.getContentPane());
                                    this.scrollSize = currentScrollSize;
                                } else {

                                    var scrollContainerElem = $('#an-gradebook-container').closest('.jspContainer');

                                    var scrollContainerDims = {
                                        'height': scrollContainerElem.height()
                                    }
                
                                    console.log('scrollContainerDims', scrollContainerDims);
                
                                    scrollContainerElem.css({
                                        'max-height' : (scrollContainerDims.height + 29) + 'px'
                                    }); 
                                }

                        }
                    }

                },
                render: function () {
                    
                    //console.log('GradeBookView render');

                    var self = this;

                    switch (this.gradebook.sort_key) {
                        case 'cell':
                            $('#students').html('');
                            $('#students-pinned').html('');
                            $('#students-header tr').html(this.studentHeader);
                            _.each(this.sort_column, function (cell) {
                                var view = new StudentView({
                                    model: self.gradebook.students.get(cell.get('uid')), course: self.course, gradebook: self.gradebook, options: self.options
                                });
                                self._subviews.push(view);
                                $('#students').append(view.render('pinned', self.gradebook.assignments));
                            });
                            var y = self.gradebook.assignments.models;
                            y = _.sortBy(y, function (assign) {
                                return assign.get('assign_order');
                            });

                            _.each(this.sort_column, function (cell) {
                                var view = new StudentView({
                                    model: self.gradebook.students.get(cell.get('uid')), course: self.course, gradebook: self.gradebook, options: self.options
                                });
                                self._subviews.push(view);
                                $('#students-pinned').append(view.render('static', self.gradebook.assignments));
                            });
                            var y = self.gradebook.assignments.models;
                            y = _.sortBy(y, function (assign) {
                                return assign.get('assign_order');
                            });

                            _.each(y, function (assignment) {
                                var view = new AssignmentView({
                                    model: assignment, course: self.course, gradebook: self.gradebook
                                });
                                self._subviews.push(view);
                                $('#students-header tr').append(view.render());
                            });
                            break;
                        case 'student':
                            $('#students').html('');
                            $('#students-pinned').html('');
                            $('#students-header tr').html(this.studentHeader);
                            _.each(this.gradebook.sort_column.models, function (student) {
                                var view = new StudentView({model: student, course: self.course, gradebook: self.gradebook, options: self.options});
                                self._subviews.push(view);
                                $('#students').append(view.render('pinned', self.gradebook.assignments));
                            });
                            _.each(this.gradebook.sort_column.models, function (student) {
                                var view = new StudentView({model: student, course: self.course, gradebook: self.gradebook, options: self.options});
                                self._subviews.push(view);
                                $('#students-pinned').append(view.render('static', self.gradebook.assignments));
                            });
                            var y = self.gradebook.assignments;
                            y = _.sortBy(y.models, function (assign) {
                                return assign.get('assign_order');
                            });
                            _.each(y, function (assignment) {
                                var view = new AssignmentView({model: assignment, course: self.course, gradebook: self.gradebook});
                                self._subviews.push(view);
                                $('#students-header tr').append(view.render());
                            });
                            break;
                    }

                    this.filterAssignments();

                    if(this.scrollSize === 0){
                        console.log('sorting');
                        this.scrollSize = this.$el.find('#an-gradebook-container').width();
                        this.gradebook.sort_key = 'student';
                        this.gradebook.students.sort_key = 'last_name';
                        this.gradebook.students.sort_direction = 'desc';
                        this.gradebook.students.sort();
                        this.render();

                        this.scrollObj = $('.table-wrapper .scrollable')
                            .bind('jsp-initialised', this.calculateScrollBarPosition)
                            .jScrollPane();

                        $('[data-toggle="tooltip"]').tooltip();
                    }

                    return this;
                },
                filterAssignments: function () {
                    var _x = $('#filter-assignments-select').val();
                    this.filter_option = _x;
                    var _toHide = this.gradebook.assignments.filter(
                            function (assign) {
                                return assign.get('assign_category') !== _x;
                            }
                    );
                    var _toShow = this.gradebook.assignments.filter(
                            function (assign) {
                                return assign.get('assign_category') === _x;
                            }
                    );

                    if (_x === "-1") {
                        this.gradebook.assignments.each(function (assign) {
                            assign.set({visibility: true});
                        });
                    } else {
                        _.each(_toHide, function (assign) {
                            assign.set({visibility: false});
                        });
                        _.each(_toShow, function (assign) {
                            assign.set({visibility: true});
                        });
                    }

                    if (typeof this.scrollObj.data !== 'undefined') {
                        var jsAPI = this.scrollObj.data('jsp');

                        if (typeof jsAPI !== 'undefined') {
                            jsAPI.reinitialise();
                        }
                    }
                },
                adjustCellWidths: function () {

                    var pinnedTable = $('.pinned .table');
                    var columnsToAdjust = pinnedTable.find('.adjust-widths');

                    if (columnsToAdjust.lenght < 1) {
                        return false;
                    }

                    var pinnedTable_w = pinnedTable.width();

                    columnsToAdjust.each(function () {

                        var thisElem = $(this);
                        var target_w = thisElem.data('targetwidth');

                        var target_pct = (target_w / pinnedTable_w) * 100;
                        thisElem.css({
                            'width': target_pct + '%'
                        });

                    });


                },
                calculateScrollBarPosition: function (event, isScrollable) {

                    var targetTable = $('#an-gradebook-container');
                    var scrollContainerElem = targetTable.closest('.jspContainer');
                    $('#an-gradebook-container').css('width', 'auto');

                    if (targetTable.height() < 500) {

                        console.log('targetTable.height', targetTable.height());

                        var targetTable_padding = 500 - targetTable.height();

                        scrollContainerElem.css({
                            'padding-bottom': targetTable.height() + targetTable_padding + 'px',
                        });
                        scrollContainerElem.find('.jspHorizontalBar').css({
                            'bottom': (targetTable_padding - 18) + 'px'
                        });
                    }

                    var scrollContainerDims = {
                        'height': scrollContainerElem.height()
                    }

                    console.log('scrollContainerDims', scrollContainerDims);

                    scrollContainerElem.css({
                        'height' : (scrollContainerDims.height + 29) + 'px'
                    });

                },
                addAssignment: function (ev) {
                    var view = new EditAssignmentView({course: this.course, gradebook: this.gradebook});
                },
                addStudent: function (ev) {
                    var view = new EditStudentView({course: this.course, gradebook: this.gradebook});
                    $('body').append(view.render());
                },
                downloadCSV: function (e) {
                    e.preventDefault();

                    this.course.export2csv();

                },
                checkStudentSortDirection: function () {
                    if (this.gradebook.students.sort_direction === 'asc') {
                        this.gradebook.students.sort_direction = 'desc';
                    } else {
                        this.gradebook.students.sort_direction = 'asc';
                    }
                },
                sortGradebookBy: function (ev) {
                    var column = ev.target.className.replace('gradebook-student-column-', '');
                    this.gradebook.sort_key = 'student';
                    this.gradebook.students.sort_key = column;
                    this.checkStudentSortDirection();
                    this.gradebook.students.sort();
                    this.render();
                },
                sortByAssignment: function (ev) {
                    var x = this.gradebook.cells.where({amid: parseInt(ev.get('id'))});
                    this.sort_column = _.sortBy(x, function (cell) {
                        if (ev.get('sorted') === 'asc') {
                            return cell.get('assign_points_earned');
                        } else {
                            return -1 * cell.get('assign_points_earned');
                        }
                    });
                    this.gradebook.sort_key = 'cell';
                    this.render();
                },
                handleAssignmentUpdates: function (ev){

                    //console.log('handleAssignmentUpdates', ev, ev._events)  ;

                    this.initRender();
                    this.render();
                    
                },
                handleAssignmentChanges: function(ev){
                    //console.log('handleAssignmentChanges', ev);
                    this.render();
                
                },
                close: function () {
                    this.clearSubViews();
                    _.map(this.xhrs, function (xhr) {
                        xhr.abort()
                    });
                    this.remove();
                },
                getTotalWeight: function () {
                    var self = this;

                    var totalWeight = 0;
                    _.each(self.gradebook.assignments.models, function (assignment) {

                        totalWeight = totalWeight + parseFloat(assignment.get('assign_weight'));

                    });

                    var message = '';

                    if (totalWeight >= 100) {
                        message += '<strong>Percentage of Total Grade:</strong> ' + totalWeight +  '% of the total grade has been designated. Any assignments that do not have a set percentage will not be included in the average calculation.';
                    } else if (totalWeight < 100) {
                        message += '<strong>Percentage of Total Grade:</strong> ' + totalWeight +  '% of the total grade has been designated. The rest of the grade average will be calculated evenly.';
                    }

                    if(self.gradebook.role === 'instructor') {
                        message += ' Percentages can be edited in the dropdown menus.';
                    }

                    return message;

                },
                updateAverageGrade: function (data) {
                    var studentID = parseInt(data.uid);
                    var target = $('#average' + studentID);
                    target.html(data.current_grade_average);
                    
                    var index = 0;
                    _.each(this.gradebook.students.models, function (student) {
                        if(parseInt(student.get('id')) === studentID){
                            student.set({ current_grade_average: data.current_grade_average }, { silent: true });
                        }
                        index++;
                    });

                    target.attr('title', data.current_grade_average)
                            .tooltip('fixTitle');

                }
            });
            return GradebookView;
        });