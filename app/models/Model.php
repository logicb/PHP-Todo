<?php
/*
 * Main Model class
 *
 * (c) 2013 Steve Chae
 */

class Model {

    // DB handler
    public $dbh;

    public function __construct() {

        // Replace the arguements with correct DB configurations
        $this->dbh = new mysqli('localhost', 'root', '123', 'test');
        if ( $this->dbh->connect_errno ) {
            echo 'Failed to connect to MySQL: (' . $this->dbh->connect_errno . ') ' . $this->dbh->connect_error;
        }
    }
}

?>
