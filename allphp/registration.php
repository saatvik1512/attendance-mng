<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="registration.css">
    <title>Registration Form</title>
</head>
<body>
    <div class="container">
        <?php
        if(isset($_POST["submit"])){
            $fullname = $_POST["fullname"];
            $email = $_POST["email"];
            $password = $_POST["password"];
            $passwordRepeat = $_POST["repeat_password"];

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $errors = array();

            if(empty($fullname) OR empty($email) OR empty($password) OR empty($passwordRepeat)){
                array_push($errors,"All fields are required");
            }
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                array_push($errors, "Email is not valid");
            }
            if(strlen($password)<8){
                array_push($errors,"password must be atleast 8 characters long");
            }
            if($password!==$passwordRepeat){
                array_push($errors,"passwords doesn't match");
            }

            require_once "database.php";
            $sql = "SELECT * FROM database_teachers WHERE email='$email'";
            $result = mysqli_query($conn, $sql);
            $rowCount = mysqli_num_rows($result);
            if($rowCount>0){
                array_push($errors, "email already exist!");
            }

            if(count($errors)>0){
                foreach ($errors as $error){
                    echo "<div class='alert alert-danger'>$error</div>";
                }
            }
            else {
                $sql = "INSERT INTO database_teachers(full_name,email,password) VALUES (?, ?, ? )";
                $stmt = mysqli_stmt_init($conn);
                $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
                if($prepareStmt){
                    mysqli_stmt_bind_param($stmt, "sss", $fullname, $email, $passwordHash);
                    mysqli_stmt_execute($stmt);
                    echo "<div class='alert alert-success'>You are registered successfully;</div>";
                }
                else {
                    die("Something went wrong;");
                }
            }
        }
        ?>
        <form action="registration.php" method="post">
            <h2 class="heading">Sign-up</h2>
            <div class="form-group">
                <input type="text" name="fullname" placeholder="Full Name" class="form-control">
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" class="form-control">
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" class="form-control">
            </div>
            <div class="form-group">
                <input type="password" name="repeat_password" placeholder="confirm Password" class="form-control">
            </div>
            <div class="form-btn">
                <input type="submit" name="submit" value="Register" class="btn btn-dark">
            </div>
        </form>
    </div>    
</body>
</html>