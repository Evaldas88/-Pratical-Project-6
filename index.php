<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">

    <title>Document</title>
</head>

<body>
    <?php

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
            if (is_dir($path . $fnd) != "Directory") {
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
    if ($path != "./") {
        print('<div class=" pt-5">
        <form action="" method="post">
        <input class ="btn btn-outline-secondary" type="submit" name="back" value=' . str_replace(' ', '&nbsp;', 'Back') . '>
        </form>');
    }
    print('<div class= "mt-5">
        <form method="POST">
        <input placeholder="Enter folder name" name="folder" type="text">
        <button type="submit" class="btn btn-outline-secondary ";>Create new folder</button>
        </form></div>');
    ('</div>');



    // Get back  statment logic

    if (isset($_POST['back'])) {
        header("Location:" . (dirname($_SERVER['REQUEST_URI'])) . '/');
    }




    ?>
</body>

</html>