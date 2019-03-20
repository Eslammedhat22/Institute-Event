<?php
error_reporting(0);

//check if user coming from POST REQUEST

if($_SERVER['REQUEST_METHOD']==='POST')
{
	$data=array();
// sanitize the inputs and store them into Variables
	$fullName=filter_var($_POST['name'], FILTER_SANITIZE_STRING) ;
	$mobile=filter_var($_POST['mobile'], FILTER_SANITIZE_NUMBER_INT);
	$email=filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
	$university=filter_var($_POST['university'], FILTER_SANITIZE_STRING) ;
	$faculty=filter_var($_POST['faculty'], FILTER_SANITIZE_STRING) ;
	$department=filter_var($_POST['department'], FILTER_SANITIZE_STRING) ;
	$year=filter_var($_POST['year'], FILTER_SANITIZE_STRING) ;
	$firstPref=$_POST['firstpref'];
	$secondPref=$_POST['secondpref'];
// variables to hold the data inputs
	$name; $mobile; $email; $university; $faculty; $department; $year; $firstpref; $secondpref;
// creating different Error variables 
	$nameError=$firstNameError=$secondNameError=$mobileError=$emailError=$universityError=$facultyError=$departmentError=$yearError=$generalError="0";

// creating array containing the errors 
	/*$formErrors=array("fullName"=>$nameError, "firstName"=>$firstnameError, "secondName"=>$secondnameError, "mobile"=>$mobileError, "email"=>$emailError, "university"=>$universityError, "faculty"=>$facultyError, "department"=>$departmentError, "year"=>$yearError);*/

// conditions that lead to an form error 

foreach ($_POST as $key => $value) {
		switch ($key) {

/*//////////// validatation of the Name\\\\\\\\\\\\\\\*/
			case 'name':
						$pos=stripos($value,' ');  // check for the space between the first two Names
//make sures that user enterd his full name
						if($pos === FALSE || strlen($value)<2)
							$nameError="you have to enter your full name";
						else
						{
// getting the first and the second names		
							$firstName=substr($value, 0, $pos);
							$secondName=substr($value, $pos, strlen($value)-$pos);

// remove any spaces in the two names
							trim($firstName," ");
							trim($secondName," ");
// check the validty of the first name
							if(strlen($firstName)<2 || !preg_match("/^[a-zA-Z\p{Arabic}\s]+$/iu", $firstName))   
								$firstNameError= "your first name is invalid, please enter a valid name";
// check the validty of the second name
							else if(strlen($secondName)<2 || !preg_match("/^[a-zA-Z\p{Arabic}\s]+$/iu", $secondName))  
								$secondNameError= "your second name is invalid, please enter a valid name";	
							else
								$data[$key]=$value;	
						}
						break;
/*/////////// validation of The Mobile Number \\\\\\\\\\\\*/
			case 'mobile':
						if (!preg_match("/^01(0|1|2|5)\d{8}+$/", $value))
							$mobileError= "Please Enter a valid Mobile Number";
						else
							$data[$key]=$value;
						break;
/*/////////// validation of The Email \\\\\\\\\\\\*/
			case 'email':
						if (!filter_var($value, FILTER_VALIDATE_EMAIL))
							$emailError= "Please Enter a valid E-mail";
						else
							$data[$key]=$value;
						break;
/*/////////// validation of The University \\\\\\\*/
			case 'university':
							if(strlen($value)<2 || !preg_match("/^[a-zA-Z\p{Arabic}\s]+$/iu", $value)) 
								$universityError= "University name isn't Valid, it may contain only English or Arabic letters  ";
							else
								$data[$key]=$value;
							break;
/*/////////// validation of The Faculty \\\\\\\*/
			case 'faculty':
							if(strlen($value)<2 || !preg_match("/^[a-zA-Z\p{Arabic}\s]+$/iu", $value)) 
								$facultyError= "faculty name isn't Valid, it may contain  only English or Arabic letters  ";
							else
								$data[$key]=$value;
							break;
/*/////////// validation of The Department \\\\\\\*/
			case 'department':
							if(strlen($value)<2 || !preg_match("/^[a-zA-Z\p{Arabic}\s]+$/iu", $value)) 
								$departmentError= "Department name isn't Valid, it may contain only English or Arabic letters ";
							else
								$data[$key]=$value;
					break;
/*/////////// validation of The year \\\\\\\*/
			case 'year':
						if(!preg_match("/^[0-9]|[a-zA-Z\p{Arabic}\s]+$/iu", $value)) 
							$yearError= "Year may contain only English or Arabic letters or numbers";
						else
							$data[$key]=$value;
						break;
			case 'firstpref': $data['firstpref']=$value; break;
			case 'secondpref': $data['secondpref']=$value; break;
			default:break;
		}		
	}
		$name=$data['name'];
		$mobile=$data['mobile'];
		$email=$data['email'];
		$university=$data['university'];
		$faculty=$data['faculty'];
		$department=$data['department'];
		$year=$data['year'];
		$firstpref=$data['firstpref'];
		$secondpref=$data['secondpref'];

		if($nameError!="0"||$firstNameError!="0"||$secondNameError!="0"||$mobileError!="0"||$emailError!="0"||$universityError!="0"||$facultyError!="0"||$departmentError!="0"||$yearError!="0") $generalError="1";
		
}

/*--- Database Connection --- */
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "institute";

// Create connection
$conn = new mysqli ($servername, $username, $password, $dbname);

// Check connection
/*if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);
echo "Connected successfully";*/

$done=0;
if(isset($data) && $generalError=="0")
{
	$dbData=array();
	$check1="none";
	$check2="none";
	$status;	

	$sql1="SELECT mobile, email FROM user";
 	$result = $conn->query($sql1);
	if ($result->num_rows > 0)
	{
		while($row=mysqli_fetch_assoc($result))
		{
			if($row["mobile"]===$mobile) $check1="mobile"; 
			if($row["email"]===$email) $check2="email";
			
		}
	}
	if($check1==="mobile" && $check2==="email") $status=  "Sorry ". $data['name'] .", These Email: ".$data['email']." and Mobile Number: ".$data['mobile']." already exist";
	else if($check1==="mobile") $status= "Sorry ". $data['name'] .", This Mobile Number: ".$data['mobile']." already exists";
	else if($check2==="email") $status= "Sorry ".$data['name'].", This Email: ".$data['email']." already exists";
	else{
			$sql3 = "INSERT INTO user (name, mobile, email, university, faculty, department, year, firstpref, secondpref)
					VALUES ('$name', '$mobile', '$email', '$university', '$faculty', '$department', '$year', '$firstpref', '$secondpref')";

			if ($conn->query($sql3) === TRUE) 
	    		{$status ="Thanks ". $data['name'] .", you have registered successfully "; $done=1;}
	 		else 
	    		echo "Error: " . $sql3 . "<br>" . $conn->error;
		}
	}		
$conn->close();
?>
<!-- Form -->
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Registration</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<script src="js/jquery-1.12.4.min.js"></script>	
	<script src="js/bootstrap.min.js"></script>
	<script src="js/sweetalert.min.js"></script>
	<script src="js/java.js"></script>	
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/sweetalert.css">
	<link rel="icon" href="css/Images/logo.png">
</head>
<body>
	<br><br><br><br>		
	<div class="container">
		<br><img src="css/Images/logo.png"><br><br>
		<form action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">			
            <!-- full name -->
			<Label>Full Name</Label>
			<input value="<?php if(isset($generalError) && $generalError!=0 || (isset($status) && $done!=1) ) echo isset($data['name'])? $data['name'] : ''; ?>" type="text" name="name" class ="form-control" class="input" 
			placeholder="<?php if( isset($nameError) || isset($firstNameError) || isset($secondNameError))
									if($nameError!="0") echo $nameError;
									else if($firstNameError!="0") echo $firstNameError ;
									else if ($secondNameError!="0") echo $secondNameError;
									else echo"example: john doe" ;
								else echo"example: john doe" ;
	 					?>" required  
			 style="<?php if(isset($done) && $done!=1 && ($check1!="mobile" || $check2!="email") )
			 				 if(isset($nameError) || isset($firstNameError) || isset($secondNameError) )
			 					if( $nameError!="0" || $firstNameError!="0" || $secondNameError!="0" )
			 						{ echo 'border: 2px solid red;
											box-shadow:0 0 1px red;';
									}
									else{
										echo'border: 2px solid green;
											box-shadow:0 0 1px green;';
										}

					?>">
			<br>
            <!-- Mobile -->
			<Label>Mobile Number</Label>
			<input value="<?php if(isset($generalError) && $generalError!=0 || (isset($status) && $done!=1) ) echo isset($data['mobile'])? $data['mobile'] : ''?>" type="text" name="mobile" class = "form-control" required
			style="<?php if( isset($done) && isset($mobileError) && $done!=1 ) if($mobileError!="0" || $check1==="mobile" ){
								echo 'border: 2px solid red;
									box-shadow:0 0 1px red;';}		
							else{
								echo'border: 2px solid green;
								box-shadow:0 0 1px green;';}
					?>" placeholder=" <?php if(isset($mobileError)) echo ($mobileError!="0")? $mobileError : "example : 01111111111" ; else echo"example : 01111111111" ; ?>" >
			<br> 
            <!-- Email -->
			<Label>Email</Label>
			<input value="<?php if(isset($generalError) && $generalError!=0 || (isset($status) && $done!=1) )  echo isset($data['email'])? $data['email'] : ''?>"  type="email" name="email" class = "form-control" required 
			placeholder=" <?php if(isset($emailError)) echo ($emailError!="0")? $emailError : "example:  john_doe@example.com" ; else echo "example:  john_doe@example.com"; ?>"
			style="<?php if( isset($done) && $done!=1 && isset($emailError) ) 
							if($emailError!="0" || $check2==="email" ){
								echo 'border: 2px solid red;
									box-shadow:0 0 1px red;';}		
							else{
								echo'border: 2px solid green;
								box-shadow:0 0 1px green;';}
					?>">
			<br>
            <!-- university -->
			<Label>University</Label>
			<input value="<?php if(isset($generalError) && $generalError!=0 || (isset($status) && $done!=1) )  echo isset($data['university'])? $data['university'] : ''?>"  type="text" name="university" class = "form-control" 
			placeholder=" <?php if(isset($universityError))
										echo ($universityError!="0")? $universityError : "example : Ain Shams" ;
								else echo "example : Ain Shams" ; ?>"
			 required
			style="<?php if(isset($done)) if($done!=1 && ($check1!="mobile" || $check2!="email") )  if(isset($universityError))  if($universityError!="0"){
								echo 'border: 2px solid red;
									box-shadow:0 0 1px red;';}		
							else{
								echo'border: 2px solid green;
								box-shadow:0 0 1px green;';}
					?>">
			<br>
            <!-- Faculty -->
			<Label>Faculty</Label>
			<input value="<?php if(isset($generalError) && $generalError!=0 || (isset($status) && $done!=1) )  echo isset($data['faculty'])? $data['faculty'] : ''?>"  type="text" name="faculty" class = "form-control" required
			placeholder="<?php if(isset($facultyError)) echo($facultyError!="0")?$facultyError:"example : Engineering";
							   else echo "example : Engineering"; ?>"
			style="<?php if(isset($done)) if($done!=1 && ($check1!="mobile" || $check2!="email") )  if(isset($facultyError))  if($facultyError!="0"){
								echo 'border: 2px solid red;
									box-shadow:0 0 1px red;';}		
							else{
								echo'border: 2px solid green;
								box-shadow:0 0 1px green;';}
					?>">
			<br>
            <!-- Department -->
			<Label>Department</Label>
			<input id="dep" value="<?php if(isset($generalError) && $generalError!=0 || (isset($status) && $done!=1) )  echo isset($data['department'])? $data['department'] : ''?>"  type="text" name="department" class = "form-control" required
			placeholder="<?php if(isset($departmentError)) echo($departmentError!="0")?$departmentError:"example : Electrical"; 			  else echo "example : Electrical";?>"
			style="<?php if(isset($done)) if($done!=1 && ($check1!="mobile" || $check2!="email") )  if(isset($departmentError))  if($departmentError!="0"){
								echo 'border: 2px solid red;
									box-shadow:0 0 1px red;';}		
							else{
								echo'border: 2px solid green;
								box-shadow:0 0 1px green;';}
					?>">
			<br>
            <!-- year -->
			<Label>Year</Label>
			<input value="<?php if(isset($generalError) && $generalError!=0  || (isset($status) && $done!=1) )  echo isset($data['year'])? $data['year'] : ''?>"  type="text" name="year" class = "form-control" required
			placeholder="<?php if(isset($yearError)) echo($yearError!="0")?$yearError:"example : 1 OR First OR 1st";
							   else echo "example : 1 OR First OR 1st"; ?>"
			style="<?php if(isset($done)) if($done!=1 && ($check1!="mobile" || $check2!="email") )  if(isset($yearError))  if($yearError!="0"){
								echo 'border: 2px solid red;
									box-shadow:0 0 1px red;';}		
							else{
								echo'border: 2px solid green;
								box-shadow:0 0 1px green;';}
					?>">
			<br>

            <!-- Preferences -->
			<Label>First Preference</Label>
			<select required name="firstpref" class="custom-select mr-sm-2" 
			id="firstpref" >
				<option value="">Choose...</option> 
				<!-- Technical -->
				<optgroup label="Technical">
        		<option value="Python">Python</option>
        		<option value="SolidWorks">SolidWorks</option>
				<!-- Non Technical -->
        		<optgroup label="Non Technical">
       			<option value="Marketing">Marketing</option>
       			<option value="Project initiation & planning">Project initiation & planning</option>
       			<option value="Budgeting & Scheduling">Budgeting & Scheduling</option>
       			<option value="Risk management & Project changes">Risk management & Project changes</option>
      		</select>
			<br><br>
			<Label>Second Preference</Label>
			<select required name="secondpref" class="custom-select mr-sm-2"
			id="secondpref">
				<option value="">Choose...</option> 
				<!-- Technical -->
				<optgroup value="Technical" label="Technical">
        		<option value="Python">Python</option>
        		<option value="SolidWorks">SolidWorks</option>
				<!-- Non Technical -->
        		<optgroup label="Non Technical">
       			<option value="Marketing">Marketing</option>
       			<option value="Project initiation & planning">Project initiation & planning</option>
       			<option value="Budgeting & Scheduling">Budgeting & Scheduling</option>
       			<option value="Risk management & Project changes">Risk management & Project changes</option>
      		</select>
			<br><br><br>
			<input  type="submit" value="Submit" class="btn btn-warning btn-block">
		</form>
		<br>
		<!-- Message to show if the email or mobile is already exist 
			or record in database is done */-->	
		<script type="text/javascript"> 
			
			var status = <?php echo json_encode($status); ?> ;
			var done = <?php echo json_encode(isset($status)); ?> ;
			var success = <?php echo json_encode($done); ?> ;
			if(done)
			{
				$(document).ready(function() 
				 	{
				 		if(success)
				 		{swal({ 
				 				title: "Congratulations!",
   								text: status,
    							icon: "success",
    						})
    					.then(function()
    					{ window.location.href = '#'; })}
				 	
			
				 		else
						{swal({ 
				 				title: "Error!",
   								text: status,
    							icon: "error",
    							button : "Try Again"
    						})
    					}
				 	})
			}
		</script>
	</div>
	<br><br>	
</body>
</html>