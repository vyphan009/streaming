<?php
namespace Fileman;

require_once __DIR__ . '/Model/File.php';
require_once __DIR__ . '/Common/Utils.php';

$fileObject = new File();
if (isset($_POST['upload'])) {
    $res = $fileObject->upload();
}
else if (isset($_POST['delete'])) {
    $res = $fileObject->delete();
}

// fetch data from the database
$results = $fileObject->fetch();
$row_count = $fileObject->fetchRowCount();

// generate pagination
$max_rows = Config::LIMIT_PER_PAGE;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = $page === 1 ? 0 : ($page - 1) * $max_rows;
$page_count = ceil($row_count / $max_rows);
if ($page_count > 1) {
    $page_link = "<ul class='pagination'>";
    for ($i=1; $i<=$page_count; $i++) {
        $active = $page == $i ? " active" : "";
        $page_link .= "<li class='page-item{$active}'><a class='page-link' href='index.php?page={$i}'>{$i}</a></li>";
    }
    $page_link .= "</ul>";
}
?>
<!doctype html>
<html>
<head lang="en">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Audio/Video Management</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css" type="text/html" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript" src="assets/js/main.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
<span class="spinner spinner-border spinner-border-sm mr-3" id="spinner" role="status" aria-hidden="true"></span>
    <div class="container">
        <div class="panel-group">
            <div class="panel panel-primary">
                <div class="panel-heading">Upload Audio/Video</div>
                <div class="panel-body">
                    <form id="uploadForm" action="index.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <input id="file" type="file" accept="audio/*,video/*" name="file" class="class="form-control"" />
                        </div>
                        <input class="btn btn-success" type="submit" name="upload" value="Upload">
                    </form>
                </div>
            </div>
        </div>
        <h3>File Management</h3>
        <hr>
    <?php
    if (!empty($res['success'])) {
        echo "<p class='text-success'>{$res['success']}</p>";
    }
    else if (!empty($res['error'])) {
        echo "<p class='text-danger'>{$res['error']}</p>";
    }
    ?>
    <?php
    if (empty($results)) {
        echo "<p>No file found</p>";
    }
    else { ?>
        <form id="deleteForm" action="index.php" method="post">
            <table class='table'>
                <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">File Name</th>
                        <th scope="col">File Size</th>
                        <th scope="col">Uploaded Date</th>
                        <th scope="col">Link</th>
                    </tr>
                </thead>
                <tbody>
        <?php foreach ($results as $row) { ?>
                    <tr>
                        <td><input type="checkbox" name="idArr[]" value="<?php echo $row['id'] ?>"></td>
                        <td><?php echo $row['file_name'] ?></td>
                        <td><?php echo bytes_to_specified($row['file_size'], 'M') ?>MB</td>
                        <td><?php echo $row['timestamp'] ?></td>
                        <td><?php echo $row['link'] ?></td>
                    </tr>
        <?php }
    } ?>
                </tbody>
            </table>
            <div class='row'>
                <div class='col-sm-6'>
                    <input class='btn btn-danger' type='submit' name="delete" value='Delete Selected Files'>
                </div>
    <?php if ($page_count > 1) { ?>
                <div class='col-sm-6 text-right'>
                    <nav><?php echo $page_link ?></nav>
                </div>
    <?php } ?>
            </div>
        </form>
    </div>
</body>
</html>
