<?php
session_start();

// READ files and directories
$path = isset($_GET["path"]) ? './' . $_GET["path"] : './';
$doc = scandir($path);

// logout logic
if (isset($_POST['logout'])) {
    session_destroy();
    session_start();
    header('Location: ' . $_SERVER['PHP_SELF']);
}

// login logic
$loginMsg = '';
if (isset($_POST['login']) && !empty($_POST['username']) && !empty($_POST['password'])) {
    if ($_POST['username'] == 'test' && $_POST['password'] == 'test') {
        $_SESSION['logged_in'] = true;
        $_SESSION['timeout'] = time();
        $_SESSION['username'] = $_POST['username'];
    } else {
        $loginMsg = 'Failed to login: wrong username or password!';
    }
}

// File delete logic
$msg = '';
if (isset($_POST['delete'])) {
    $delete = $_POST['delete'];
    if ($delete !== "index.php" and $delete !== "README.md" and $delete !== "login.php") {
        $modifiedDelete = preg_replace('/\s/u', ' ', $delete);
        unlink($path . $modifiedDelete);
        header("Refresh:0");
    } else {
        $msg = 'Cannot delete this file';
        header("Refresh:2");
    }
}

//Create new folder logic
 $msg1 = '';
if (isset($_POST['folder'])) {
    $foldername = $_POST['folder'];
    if (isset($_GET['path'])) {
        $path_n = $_GET['path'];
        $path = './' . $path_n;
    }
    if (!file_exists($path . $foldername)) {
        @mkdir($path . $foldername, 0777, true);
        header("refresh: 0");
    } else if (isset($_POST['folder']) and file_exists("./" . $_POST['folder'])) {
        $msg1= 'Directory "' . $_POST['folder'] . '" already exists ';
    }
}

// download file logic
if (isset($_POST['download'])) {
    $file = './' . $path . $_POST['download'];
    $file_path = str_replace("&nbsp;", " ", htmlentities($file, 0, 'utf-8'));
    ob_clean();
    ob_start();
    header('Content-Description: File Transfer');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename=' . basename($file_path));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));
    ob_end_flush();
    readfile($file_path);
    exit;
}

// UPLOAD  file logic
$error = '';
if (isset($_FILES['image'])) {
    $file_name = $_FILES['image']['name'];
    $file_size = $_FILES['image']['size'];
    $file_tmp = $_FILES['image']['tmp_name'];
    $file_type = $_FILES['image']['type'];
    $exploded = explode('.', $_FILES['image']['name']);
    $file_ext = strtolower(end($exploded));
    $extensions = ["jpeg", "jpg", "png"];
    if (in_array($file_ext, $extensions) === false) {
        $error = 'File format is not allowed, please choose a JPEG, PNG, JPG';
        header("refresh:2");
    }
    if ($file_size > 5000000) {
        $error = 'Image size must be smaller than 5 MB';
        header("refresh:2");
    }
    if (empty($error) == true) {
        move_uploaded_file($file_tmp, $path . $file_name);
        header("refresh:2");

    } 
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <title>File Manager</title>
</head>
<body>
    <?php
if (isset($_SESSION['logged_in']) == true){
        print('<div class="container  pt-5 ">
                    <h3> 
                     <form action="" method="post"  isset($_SESSION["logged_in"])true ? print("style = \"display: block\"") : print("style = \"display: none\"") ?>
                        <div class="mt-5">
                            <button class="btn btn-outline-secondary" type="submit" name="logout" value="Logout"><i class="bi bi-box-arrow-in-left me-1"></i>Exit </button>
                         </div>
                    </h3>
                    <h1 class="text-center">PHP File Manager</h1>
                        <table class="table  table-bordered mt-5 text-center" >
                            <th class=" w-25 table-success" >Name</th>
                            <th class=" w-25 table-success">Type</th>
                            <th class=" w-25 table-success">Actions</th>');
        foreach ($doc as $fnd) {
            if ($fnd != ".." and $fnd != ".") {
                print('<tr>');
                print('<td>' . (is_dir($path . $fnd) ? "Directory" : "File") . '</td>');
                print('<td>' . (is_dir($path . $fnd)
                    ? '<a href="' . (isset($_GET['path'])
                        ? $_SERVER['REQUEST_URI'] . $fnd . '/'
                        : $_SERVER['REQUEST_URI'] . '?path=' . $fnd . '/') . '">' . $fnd . '</a>'
                    : $fnd)
                    . '</td>');
                if (is_dir($path . $fnd) != "Directory") { //show buttons only on files 
                    print('
                        <td > 
                            <div class="d-flex  justify-content-center"> 
                            <form  action="" method="post">
                                <div class="me-2">
                                    <input type="hidden" name="delete" value=' . str_replace(' ', '&nbsp;', $fnd) . '>
                                    <button class="btn btn-warning mr-5" type="submit" value="Delete"><i class="bi bi-trash3-fill"></i></button>
                                </div>
                            </form>
                            <form action="" method="POST">
                                <div >
                                    <button class="btn btn-success" type="submit" name="download" value="' . $fnd . '"/><i class="bi bi-download"></i></button>
                                </div>
                            </form>
                            </div>
                        </td>');
                } else {
                    print('<td></td>');
                }
                print('</tr>');
            }
        }
        print('</table>');

        // Messages printing
        print("<p class='text-center text-danger'> $msg</p>");
        print("<p class='text-center text-danger'> $error</p>");        
        print("<p class='text-center text-danger'> $msg1</p>");


        // Back logic
        $split = explode('/', rtrim($_SERVER['QUERY_STRING'], '/')); // Back function
        array_pop($split);
        if (count($split) != 0) {
            print(' <a  class="btn btn-secondary mt-5" href= ' . '?' . implode('/', $split) . ' >Back</a> ');
        } else {
            print(' <a class="btn btn-secondary mt-5" href= "./" >Back</a> ');
        }


        // print create folder input and button
        print('<div class= "mt-5"> 
                    <form method="POST">
                        <input placeholder="Enter folder name" name="folder" type="text">
                        <button type="submit" class="btn btn-secondary ";>Submit</button>
                    </form>
                </div>');
    }
    ?>

    <!-- login form -->
        <div class="card  bg-light h-25 col-3  position-absolute top-50 start-50 translate-middle" <?php isset($_SESSION['logged_in']) == true ? print("style = \"display: none\"") : print("style = \"display: block\"") ?>>
            <div class="p-1">
                <h5 class="text-danger text-center"><?php print $loginMsg; ?></h5>
            </div>
            <h1 class="text-center"> File manager</h1>
            <form class="position-absolute top-50 start-50 translate-middle" action="" method="post" >
                <input type="text" name="username" placeholder="username = test" required autofocus></br>
                <input type="password" name="password" placeholder="password = test" required>
                <div class="text-center mt-3">
                    <button class="btn btn-secondary" type="submit" name="login"><i class="bi bi-box-arrow-in-right me-1"></i>Login</button>
                </div>
            </form>
        </div>
        <div class="mt-4">
            <form action="" method="post" enctype="multipart/form-data" <?php isset($_SESSION['logged_in']) == true ? print("style = \"display: block\"") : print("style = \"display: none\"") ?>>
                <div class="w-50 input-group mb-3">
                    <input type="file" name="image" class="form-control" id="inputGroupFile02">
                    <button class="btn btn-secondary" id="inputGroupFileAddon04" type="submit"><i class="bi bi-upload"></i></button>
                </div>
            </form>
        </div>
  
</body>
</html>