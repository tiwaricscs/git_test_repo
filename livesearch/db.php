
<?php 

//connect to database
$conn=mysqli_connect('localhost', 'chandan', '123', 'org');

//check connection
if(!$conn){
	echo "connection error: " . mysqli_connect_error();
}


if(isset($_POST['submit'])){

    if (empty($_POST['name'])) {
        header('location: index.php?err=yes');
		}
		else{
			$name=$_POST['name'];
            
            
            //create sql
            $sql="INSERT INTO user(name) VALUES('$name')";
            
            //	save to database and check
			if(mysqli_query($conn, $sql)){
				//success
			header('location: index.php');
			}else{
				//error
				echo 'query error: '. mysqli_error($conn);
			}
        }
    }
 