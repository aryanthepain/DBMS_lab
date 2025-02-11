<?php
require_once '../include/dbh.inc.php';

$message = "";
$toastClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $roll = $_POST['roll'];
    $password = $_POST['password'];
    $phone_no = $_POST['phone'];

    // Check if a photo was uploaded
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $photo = file_get_contents($_FILES['photo']['tmp_name']);
    } else {
        $photo = null;
    }

    // Check if the roll already exists
    $checkRoll = $pdo->prepare("SELECT Roll_number FROM student_password WHERE Roll_number = :roll");
    $checkRoll->bindParam(":roll", $roll);
    $checkRoll->execute();

    if ($checkRoll->rowCount() > 0) {
        $message = "Roll Number already exists";
        $toastClass = "#007bff"; // Primary color
    } else {
        // Call the stored procedure to register the student
        $stmt = $pdo->prepare("CALL RegisterStudent(:roll, :name, :phone, :pwd, :photo)");
        $stmt->bindParam(":roll", $roll, PDO::PARAM_INT);
        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
        $stmt->bindParam(":phone", $phone_no, PDO::PARAM_STR);
        $stmt->bindParam(":pwd", $password, PDO::PARAM_STR);
        $stmt->bindParam(":photo", $photo, PDO::PARAM_LOB);

        try {
            if ($stmt->execute()) {
                $message = "Account created successfully";
                $toastClass = "#28a745"; // Success color
            } else {
                throw new Exception($stmt->errorInfo()[2]);
            }
        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
            $toastClass = "#dc3545"; // Danger color
        }

        $stmt = null;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome and custom CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-light">
    <?php include 'navbar.php'; ?>
    <div class="container p-5 d-flex flex-column align-items-center">
        <?php if ($message): ?>
            <div class="toast align-items-center text-white border-0"
                role="alert" aria-live="assertive" aria-atomic="true"
                style="background-color: <?php echo $toastClass; ?>;">
                <div class="d-flex">
                    <div class="toast-body">
                        <?php echo $message; ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>

        <form method="post" class="form-control mt-5 p-4" enctype="multipart/form-data" style="max-width:380px;">
            <div class="row text-center">
                <i class="fa fa-user-circle-o fa-3x mt-1 mb-2" style="color: green;"></i>
                <h5 class="p-4" style="font-weight: 700;">Create Your Account</h5>
            </div>
            <div class="mb-2">
                <label for="username"><i class="fa fa-user"></i> User Name</label>
                <input type="text" name="name" id="username" class="form-control" required>
            </div>
            <div class="mb-2 mt-2">
                <label for="roll"><i class="fa fa-hashtag"></i> Roll Number</label>
                <input type="text" name="roll" id="roll" class="form-control" required>
            </div>
            <div class="mb-2 mt-2">
                <label for="phone"><i class="fa fa-phone"></i> Phone Number</label>
                <input type="text" name="phone" id="phone" class="form-control" required>
            </div>
            <div class="mb-2 mt-2">
                <label for="photo"><i class="fa fa-file"></i> Photo</label>
                <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
            </div>
            <div class="mb-2 mt-2">
                <label for="password"><i class="fa fa-lock"></i> Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="mb-2 mt-3">
                <button type="submit" class="btn btn-success" style="font-weight: 600;">Create Account</button>
            </div>
            <div class="mb-2 mt-4">
                <p class="text-center" style="font-weight: 600; color: navy;">I have an Account <a href="login.php" style="text-decoration: none;">Login</a></p>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let toastElList = [].slice.call(document.querySelectorAll('.toast'));
        let toastList = toastElList.map(function(toastEl) {
            return new bootstrap.Toast(toastEl, {
                delay: 3000
            });
        });
        toastList.forEach(toast => toast.show());
    </script>
</body>

</html>