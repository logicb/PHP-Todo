<?php
require_once('Model.php');

class TasksModel extends Model {

    /*
     * Add a new task
     */
    public function addTask($data) {

        // Sanity
        if ( !($data and strcmp(gettype($data), 'array') == 0) ) {
            return FALSE;
        }

        // Prepare a query that inserts a new task
        if ( !($stmt = $this->dbh->prepare("INSERT INTO tasks (name, time, date, status) VALUES (?,?,?,?)")) ) {
            echo 'Prepare failed: (' . $this->dbh->errno . ') ' . $this->dbh->error;
            return FALSE;
        }

        if ( !$stmt->bind_param('ssss', $data['name'], $data['time'], $data['date'], $data['status']) ) {
            echo 'Binding parameters failed: ('. $stmt->errno . ') ' . $stmt->error;
            return FALSE;
        }
        
        if ( !$stmt->execute() ) {
            echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
            return FALSE;
        }

        return TRUE;
    }

    /*
     * Delete a task
     */
    public function deleteTask($id) {

        // Sanity
        if ( !$id ) {
            return FALSE;
        }

        // Prepare a query that deactivates a task
        if ( !($stmt = $this->dbh->prepare("UPDATE tasks SET status='D' WHERE taskID=?")) ) {
            echo 'Prepare failed: (' . $this->dbh->errno . ') ' . $this->dbh->error;
            return FALSE;
        }

        if ( !$stmt->bind_param('i', $id) ) {
            echo 'Binding parameters failed: ('. $stmt->errno . ') ' . $stmt->error;
            return FALSE;
        }
        
        if ( !$stmt->execute() ) {
            echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
            return FALSE;
        }

        return TRUE;
    }

    /*
     * Get all active tasks stored for today
     */
    public function getTasks() {

        // Prepare a query that gets all today's active and non-expired tasks
        if ( !($stmt = $this->dbh->prepare("SELECT taskID, name, time, status FROM tasks WHERE date=CURDATE() AND status='A' AND time > NOW() ORDER BY time ASC")) ) {
             echo 'Prepare failed: (' . $this->dbh->errno . ') ' . $this->dbh->error;
            return array();
        }

        if ( !$stmt->execute() ) {
            echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
            return array();
        }

        $res = $stmt->get_result();

        // Format output as a list of associative arrays
        $outputArray = array();
        while ( $row = $res->fetch_assoc() ) {
            // strip the second (SS) part from the HH:MM::SS formatted string
            $row['time'] = substr($row['time'], 0, 5);
            array_push($outputArray, $row);
        }

        return $outputArray;
    }
}

?>
