<?php
namespace Fileman;

class Database
{
  const DB_HOST = "localhost";
  const DB_USERNAME = "stream";
  const DB_PASSWORD = "";
  const DB_NAME = "stream";

  private $conn;

  function __construct()
  {
    $this->conn = $this->getConnection();
  }

  /*
   * get MySQL connection
   * return \mysqli
   */
  public function getConnection()
  {
    $conn = new \mysqli(self::DB_HOST, self::DB_USERNAME, self::DB_PASSWORD, self::DB_NAME);

    if (mysqli_connect_errno()) {
      trigger_error("Problem with connecting to database.");
    }

    $conn->set_charset("utf8");
    return $conn;
  }

  /*
   * fetch data
   * $query - param string
   * $paramType - param string
   * $paramArray - param array
   * return - array
   */
  public function select($query, $paramType = "", $paramArray = array())
  {
    $statement = $this->conn->prepare($query);

    if (! empty($paramType) && ! empty($paramArray)) {
      $this->bindQueryParams($statement, $paramType, $paramArray);
    }

    $statement->execute();
    $result = $statement->get_result();

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $resultset[] = $row;
      }
    }

    if (! empty($resultset)) {
      return $resultset;
    }
  }

  /*
   * execute a query
   * $query - param string
   * $paramType - param string
   * $paramArray - param array
   * return - array
   */
  public function execute($query, $paramType = "", $paramArray = array())
  {
    try {
      $statement = $this->conn->prepare($query);

      if (! empty($paramType) && ! empty($paramArray)) {
        $this->bindQueryParams($statement, $paramType, $paramArray);
      }

      $statement->execute();
      return "success";
    } catch (PDOException $e) {
      return $e->getMessage();
    }
  }

  public function bindQueryParams($statement, $paramType, $paramArray = array())
  {
    $paramValueReference[] = & $paramType;
    for ($i = 0; $i < count($paramArray); $i ++) {
      $paramValueReference[] = & $paramArray[$i];
    }

    call_user_func_array(array(
      $statement,
      'bind_param'
    ), $paramValueReference);
  }

  public function getRecordCount($query, $paramType = "", $paramArray = array())
  {
    $statement = $this->conn->prepare($query);

    if (! empty($paramType) && ! empty($paramArray)) {
      $this->bindQueryParams($statement, $paramType, $paramArray);
    }
    $statement->execute();
    $statement->store_result();
    $recordCount = $statement->num_rows;

    return $recordCount;
  }
}

?>
