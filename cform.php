<?php 
include('db.php');

$con->query("create table if not exists `customer` (customer_id int(11) primary key AUTO_INCREMENT, 
	fname varchar(255),
	lname varchar(255),
	email varchar(255),
	address text,
	date_added datetime)");

$email_error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {
	$flag = true;
	if(empty($_POST['email'])) {
		$flag = false;
		$email_error = "email is empty";
	} 	 
	
	if($flag) { 
		if(isset($_GET['customer_id']) && $_GET['customer_id'] > 0) {
			// do update
			$con->query("update `customer` set fname = '".$_POST['fname']."', lname = '".$_POST['lname']."', email = '".$_POST['email']."', address = '".$_POST['address']."', date_added = now() where customer_id = " . $_GET['customer_id']);		
		} else {
			// do insert		
			$con->query("insert into `customer` set fname = '".$_POST['fname']."', lname = '".$_POST['lname']."', email = '".$_POST['email']."', address = '".$_POST['address']."', date_added = now() ");
		}
		
		header('location:clist.php');
	}
}

$customer_data = array('fname' => '', 'lname' => '', 'email' => '', 'address' => '');

if(isset($_GET['customer_id']) && $_GET['customer_id'] > 0) {
	$result = $con->query("select * from customer where customer_id = " . $_GET['customer_id']);
	if($result->num_rows > 0) { 
		$customer_data = $result->fetch_assoc();
	}	 
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Customer Form</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>

<body>
<div class="container">
  <h1>Customer Form</h1>
  <form action="" method="post">
  <div class="container-fluid">
  <div class="">

    <div class="mb-3">
      <label for="fname" class="col-form-label">first name</label>
      <input type="text" name="fname" class="form-control" id="fname" value="<?php echo $customer_data['fname'];?>">
    </div>
    <div class="mb-3">
      <label for="lname" class="col-form-label">last name</label>
      <input type="text" name="lname" class="form-control" id="lname" value="<?php echo $customer_data['lname'];?>">
    </div>
    <div class="mb-3">
      <label for="email" class="col-form-label">Email</label>
      <input type="email" name="email" class="form-control" id="email" value="<?php echo $customer_data['email'];?>">
      <?php echo $email_error;?>
    </div>
    <div class="mb-3">
      <label for="address" class="col-form-label">Address</label>
      <textarea name="address" class="form-control" id="address"> <?php echo $customer_data['address'];?> </textarea>
    </div>
    <input type="submit" name="save" value="save"  class="btn btn-primary" />
    <input type="reset" name="reset" value="reset"  class="btn btn-warning" />
    <a href="clist.php" class="btn btn-danger">Cancel</a>
</div>
</div>
</form>
</body>
</div>
</html>
