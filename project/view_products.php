<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
//we'll put this at the top so both php block have access to it
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}
?>
<?php
//fetching
$result = [];
if (isset($id)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT product.id, name, quantity, price, description, category, user_id, Users.username FROM Products as product JOIN Users on product.user_id = Users.id where product.id = :id");
    $r = $stmt->execute([":id" => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        $e = $stmt->errorInfo();
        flash($e[2]);
    }
}
?>

<?php
//
    $userID = get_user_id();
    $db = getDB();
    $stmt = $db->prepare("SELECT Orders.id,OrderItems.product_id FROM Orders JOIN OrderItems where Orders.user_id = :id AND OrderItems.order_id = Orders.id");
    $r = $stmt->execute([":id"=>$userID]);
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $ordered = false;
    foreach($orderItems as $item):
        if($item["product_id"]==$_GET["id"]){
            $ordered = true;
        }
    endforeach;
?>




<?php if (isset($result) && !empty($result)): ?>
    <div class="card">
        <div class="card-title">
            <h3><?php safer_echo($result["name"]); ?></h3>
        </div>
        <div class="card-body">
            <div>
                <h5>-- Stats --</h5>
                <div><b>Quantity: </b><?php safer_echo($result["quantity"]); ?></div>
                <div><b>Description: </b><?php safer_echo($result["description"]); ?></div>
                <div><b>Category: </b><?php safer_echo($result["category"]); ?></div>
                <div><b>Owned by: </b><?php safer_echo($result["username"]); ?></div>
            </div>
        </div>
    </div>
<?php else: ?>
    <p>Error looking up id...</p>
<?php endif; ?>
<?php require(__DIR__ . "/partials/flash.php");