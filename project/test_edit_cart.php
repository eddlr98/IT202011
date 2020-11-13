<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
//we'll put this at the top so both php block have access to it
if (isset($_GET["id"])) {
    $cid = $_GET["id"];
}
?>
<?php
//saving
if (isset($_POST["save"])) {
    //TODO add proper validation/checks
    $name = $_POST["name"];
    $productID = $_POST["product_id"];
    if ($cid <= 0) {
        $cid = null;
    }
    $quantity = $_POST["quantity"];
    $user = get_user_id();
    $db = getDB();
    if (isset($id)) {
        $stmt = $db->prepare("UPDATE Cart set quantity=:quantity where id=:id");
        $r = $stmt->execute([
            ":quantity"=>$quantity,
            ":id" => $cid
        ]);
        if ($r) {
            flash("Updated successfully with id: " . $cid);
        }
        else {
            $e = $stmt->errorInfo();
            flash("Error updating: " . var_export($e, true));
        }
    }
    else {
        flash("ID isn't set, we need an ID in order to update");
    }
}
?>
<?php
//fetching
$result = [];
if (isset($cid)) {
    $cid = $_GET["id"];
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM Cart where id = :id");
    $r = $stmt->execute([":id" => $cid]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
}
//get pids for dropdown
$db = getDB();
$stmt = $db->prepare("SELECT id,name from Products LIMIT 10");
$r = $stmt->execute();
$productID = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <h3>Edit Cart</h3>
    <form method="POST">
        <label>Product</label>
        <select name="id" value="<?php echo $result["id"];?>" >
            <option value="-1">None</option>
            <?php foreach ($Products as $Product): ?>
                <option value="<?php safer_echo($productID["id"]); ?>" <?php echo ($result["product_id"] == $productID["id"] ? 'selected="selected"' : ''); ?>
                ><?php safer_echo($productID["name"]); ?></option>
            <?php endforeach; ?>
        </select>
        <label>Quantity</label>
        <input type="number" min="0" name="quantity" value="<?php echo $result["quantity"]; ?>"/>
        <input type="submit" name="save" value="Update"/>
    </form>


<?php require(__DIR__ . "/partials/flash.php");