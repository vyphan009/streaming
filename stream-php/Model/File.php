<?php
namespace Fileman;
require 'vendor/autoload.php';

use Aws\S3\S3Client;  
use Aws\Exception\AwsException;
use Aws\Common\Aws;
use Fileman\Database;

class File
{
  private $db;
  
  function __construct()
  {
    require_once __DIR__ . './../lib/Database.php';
    $this->db = new Database();
  }

  public function fetch()
  {
    require_once __DIR__ . './../Common/Config.php';
    $max_rows = Config::LIMIT_PER_PAGE;

    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = $page === 1 ? 0 : ($page - 1) * $max_rows;

    $query = 'SELECT * FROM files ORDER BY timestamp DESC LIMIT ? OFFSET ?';
    $paramType = 'ii';
    $paramValue = array(
      $max_rows,
      $offset
    );
    $result = $this->db->select($query, $paramType, $paramValue);
    return $result;
  }

  public function fetchRowCount()
  {
    $query = 'SELECT * FROM files';
    $num_rows = $this->db->getRecordCount($query);
    return $num_rows;
  }

  public function upload()
  {
    require_once __DIR__ . './../Common/Config.php';
    $allowed_extension = Config::ALLOWED_EXTENSION;
    
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_name = $_FILES['file']['name'];
    $file_size = $_FILES['file']['size'];

    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    if (in_array($ext, $allowed_extension)) {
      #TODO: push uploaded $file_tmp to amazon s3
      $bucket = 'mymedia-dev-input-8se39g3r';
      $s3 = new S3Client([
        'version' => 'latest',
        'region'  => 'us-west-2',
        'credentials' => [
            'key'    => 'AKIAIKT6TX76Z6FCQHRA',
            'secret' => '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',
        ],
      ]);

      try {
        $result = $s3->putObject([
            'Bucket' => $bucket,
            'Key' => $file_tmp
            // 'ACL' => 'public-read'
        ]);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);

        $query = "INSERT INTO files (file_name, file_type, file_size, timestamp, link) VALUES (?,?,?,?,?)";
        $paramType = "ssiss";
        $paramArray = array(
          $file_name,
          $mime_type,
          $file_size,
          date('Y-m-d h:i:s', time()),
          $result['ObjectURL']
        );
        $result = $this->db->execute($query, $paramType, $paramArray);

        if ($result === "success") {
          return array("success" => "The file was uploaded successfully");
        }
        else {
          return array("error" => $result);
        }
      }catch (S3Exception $e) {
        return array("error" => $e->getMessage() . PHP_EOL);
      }

    }

  }

  public function delete()
  {
    $deleted = 0;
    foreach ($_POST["idArr"] as $checked) {

      $query = "SELECT link from files WHERE id=?";
      $paramType = 'i';
      $res = $this->db->select($query, $paramType, array($checked));
      $link =  $res[0][link];
      preg_match_all('/([\w-_]+)\.s3\.[^.]+\.amazonaws\.com\/\/private\/var\/tmp\/(\w+)/', $link, $data);
      $bucket = $data[1][0];
      $keyname = $data[2][0];

      $query = "DELETE FROM files WHERE id=?";
      $result = $this->db->execute($query, "i", array($checked));
      
      if ($result === "success") {
        $deleted += 1;
      //   #TODO: delete the file from amazon s3 also
        $s3 = new S3Client([
          'version' => 'latest',
          'region'  => 'us-west-2',
          'credentials' => [
              'key'    => 'AKIAIKT6TX76Z6FCQHRA',
              'secret' => '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',
          ],
        ]);
        try
        {
          $result = $s3->deleteObject([
              'Bucket' => $bucket,
              'Key'    => '/private/var/tmp/' . $keyname
          ]);  

        }catch (S3Exception $e) {
          exit('Error: ' . $e->getAwsErrorMessage() . PHP_EOL);
        }

       }
    }
    return array("success" => "{$deleted} selected file(s) was deleted");
  }
}

?>
