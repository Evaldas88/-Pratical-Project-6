<?php
session_start();

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
        $loginMsg = 'Failed to login: wrong username and/or password!';
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
    <title>File Manager</title>
</head>

<body>

    <?php
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {

        // READ files and directories
        $path = isset($_GET["path"]) ? './' . $_GET["path"] : './';
        $doc = scandir($path);

        print('<div class="container pt-5 ">
                    <h1 class="text-center">PHP File Manager</h1>
                        <table class="table  table-bordered mt-5 text-center" >
                            <th class="table-success" >Name</th>
                            <th class="table-success">Type</th>
                            <th class="table-success">Actions</th>');
        foreach ($doc as $fnd) {
            if ($fnd != ".." and $fnd != ".") {
                print('<tr>');
                print('<td >' . (is_dir($path . $fnd) ? "Directory" : "File") . '</td>');
                print('<td >' . (is_dir($path . $fnd)
                    ? '<a href="' . (isset($_GET['path'])
                        ? $_SERVER['REQUEST_URI'] . $fnd . '/'
                        : $_SERVER['REQUEST_URI'] . '?path=' . $fnd . '/') . '">' . $fnd . '</a>'
                    : $fnd)
                    . '</td>');
                if (is_dir($path . $fnd) != "Directory") { //show button only on files 
                    print('<td class="text-center">
                    <form  action="" method="post">
                        <div class="button">
                            <input type="hidden" name="delete" value=' . str_replace(' ', '&nbsp;', $fnd) . '>
                            <input class="btn btn-outline-danger" type="submit" value="Delete">
                        </div>
                    </form></td>');
                } else {
                    print('<td></td>');
                }
                print('</tr>');
            }
        }
        print('</table>');

        // print back button
        if ($path != "./") {
            print('<div class=" pt-5">
                    <form action="" method="post">
                        <input class ="btn btn-outline-secondary" type="submit" name="back" value=' . str_replace(' ', '&nbsp;', 'Back') . '>
                    </form>');
        }

        // print create folder input and button
        print('<div class= "mt-5"> 
                    <form method="POST">
                        <input placeholder="Enter folder name" name="folder" type="text">
                        <button type="submit" class="btn btn-outline-secondary ";>Create new folder</button>
                    </form>
                </div>');
        ('</div>');



        // Get back  statment logic

        if (isset($_POST['back'])) {
            header("Location:" . (dirname($_SERVER['REQUEST_URI'])) . '/');
        }


        // File delete logic

        if (isset($_POST['delete'])) {
            $delete = $_POST['delete'];

            if ($delete !== "index.php" and $delete !== "README.md") {
                $modifiedDelete = preg_replace('/\s/u', ' ', $delete);
                unlink($path . $modifiedDelete);
                header("Refresh:0");
            } else {
                print('<h2 class="text-center text-danger mt-5">Cannot delete Readme or index files</h2>');
                header("Refresh:2");
            }
        }

        //Create new folder logic

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
                print('<h2 class="text-center text-danger mt-5">Directory "' . $_POST['folder'] . '" already exists </h2>');
            }
        }
    }
    ?>
    <!-- login form -->

    <div class=" container ">

        <div class="position-absolute top-50 start-50 translate-middle">
            <div class="p-1">
                <h5 class="text-danger"><?php print $loginMsg; ?></h5>
            </div>
            <form  class="text-center mt-" action="" method="post" <?php isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true ? print("style = \"display: none\"") : print("style = \"display: block\"") ?>>
                <input type="text" name="username" placeholder="username = test" required autofocus></br>
                <input type="password" name="password" placeholder="password = test" required>
                <div class="text-center mt-1">
                    <button class="btn btn-outline-secondary" type="submit" name="login">Login</button>
                </div>
            </form>
        </div>
    </div>

    <!-- logout form only show ,when we  are logged-in -->
    <form action="" method="post" <?php isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true ? print("style = \"display: block\"") : print("style = \"display: none\"") ?>>
        <div class="text-center mt-5">
            <button class="btn btn-outline-secondary" type='submit' name='logout' value='Logout'> logout </button>
        </div>
</body>

</html>