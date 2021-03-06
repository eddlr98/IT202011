<?php require_once(__DIR__ . "../partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>

<form method="POST">
	<label>Name</label>
	<input name="name" placeholder="Name"/>
	<label>Quantity</label>
	<input type="number" min="1" name="quantity"/>
	<label>Price</label>
	<input type="number" min="0.00" name="price"/>
	<label>Description</label>
	<input name="description" placeholder="Description"/>
	<input type="submit" name="save" value="Create"/>
</form>

<?php
if(isset($_POST["save"])){
	//TODO add proper validation/checks
	$name = $_POST["name"];
	$quan = $_POST["quantity"];
	$price = $_POST["price"];
	$desc = $_POST["description"];
	$mod = date('Y-m-d H:i:s');
	$crtd = date('Y-m-d H:i:s');
	$user = get_user_id();
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO Products (name, quantity, price, description, modified, created, user_id) VALUES(:name, :quan, :price, :desc, :mod, :crtd, :user)");
	$r = $stmt->execute([
		":name"=>$name,
		":quan"=>$quan,
		":price"=>$price,
		":desc"=>$desc,
		":mod"=>$mod,
		":crtd"=>$crtd,
		":user"=>$user
	]);
	if($r){
		flash("Created successfully with id: " . $db->lastInsertId());
	}
	else{
		$e = $stmt->errorInfo();
		flash("Error creating: " . var_export($e, true));
	}
}
?>
<?php require(__DIR__ . "../partials/flash.php");
