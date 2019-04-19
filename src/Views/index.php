<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Intervals Challenge</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://formden.com/static/cdn/font-awesome/4.4.0/css/font-awesome.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet" type="text/css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
    
    </head>
    <body>
        <div class="container">
            <h1>Intervals Challenge</h1>
            <div class="btn-group">
                <button id="listAllButton"type="button" class="btn btn-primary">List All</button>
                <button id="deleteAllButton"type="button" class="btn btn-danger">Delete All</button>
                <button id="addButton"type="button" class="btn btn-success">Add</button>
            </div>
            <div id="dateStart" class="input-group date" data-date-format="yyyy-mm-dd">
                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                <input id="dateStartText" class="form-control" type="text" readonly />
            </div>
            <div id="dateEnd" class="input-group date" data-date-format="yyyy-mm-dd">
                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                <input id="dateEndText" class="form-control" type="text" readonly />
            </div>
            <div class="input-group" >
                <span class="input-group-addon"><i class="glyphicon glyphicon-usd"></i></span>
                <input id="priceText" class="form-control" type="number"/>
            </div>
            <h1>Intervals</h1>
            <div id="intervals"></div>
        </div>
    <script>
        $(function() {
            $("#dateStart").datepicker({
                autoclose: true, 
                todayHighlight: true
            }).datepicker('update', new Date());

            $("#dateEnd").datepicker({
                autoclose: true, 
                todayHighlight: true
            }).datepicker('update', new Date());

            listAll = function(){
                $('#intervals').empty();
                $.get( "api/intervals", function(data) {
                    $items = [];
                    for(var item in data){
                        $first = data[item].date_start.split('-')[2];
                        $last = data[item].date_end.split('-')[2];
                        $price = data[item].price;
                        $items.push("(" + $first + "-" + $last + ":" + $price + ")");
                    }
                    $('#intervals').append($items.length > 0 ? $items.join(',') : 'No Intervals to show 😅');
                });
            };
            deleteAll = function(){
                $promtp = confirm("Delete All Intervals? 😱");
                if($promtp){
                    $.get( "api/intervals/deleteall", function(data) {
                        listAll();
                    });
                }
            };
            add = function(){
                $dateStart = $("#dateStartText").val();
                $dateEnd = $("#dateEndText").val();
                $price = $("#priceText").val();
                $data = '{"add": "' + $dateStart + '-' + $dateEnd + ':' + $price +'"}';
                $.post('api/intervals/new', $data)
                .done(function(data) {
                    listAll();
                }).fail(function(error){
                    alert( "Error: " + JSON.stringify(error));
                });
            };
            
            $('#listAllButton').click(function(){
                listAll();
            });

            $('#deleteAllButton').click(function(){
                deleteAll();
            });

            $('#addButton').click(function(){
                $('#addButton').attr("disabled", true);
                add();
                $('#addButton').attr("disabled", false);
            });

            listAll();
        });
    </script>
    </body>
</html>