<!DOCTYPE html>
<html>
    <head>
        <title>Heavy Importer</title>
        <link href="//fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">
                    <h1 class="text-center">Heavy Importer</h1>
                </div>
            </div>
        </div>
        <?php if(!empty($errors) && count($errors)>0) : ?>
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 text-center ">
                        <?php foreach ($errors->all() as $error) : ?>
                            <p class="bg-danger"><?php echo $error ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if(Session::has('message')) : ?>
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 text-center ">
                        <p class="bg-success" id="status"><?php echo Session::get('message'); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <form action="/import" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="container">
                <div class="row">
                    <div class="col-sm-4 col-sm-offset-4">
                        <input class="form-control" type="file" name="excel_file">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-sm-offset-4 text-center">
                        <button class="btn btn-success" style="margin-top:10px;" type="submit">Upload</button>
                    </div>
                </div>
            </div>
        </form>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
        <script>
            (function($){
                'use strict';
                function statusUpdater() {
                    var status = $('#status');
                    $.ajax({
                        'url': '/status',
                    }).done(function(r) {
                        if(r.msg==='done') {
                            status.text( "The import is completed. Your data is now available for viewing ... " );
                            console.log( "The import is completed. Your data is now available for viewing ... " );
                        } else {
                            //get the total number of imported rows
                            status.text( "Status is: " + r.msg );
                            console.log("Status is: " + r.msg);
                            console.log( "The job is not yet done... Hold your horses, it takes a while :)" );
                            statusUpdater();
                        }
                      })
                      .fail(function() {
                          status.text( "An error has occurred... We could ask Neo about what happened, but he's taken the red pill and he's at home sleeping" );
                          console.log( "An error has occurred... We could ask Neo about what happened, but he's taken the red pill and he's at home sleeping" );
                      });
                }
                statusUpdater();
            })(jQuery);
        </script>
    </body>
</html>
