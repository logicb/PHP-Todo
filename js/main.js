/*!
 * Steve's ToDo App - created with AngularJS
 *
 * (c) 2013 Steve Chae
 */

// Steve's ToDo app AngularJS module
var steveTodoApp = angular.module('steveTodoApp', []);

steveTodoApp.controller('SteveTodoCtrl', function($scope, $http) {

    // Get today's date to be diplayed
    var date = new Date();
    $scope.todayString = date.toDateString() + ' (Today)';

    // Get the list of active 
    $http.get('http://localhost/todo-php/app/app.php').success(function(data) {
        $scope.tasks = data;
        console.log($scope.tasks);
    });

    /*
     * Add a task
     *
     * The function sends an Add Task request to the server with user input
     */
    $scope.addTask = function() {

        // Get the user inputs
        var newTaskName = $('#new-task-name').val();
        var newTaskTime = $('#new-task-time').val();

        // Input validation object
        var fc = new FieldCheck();
        fc.checkName(newTaskName);
        fc.checkTime(newTaskTime);

        // Failed input validation
        if ( fc.getErrors() ) {
            alert('Please check the following fields:\n' + fc.getErrors());
            return false;
        }

        var newTaskData = {
            'action'    : 'add', // no need to specify if the server allows DELETE HTTP method as deleteTask will use DELETE instead of POST
            'name'      : newTaskName,
            'time'      : newTaskTime
        }

        // Request adding a new task
        $http.post('http://localhost/todo-php/app/app.php', newTaskData).success(function(data) {
            $scope.tasks = data;
        });
    }

    /*
     * Delete a task by the ID
     *
     * The function sends a Delete Task request to the server
     * @param id task ID of the task to be deleted
     */
    $scope.deleteTask = function(id) {

        $http.post('http://localhost/todo-php/app/app.php', {'action' : 'delete', 'id' : id}).success(function(data) {
            $scope.tasks = data;
        });

        // DELETE HTTP method should be allowed by the server for the following to work
        //$http.delete('http://localhost/todo-app/app/app.php', {'id' : id}).success(function(data) {
        //    $scope.tasks = data;    
        //});
    }
});


/*
 * Field check object
 */
function FieldCheck() {

    // Stored error messages
    this.errorMsgs = '';
}

/*
 * Check the user input provided for the Name field.
 */
FieldCheck.prototype.checkName = function(str) {

    if ( !str ) {
        this.errorMsgs += '- Name field cannot be empty\n';
        return false;
    }

    var nameRegex = /^[\w\s]+$/;    // allow any word and space
    if ( !nameRegex.test(str) ) {
        this.errorMsgs += '- Input provided for Name is invalid. Only alphanumeric characters and spaces are allowed.\n';
        return false;
    }

    // Do not allow input string longer than 30 chacters
    if ( str.length > 30 ) {
        this.errorMsgs += '- The provided name is too long. It should consist of 30 characters.\n';
        return false;
    }

    return true;
}

/*
 * Check the user input provided for the Time field.
 */
FieldCheck.prototype.checkTime = function(str) {

    if ( !str ) {
        this.errorMsgs += '- Time field cannot be empty\n';
        return false;
    }

    // check the format of the input
    var timeRegex = /^\d\d:\d\d$/;
    if ( !timeRegex.test(str) ) {
        this.errorMsgs += '- Input provided for Time is invalid. Only a string of the format HH:MM (24-hour format).\n';
        return false;
    }

    // check the proper numbers for hours and minutes
    var hour = parseInt(str.substr(0,2));
    var minute = parseInt(str.substr(3,2));
    if ( hour > 24 || hour < 1) {
        this.errorMsgs += '- Input provided for Time is invalid. Only a string of the format HH:MM (24-hour format).\n';
        return false;
    }
    if ( minute > 60 || minute < 0 ) {
        this.errorMsgs += '- Input provided for Time is invalid. Only a string of the format HH:MM (24-hour format).\n';
        return false;
    }

    // check if the time has already expired
    var date = new Date();
    var curTime = date.toTimeString().substr(0,8);
    var fullInputTime = str + ':00';
    if ( Date.parse('01/01/2014 ' + curTime) > Date.parse('01/01/2014 ' + fullInputTime) ) {
        this.errorMsgs += '- The Time specified has alaready expired.\n';
        return false;
    }

    return true;
}

/*
 * Get error messages stored in this object.
 */
FieldCheck.prototype.getErrors = function() {

    return this.errorMsgs;
}
