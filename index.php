<html>
<head>
    <meta charset="utf-8">
    <title>Files Analyzer Module</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
</head>
<body>
<?php

$files_dir = './files';
$scanned_directory = array_diff(scandir($files_dir), array('..', '.', '.DS_Store'));
$current_file = $_GET['file'];
$current_url = $_GET['url'];

foreach ($scanned_directory as $key => $item ) {
    $active = '';
    if ($current_file === $item) {
        $active = ' active';
    }
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Files Analyzer Module</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="/playground/index.php">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Files List
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <?php foreach ($scanned_directory as $key => $item ) {
                            $active = '';
                            if ($current_file === $item) {
                                $active = ' active';
                            }
                         ?>
                        <li><a class="dropdown-item <?php echo $active; ?>" href="index.php?file=<?php echo $item;?>"><?php echo $item;?></a></li>
                        <?php } ?>
                    </ul>
                </li>
            </ul>
            <form class="d-flex">
                <input class="form-control me-2" type="url" placeholder="File URL" aria-label="Search" name="url">
                <button class="btn btn-warning" type="submit">Analyze</button>
            </form>
        </div>
    </div>
</nav>

<div class="container">

<?php
if ($current_file || $current_url) {
    if($current_file) {
        echo '<p class="mt-4"><b>Selected File:</b></p>';
        echo '<p class="lead">'.$current_file. ' <a href="index.php" style="font-size: .75rem" type="button" class="btn-close" aria-label="Close"></a></p>';
        $url = $files_dir.'/'.$current_file;
        analyzeData($url);
    }
    if($current_url) {
        $filename = tempnam('/tmp','getid3_');
        if (file_put_contents($filename, file_get_contents($current_url, false, null, 0, 300000))) {
            echo '<p class="mt-4"><b>File URL:</b></p>';
            echo '<p class="lead">'.$current_url. ' <a href="index.php" style="font-size: .75rem" type="button" class="btn-close" aria-label="Close"></a></p>';
            $url = $current_url;
            analyzeData($filename, true);
        }

    }
} else { ?>
    <div class="text-center" style="margin-top: 200px">
    <h1 class="display-6">Welcome to File Analyzer Module</h1><br />
        <p>Please either select a file from the <span class="badge bg-secondary">Files List</span> or enter <span class="badge bg-warning">File URL</span> in the top right corner.</p>
    </div>
<?php }

function analyzeData($file, $with_unlink = false) {
    require_once('./vendor/getid3/getid3/getid3.php');

    $id3  = new \getID3();
    $data = $id3->analyze( $file );

    echo '<hr />';
    echo '<p><b>Analysis Report:</b></p>';

    getArrayItem($data);

    if($with_unlink) {
        unlink($file);
    }
}

function getArrayItem($array) {
    echo '<ul>';
    foreach ($array as $key => $item ) {
        echo '<li>';
        echo '['.gettype($item).'] <b>' . $key . '</b>';
        echo ': ';
        if(gettype($item) === 'array') {
            getArrayItem($item);
        } else if(gettype($item) === 'boolean') {
            echo $item ? 'true' : 'false';
        } else {
            echo $item;
        }
        echo '</li>';
    }
    echo '</ul>';
}
?>
</div>
</body>
</html>

