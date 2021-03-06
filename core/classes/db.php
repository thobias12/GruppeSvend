<?php
/**
 * Description of Db Class
 *
 * Database connection and query handles with defined credentials
 * Credentials must be valued in the class constructor
 * Use method query for insert, update and delete queries
 * Use method fetch_array for row selecting
 *
 * Example of class initialize
 * $db = new db;
 *
 * Example for use of query:
 * $params = array($title,$id);
 * $sql = "UPDATE table SET title = ? WHERE id = ?";
 *
 * Example for use of fetch_array:
 * $params = array($value1,$value2);
 * $sql = "SELECT [*, fieldnames] FROM table WHERE fieldname = ? AND fieldname2 = ?";
 * $array = $db->fetch_array($sql,$params);
 *
 * @author Heinz K, Nov 2016
 */
class db {

    /* Setting properties */
    protected $db;
    protected $dbhost;
    protected $dbuser;
    protected $dbpassword;
    protected $dbname;
    private $sql;
    private $stmt;
    private $result;
    private $row;

    /**
     * Class constructor - sets db credentials
     */
    public function __construct() {
        $this->db = "";
        $this->dbhost = "";
        $this->dbuser = "";
        $this->dbpassword = "";
        $this->dbname = "";
        $this->sql = "";
        $this->stmt = "";
        $this->result = "";
        $this->row = array();
    }

    /**
     * DB Connection Error
     * Writes error on connection fail
     */
    public function connecterror() {
        echo "DB ERROR: " . mysqliconnecterror();
        exit();
    }

    /**
     * DB Method Statement Error
     * Writes error on query fail
     */
    public function error() {
        echo "STMT ERROR: " . $this->db->error;
        exit();
    }

    /**
     * DB Connect Method
     * Establish a connection to a database
     */
    public function connect() {
        @$this->db = new mysqli($this->dbhost, $this->dbuser, $this->dbpassword,$this->dbname);

        if (mysqli_connect_errno())
        {
            $this->connecterror();
        }
        /* BUG FIX: Sets the connection charset to fix danish letter bug */
        mysqli_set_charset($this->db,"utf8");
    }

    /**
     * DB Query
     * Send a SQL query with or without parametres
     * @param string $sql
     * @param array $params
     * @param int $sanitize
     */
    public function query($sql, $params = NULL, $sanitize = TRUE) {

        $this->sql = $this->sanitize($sql,$sanitize);

        /* Exit on error if statement fails and */
        if(!$this->db->prepare($this->sql)) {
            $this->error();
        } else {
            $this->stmt = $this->db->prepare($this->sql);
        }

        if(is_array($params)) {
            $this->bindparam($params);
        }

        if(!$this->stmt->execute()) {
            $this->error();
        }
        $this->stmt->reset();
        $this->stmt->close();
    }

    /**
     * DB Fetch Array
     * Send a SQL select query with or without parametres
     * and returns an array with given values
     * @param string $sql
     * @param array $params
     * @param int $sanitize
     * @return array Returns a dimensioned array with selected rows and fields
     */
    public function fetch_array($sql, $bindparams = NULL, $sanitize = TRUE, $useKeyType = MYSQLI_ASSOC) {

        $this->sql = $this->sanitize($sql,$sanitize);

        /* Exit on error if statement fails and */
        if(!$this->db->prepare($this->sql)) {
            $this->error();
        } else {
            $this->stmt = $this->db->prepare($this->sql);
        }

        if(!empty($bindparams)) {
            $this->bindparam($bindparams);
        }

        if(!$this->stmt->execute()) {
            $this->error();
        } else {
            $this->result = $this->stmt->get_result();
            return $this->row = $this->result->fetch_all($useKeyType);
        }
        $this->stmt->free_result();
        $this->stmt->close();
    }

    /**
     * DB Fetch Value
     * Fetches a single value
     * @param type $sql
     * @param type $bindparams
     * @param type $sanitize
     * @return type
     */
    public function fetch_value($sql, $bindparams = NULL, $sanitize = TRUE) {

        $this->sql = $this->sanitize($sql,$sanitize);

        /* Exit on error if statement fails and */
        if(!$this->db->prepare($this->sql)) {
            $this->error();
        } else {
            $this->stmt = $this->db->prepare($this->sql);
        }

        if(is_array($bindparams)) {
            $this->bindparam($bindparams);
        }

        if(!$this->stmt->execute()) {
            $this->error();
        } else {
            $this->result = $this->stmt->get_result();
            $this->stmt->store_result();
            $this->row = $this->result->fetch_all();
            if(count($this->row) > 0) {
                $this->row = call_user_func_array("array_merge", $this->row);
                return reset($this->row);
            }
        }
        $this->stmt->free_result();
        $this->stmt->close();
    }

    /**
     * DB Bind params
     * Binds paramteters to a prepared sql statement
     * @param type $arrParams
     */
    protected function bindparam($arrParams) {
        $params = array();
        $params[0] = "";

        foreach ($arrParams as $key => $value) {
            $params[0] .= $this->gettype($value);
            array_push($params, $arrParams[$key]);
        }
        call_user_func_array(array($this->stmt,'bind_param'), $this->refval($params));
    }

    /**
     * DB Get Inserted ID
     * Returns last inserted id
     * @return int
     */
    public function getinsertid() {
        return $this->db->insert_id;
    }

    /**
     * Determines the datatype o a given value
     * @param type $var
     * @return string
     */
    protected function gettype($var) {
        switch(gettype($var)) {
            case 'NULL':
            case 'string':
                return 's';
                break;
            case 'boolean':
            case 'integer':
                return 'i';
                break;
            case 'blob':
                return 'b';
                break;
            case 'double':
                return 'd';
                break;
        }
        return '';
    }

    /**
     * Changes array values to referring values
     * @param array $arr
     * @return array $refs
     */
    protected function refval($arr)
    {
        if (strnatcmp(phpversion(), '5.3') >= 0) {
            $refs = array();
            foreach ($arr as $key => $value) {
                $refs[$key] =& $arr[$key];
            }
            return $refs;
        }
        return $arr;
    }

    /**
     * Method Sanitize
     * Sanitizes a query string
     * @param string $sql
     * @param int $sanitize
     * @return string
     */
    protected function sanitize($sql,$sanitize) {
        $str_sanitized = ($sanitize) ? filter_var($sql,FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES) : $sql;
        return $str_sanitized;
    }

    /**
     * Escapes string
     * Strips it of tags
     * @param $value
     * @return mixed
     */
    public function real_escape_string($value) {
        return $this->db->real_escape_string($value);
    }

    /**
     * Method for SQL debugging
     * Combines sql string and params in a string
     * @param string $sql
     * @param array $params
     * @return string Returns a SQL
     */
    public function to_string($sql, $params) {
        foreach($params as $key => $value) {
            if(is_string($value)) {
                $params[$key] = "'" . $value . "'";
            }
        }
        $sql = preg_replace("/[?]+(\W.)*/", implode(",",$params), $sql);
        return $sql;
    }


    /**
     * Method Close
     * Close a db connection
     */
    public function close() {
        $this->db->close();
    }
}

/* END: DB CLASS */