<?php
require_once '../include/dbh.inc.php';
session_start();

$message = "";
$toastClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eid = $_POST['eid'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT password FROM examiner_password WHERE EID = :eid");
    $stmt->bindParam(":eid", $eid, PDO::PARAM_INT);
    $stmt->execute();
    $db_password = $stmt->fetchAll();

    if ($db_password) {
        if ($password === $db_password[0]['password']) {
            $stmt = $pdo->prepare("SELECT * FROM examiners WHERE EID = :eid");
            $stmt->bindParam(":eid", $eid, PDO::PARAM_INT);
            $stmt->execute();
            $admin = $stmt->fetch();

            if ($admin) {
                $_SESSION['admin'] = true;
                $_SESSION['eid'] = $eid;
                $_SESSION['admin_name'] = $admin['name'];
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $message = "Admin details not found.";
                $toastClass = "bg-warning";
            }
        } else {
            $message = "Incorrect password";
            $toastClass = "bg-danger";
        }
    } else {
        $message = "Admin not found";
        $toastClass = "bg-warning";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-light">
    <?php include 'navbar.php'; ?>
    <div class="container p-5 d-flex flex-column align-items-center">
        <?php if ($message): ?>
            <div class="toast align-items-center text-white <?php echo $toastClass; ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body"><?php echo $message; ?></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>
        <form method="post" class="form-control mt-5 p-4" style="max-width:380px;">
            <div class="row">
                <i class="fa fa-user-secret fa-3x mt-1 mb-2" style="text-align: center; color: blue;"></i>
                <h5 class="text-center p-4" style="font-weight: 700;">Admin Login</h5>
            </div>
            <div class="mb-3">
                <label for="eid"><i class="fa fa-id-badge"></i> Admin ID</label>
                <input type="text" name="eid" id="eid" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password"><i class="fa fa-lock"></i> Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary" style="font-weight: 600;">Login as Admin</button>
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