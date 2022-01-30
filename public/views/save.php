<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
    <title>Url Shortener</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Url Shortener</h2>
        <form action="/c" method="post">
            <div class="form-group ">
                <label>Url / Link</label>
                <input type="text" name="link" class="form-control" value="">
            </div>  
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Go">
            </div>
        </form>
    </div>    
</body>
</html>