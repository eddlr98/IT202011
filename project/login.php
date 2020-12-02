<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<head>
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
    <!-- Custom styles for this template -->
    <link href="static/css/styles.css" rel="stylesheet">
  </head>

<body class="text-center">
    <form method="POST" class="form-signin">
        <img class="mb-4" src="../assets/brand/bootstrap-solid.svg" alt="" width="72" height="72">
            <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
                <label for="email" class="sr-only">Email address</label>
                <input class="form-control" type="email" id="email" name="email" placeholder="Email address" required autofocus>
                <label for="username" class="sr-only">Username:</label>
                <input class="form-control" type="username" id="username" name="username" placeholder="Username"/>
                <label for="password" class="sr-only">Password</label>
                <input class="form-control" type="password" id="p1" name="password" placeholder="Password" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="login" value="Login">Sign in</button>
        <p class="mt-5 mb-3 text-muted">&copy; 2020</p>
    </form>
</body>



<?php
if (isset($_POST["login"])) {
    $email = null;
    $username = null;
    $password = null;
    if (isset($_POST["email"])) {
        $email = $_POST["email"];
    }
    if (isset($_POST["username"])) {
        $username = $_POST["username"];
    }
    if (isset($_POST["password"])) {
        $password = $_POST["password"];
    }
    $isValid = true;
    if ((!isset($email) && !isset($username)) || !isset($password)) {
        $isValid = false;
        flash("Email, username or password missing");
    }
    if (!isset($email) && $isValid) {
	    if (!strpos($email, "@")) {
	        $isValid = false;
	        //echo "<br>Invalid email<br>";
	        flash("Invalid email");
	    }
    }
    if ($isValid) {
        $db = getDB();
        if (isset($db)) {
            $stmt = $db->prepare("SELECT id, email, username, password from Users WHERE email = :email LIMIT 1");

            $params = array(":email" => $email);
            $r = $stmt->execute($params);
            //echo "db returned: " . var_export($r, true);
            $e = $stmt->errorInfo();
            if ($e[0] != "00000") {
                //echo "uh oh something went wrong: " . var_export($e, true);
                flash("Something went wrong, please try again");
            }
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && isset($result["password"])) {
                $password_hash_from_db = $result["password"];
                if (password_verify($password, $password_hash_from_db)) {
                    $stmt = $db->prepare("
SELECT Roles.name FROM Roles JOIN UserRoles on Roles.id = UserRoles.role_id where UserRoles.user_id = :user_id and Roles.is_active = 1 and UserRoles.is_active = 1");
                    $stmt->execute([":user_id" => $result["id"]]);
                    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    unset($result["password"]);//remove password so we don't leak it beyond this page
                    //let's create a session for our user based on the other data we pulled from the table
                    $_SESSION["user"] = $result;//we can save the entire result array since we removed password
                    if ($roles) {
                        $_SESSION["user"]["roles"] = $roles;
                    }
                    else {
                        $_SESSION["user"]["roles"] = [];
                    }
                    //on successful login let's serve-side redirect the user to the home page.
                    flash("Log in successful");
                    die(header("Location: home.php"));
                }
                else {
                    flash("Invalid password");
                }
            }
            else {
                flash("Invalid user");
            }
        }
    }
    else {
        flash("There was a validation issue");
    }
}
?>
<?php require(__DIR__ . "/partials/flash.php");
