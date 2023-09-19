<?php 
include('db.php');

if(isset($_GET['del_customer_id']) && $_GET['del_customer_id'] > 0) {
	$con->query("delete from `customer` where customer_id = " . $_GET['del_customer_id']);

	header('location:clist.php');
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bulkdelete'])) {
	if(isset($_POST['cid']) && !empty($_POST['cid'])) {
		foreach($_POST['cid'] as $cid) {
			$con->query("delete from `customer` where customer_id = " . $cid);			
		}
		header('location:clist.php');
	}	
}

if(isset($_GET['export']) && $_GET['export'] == 1) {
	$filename = "customer_data_" . date('Y-m-d') . ".csv"; 
	$delimiter = ","; 
	 
	// Create a file pointer 
	$p = fopen($filename, 'w'); 
	 
	// Set column headers 
	$columns = array('customer ID', 'Email', 'Fname', 'Lname', 'Address', 'Date Added'); 
	fputcsv($p, $columns, $delimiter); 
	 
	// Get records from the database 
	$export_rs = $con->query("SELECT * FROM customer"); 
	if($export_rs->num_rows > 0){ 
		// Output each row of the data, format line as csv and write to file pointer 
		while($row = $export_rs->fetch_assoc()){ 
			$row_data = array($row['customer_id'], $row['email'], $row['fname'], $row['lname'], $row['address'], $row['date_added']); 
			fputcsv($p, $row_data, $delimiter); 
		} 
	} 
	 
	// Move back to beginning of file 
	 
	// Set headers to download file rather than displayed 
	header('Content-Type: text/csv'); 
	header('Content-Disposition: attachment; filename="' . $filename . '";'); 
	readfile($filename);
	 
	// Exit from file 
	exit();
}

$filter_data = array('fname' => '', 'lname' => '', 'email' => '', 'address' => '', 'date_added' =>  '');

$sql = "select * from customer where 1 ";

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['filter'])) {
	if(isset($_POST['fname']) && !empty($_POST['fname'])) {
		$sql .= " AND fname like '".trim($_POST['fname'])."' ";
		$filter_data['fname'] = trim($_POST['fname']);
	}
	
	if(isset($_POST['lname']) && !empty($_POST['lname'])) {
		$sql .= " AND lname like '".trim($_POST['lname'])."' ";
		$filter_data['lname'] = trim($_POST['lname']);
	}
	
	if(isset($_POST['email']) && !empty($_POST['email'])) {
		$sql .= " AND email like '%".trim($_POST['email'])."%' ";
		$filter_data['email'] = trim($_POST['email']);
	}
}


$results_per_page = 5;  
  
$result_cnt = $con->query("select count(*) as total_record from customer");  
$total_rec = $result_cnt->fetch_assoc();

//determine the total number of pages available  
$number_of_page = ceil ($total_rec['total_record'] / $results_per_page);  

//determine which page number visitor is currently on  
if (!isset($_GET['page']) ) {  
	$page = 1;  
} else {  
	$page = $_GET['page'];  
}  

//determine the sql LIMIT starting number for the results on the displaying page  
$start = ($page-1) * $results_per_page;  

//retrieve the selected results from database   
$sql .= " LIMIT " . $start . ',' . $results_per_page;  

// echo $sql;

$result = $con->query($sql);
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
<h1>Customer List</h1>
<form action="" method="post">

<a href="cform.php" class="btn btn-primary">Add New</a> 
<input type="submit" class="btn btn-danger" value="Bulk Delete" name="bulkdelete">
<a href="cimport.php" class="btn btn-info">Import</a> 
<a href="clist.php?export=1" class="btn btn-warning">Export</a>

  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-3 col-md-12 order-lg-first d-none d-lg-block mb-3">
        <div class="card">
          <div class="card-body">
            <div class="col mb-3">
              <label for="fname">first name</label>
              <input type="text" name="fname" class="form-control" id="fname" value="<?php echo $filter_data['fname']; ?>">
            </div>
            <div class="col mb-3">
              <label for="lname">last name</label>
              <input type="text" name="lname" class="form-control" id="lname" value="<?php echo $filter_data['lname']; ?>">
            </div>
            <div class="col mb-3">
              <label for="email">Email</label>
              <input type="text" name="email" class="form-control" id="email" value="<?php echo $filter_data['email']; ?>">
            </div>
            <div class="col mb-3">
              <label for="address">address</label>
              <input type="text" name="address" class="form-control" id="address" value="<?php echo $filter_data['address']; ?>">
            </div>
            <div class="col mb-3">
              <label for="date_added">Date Added</label>
              <input type="text" name="date_added" class="form-control" id="date_added" value="<?php echo $filter_data['date_added']; ?>">
            </div>
            <input type="submit" name="filter" value="Filter"  class="btn btn-primary" />
          </div>
        </div>
      </div>
      <div class="col col-lg-9 col-md-12">
        <table class="table">
          <thead>
            <tr>
              <th scope="col"></th>
              <th scope="col">customer_id</th>
              <th scope="col">#</th>
              <th scope="col">First</th>
              <th scope="col">Last</th>
              <th scope="col">Email</th>
              <th scope="col">Address</th>
              <th scope="col">Date Added</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
          	<?php 
			
			if($result->num_rows > 0) { $counter = 0;
				while($rs = $result->fetch_assoc()) {
					$counter += 1;
			?>
            
            <tr>
              <th scope="row"><input type="checkbox" name="cid[]" value="<?php echo $rs['customer_id'];?>" /></th>
              <th scope="row"><?php echo $rs['customer_id']; ?></th>
              <th scope="row"><?php echo $counter; ?></th>
              <td><?php echo $rs['fname'];?></td>
              <td><?php echo $rs['lname'];?></td>
              <td><?php echo $rs['email'];?></td>
              <td><?php echo $rs['address'];?></td>
              <td><?php echo $rs['date_added'];?></td>
              <td><a href="cform.php?customer_id=<?php echo $rs['customer_id'];?>" class="btn btn-primary">Edit</a> 
              <a href="clist.php?del_customer_id=<?php echo $rs['customer_id'];?>" class="btn btn-danger">Delete</a></td>
            </tr>
            	
            <?php 
					
				}
			}
			?>
                          
          </tbody>
        </table>
        <nav aria-label="Page navigation example">
          <ul class="pagination">
			<?php 
            for($page = 1; $page<= $number_of_page; $page++) {  
                echo '<li class="page-item"><a href = "clist.php?page=' . $page . '" class="page-link">' . $page . ' </a></li>';  
            } 
            ?>     
          </ul>
        </nav>
      </div>
    </div>
  </div>
</form>
</body>
</div>
</html>
