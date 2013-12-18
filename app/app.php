<?php

/*
 * Main app handler (works as a Controller)
 *
 * (c) 2013 Steve Chae
 */

require_once("models/TasksModel.php");

// Model
$tasksModel = new TasksModel();

// HTTP request method (GET, POST, DELETE or PUT)
$httpMethod = $_SERVER['REQUEST_METHOD'];

switch ($httpMethod) {
    case 'GET':
        break;
    case 'POST':
        // Receive and decode JSON payload for addTask operation
        $requestObj = json_decode(file_get_contents('php://input'));
        $actionType = $requestObj->action;

        /*
         * Ideally, delete task request should be made with DELETE verb.
         * However, the server will first have to configured to non-GET or
         * POST HTTP methods such as PUT and DELETE
         */
        if ( strcmp($actionType, 'add') == 0 ) {
            $data = array(
                'name'      => $requestObj->name,
                'time'      => $requestObj->time,
                'date'      => '2013-12-18',
                'status'    => 'A'
            );
            // Add a new task
            $tasksModel->addTask($data);
        } else {
            $tasksModel->deleteTask($requestObj->id);
        }
        break;
    case 'DELETE':
        // This HTTP method be allowed by server
        break;
    case 'PUT':
        // This HTTP method be allowed by server
        break;
}

// Output data as JSON
$output = $tasksModel->getTasks();

echo json_encode($output);

?>
