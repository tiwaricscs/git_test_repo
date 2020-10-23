<html>
    <head>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    </head>

    <body>
        <form action="db.php" method="POST">
            <label for="name">enter your name</label>
            <input type="text" name="name" placeholder="firstname">
            <button type="submit" name="submit">submit</button>
            <?php if( isset($_GET['err'])  and $_GET['err'] ==='yes' ){echo "a name is required <br/>";}  ?>
        </form>
           
        <div>
             <label for="name">search</label>
             <input type="text" id="search" placeholder="search" autocomplte="off" onkeyup="search();">
        </div>

        <div>
            <table width="100%" cellspacing="0" cellpadding="10px">
                <tr><td id="table-data"></td></tr>
            </table>
        </div>
   
   
<script type="text/javascript">

function search(){

        var search_term=$('#search').val();

        $.ajax({
                url:"search.php",
                type:"GET",
                data:{search:search_term},
                success: function(data){
                    $("#table-data").html(data);
                }
            });
}  

//$(document).ready(function(){search();});
$(document).ready(search());
</script>
    </body>
</html>