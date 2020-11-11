<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
    <h3>Create Cart</h3>
    <form method="POST">
        <label>Product ID</label>
        <input type="number" min="1" name="product_id"/>
        <label>Quantity</label>
        <input type="number" min="0" name="quantity"/>
        <label>Price</label>
        <input type="number" min="0" name="price"/>
        <input type="submit" name="save" value="Create"/>
    </form>

<?php
if (isset($_POST["save"])) {
    //TODO add proper validation/checks
    $pid = $_POST["product_id"];
    $quan = $_POST["quantity"];
    $price = $_POST["price"];
    $user = get_user_id();
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO Cart (product_id, quantity, price, user_id) VALUES(:pid, :quan, :price,:user)");
    $r = $stmt->execute([
        ":pid" => $pid,
        ":quan" => $quan,
        ":price" => $price,
        ":user" => $user
    ]);
    if ($r) {
        flash("Created successfully with id: " . $db->lastInsertId());
    }
    else {
        $e = $stmt->errorInfo();
        flash("Error creating: " . var_export($e, true));
    }
}
?>
<?php require(__DIR__ . "/partials/flash.php");