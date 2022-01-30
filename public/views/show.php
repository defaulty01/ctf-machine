<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Preview</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ height: 800px ;width: 100%; padding: 20px; }
    </style>
</head>
<!-- /assets/ctf-machine.tar -->
<body>
    <div class="wrapper">
        <h2>Preview</h2>
        <a href='/s/<?= $link ?>'>link</a>

        <div class="wrapper">
          <p><img src=data:image/png;base64,<?= base64_encode($data['meta_image']) ?> width=250px height=250px></p>
          <p><a href="<?= $data['meta_url'] ?>"><?= $data['meta_title'] ?></a></p>
          <p><?= $data['meta_desc'] ?></p>
        </div>

    </div>    
</body>
</html>
