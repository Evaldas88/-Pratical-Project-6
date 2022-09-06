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

    // print('<h2>Current directory: ' . str_replace('?path=/', '', $_SERVER['REQUEST_URI']) . '</h2>');  show current directory path
    print('<div class="container pt-5"><table class="table  table-striped table-active" style="width:80%; margin:auto; border-radius: 10%; border: 1px solid black">
    <th style="width: 40%; text-align: center; border: 1px solid black;">Name</th>
    <th style="width: 30%; text-align: center; border: 1px solid black;">Type</th>
    <th style="width: 30%; text-align: center; border: 1px solid black;">Actions</th>');
    foreach ($doc as $fnd) {
        if ($fnd != ".." and $fnd != ".") {
            print('<tr>');
            print('<td style="width: 40%; text-align: center; border: 1px solid black;">' . (is_dir($path . $fnd) ? "Directory" : "File") . '</td>');
            print('<td style="width: 40%; text-align: center; border: 1px solid black;">' . (is_dir($path . $fnd)
                ? '<a href="' . (isset($_GET['path'])
                    ? $_SERVER['REQUEST_URI'] . $fnd . '/'
                    : $_SERVER['REQUEST_URI'] . '?path=' . $fnd . '/') . '">' . $fnd . '</a>'
                : $fnd)
                . '</td>');
            print('<td><form method="post">
            <button  class=" btn btn-primary" type ="submit" name="delete" >
            Delete</button>

                 </form></td>');
            print('</tr>');
        }
    }
    print('</table></div>');



    ?>

</body>

</html>