<?php
session_start();
include_once "../api/config/site_config.php";
include "system/api_caller.php";

$apiCaller = new Api_Caller('1', 'asdfghjkl', FORTOS_API_URL);

$images = $apiCaller->sendRequest(
    [
        'controller' => 'image',
        'action' => 'get',
        //extra data please
    ]
);

//var_dump($images);
//echo DIR_FORTOS;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fortos | Instant Upload Service</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/styles.css">

    <script type="text/javascript" src="js/jquery-3.1.1.slim_min.js" ></script>

</head>
<body>

<main id="wrapper">

    <header>
        <h1 class="site-name">Fortos.com</h1>
        <p class="site-tagline">A Swift Photo Gallery For All</p>
    </header>

    <section id="upload-section">

        <form id="upload-form" action="../api/upload.php?f=1" method="post" enctype="multipart/form-data">

            <input id="input-caption" type="text" name="image_caption" placeholder="Add Caption" autocomplete="off">

            <label id="button-file" for="input-file" class="button button-flat">
                <i class="fa fa-plus"></i> &nbsp; Add Photo
                <input id="input-file" type="file" name="image_file">
            </label>

        </form>

        <?php
//        echo $date = date("Y-m-d H:i:s");//, time());

        if( isset($_SESSION['success']) ){
            $msg = $_SESSION['success'] == false ? $_SESSION['error_msg'] : null;
            session_unset(); session_destroy();
            ?>
            <p class="alert text-center"><strong><?php echo $msg; ?></strong></p>
        <?php
        }
        ?>

    </section>

    <section id="photo-section">
        <ul id="photo-list">

            <?php
            if(! empty($images)):
                foreach($images as $image):
            ?>
            <li class="photo-item">
                <div class="photo-wrap">
                    <div class="meta">
                        <span class="date"><?php
                            echo date_format(date_create($image['created_on']), "D d, M \a\\t h : i a"); ?></span>
                        <a class="delete" href="delete.php?p=<?php echo $image['image_id']; ?>" title="delete image">Delete</a>
                    </div>
                    <img class="photo" src="<?php echo "../photos/" . $image['image_name']; ?>">
                </div>
                <div class="caption"><span><?php echo $image['caption']; ?></span></div>
            </li>
            <?php
                endforeach;
            endif;
            ?>

        </ul>
    </section>

</main>

</body>

<script type="text/javascript" src="js/fortos_js.js" ></script>

</html>1