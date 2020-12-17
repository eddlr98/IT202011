<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (!is_logged_in()) {
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}
?>

<?php

$db = getDB();
$stmt = $db->prepare("SELECT count(*) as total from Orders where user_id=:id");
$orderResult = $stmt->fetch(PDO::FETCH_ASSOC);
if(has_role("User")){
    $uid = get_user_id();
    $db = getDB();
    $stmt = $db->prepare("SELECT total_price, created, address FROM Orders where user_id=:id ORDER by created DESC LIMIT 10");
    $stmt->bindValue(":id", $uid);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<div class="results">
    <div>
        <div><h3>Orders:</h3></div>
    </div>
    <div>
        <br>
    </div>
    <table class="table table-striped" style="width: 50rem;">
    <thead>
      <tr>
        <th scope="col">Order Created</th>
        <th scope="col">Address</th>
        <th scope="col">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <div class="results">     
          <?php
          foreach ($orders as $order):?>
            <tr>
              <td><?php safer_echo($order["created"]); ?></td>
              <td><?php safer_echo($order["address"]); ?></td>
              <td>$<?php safer_echo($order["total_price"]); ?></td>              
            </tr>
          <?php endforeach; ?>
      </div>
  </table>
</div>

<?php require(__DIR__ . "/partials/flash.php"); ?>