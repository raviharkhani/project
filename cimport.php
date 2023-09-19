<?php 
include('db.php');

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload'])) {
	
	//echo "<pre>"; print_r($_FILES['file']);exit;
	
	// Allowed mime types
    $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
    
    // Validate whether selected file is a CSV file
    if(!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)){
        
        // If the file is uploaded
        if(is_uploaded_file($_FILES['file']['tmp_name'])){
            
            // Open uploaded CSV file with read-only mode
            $p = fopen($_FILES['file']['tmp_name'], 'r');
            
            // Skip the first line
            fgetcsv($p);
            
            // Parse data from CSV file line by line
            while(($line = fgetcsv($p)) !== FALSE){
                // Get row data
                $email   = $line[0];
                $fname  = $line[1];
                $lname  = $line[2];
                $address = $line[3];
                
				if(!empty($email)) { 					
					// Check whether member already exists in the database with the same email
					$result = $con->query("SELECT * FROM customer WHERE email like '".$email."'");
					
					if($result->num_rows > 0){
						$customer_data = $result->fetch_assoc();
						
						// Update 
						$con->query("UPDATE customer SET fname = '".$fname."', lname = '".$lname."', email = '".$email."', address = '".$address."', date_added = now() where customer_id = " . $customer_data['customer_id']);
					}else{
						// Insert 
						$con->query("INSERT INTO customer set fname = '".$fname."', lname = '".$lname."', email = '".$email."', address = '".$address."', date_added = now() ");
					}
				}
            }
            
            // Close opened CSV file
            fclose($p); 
			
			header('location:clist.php');
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Customer List</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>

<body>
<div class="container">
<h1>Import CSV file</h1>
<form action="" method="post" enctype="multipart/form-data">

<input type="file" name="file" />
<input type="submit" name="upload" value="upload" class="btn btn-primary"/>
<a href="clist.php" class="btn btn-danger">Cancel</a>

</form>
</body>
</div>
</html>
