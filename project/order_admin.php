<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php

if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>

<?php

$db = getDB();
$stmt = $db->prepare("SELECT count(*) as total from Orders");
$stmt->execute([":id"=>get_user_id()]);
$orderResult = $stmt->fetch(PDO::FETCH_ASSOC);

// just double checking admin status
if(has_role("Admin")){
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM Orders ORDER by created DESC LIMIT 10");
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
        <th scope="col">Order ID</th>
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
              <td><?php safer_echo($order["id"]); ?></td>
              <td><?php safer_echo($order["address"]); ?></td>
              <td>$<?php safer_echo($order["total_price"]); ?></td>              
            </tr>
          <?php endforeach; ?>
      </div>
  </table>
</div>

<?php require(__DIR__ . "/partials/flash.php"); ?>