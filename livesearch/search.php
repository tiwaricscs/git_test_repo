<?php 



//connect to database
$conn=mysqli_connect('localhost', 'chandan', '123', 'org');

//check connection
if(!$conn){
	echo "connection error: " . mysqli_connect_error();
}


    $search_value=$_GET["search"];


    if (empty($_GET['search'])) {        
        $sql="SELECT * FROM user ";
    }
	else{
        $sql="SELECT * FROM user WHERE name LIKE '%{$search_value}%'";
    }

    //	save to database and check
            $result=mysqli_query($conn, $sql);
            $output='';
           
            if(mysqli_num_rows($result)>0){
                $output='<table border="1" width="20%" cellspacing="0" cellpadding="10px" align="center">
                <tr>
                <th width="10%">id</th>
                <th width="90%">name</th>
                </tr>';
               
                while($row=mysqli_fetch_assoc($result)){
                    $output .="<tr><td align='center'>{$row['id']}</td><td>{$row['name']}</td></tr>";
                }
                $output .="</table>";
                mysqli_close($conn);
                echo $output;
            }
            else{
                echo "<h2>no match</h2>";
            }
				
			
			


 