<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php

if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>

<?php

// get all the info about the orders limited to 10 at a time 
$db = getDB();
$stmt = $db->prepare("SELECT * FROM Orders ORDER by created DESC LIMIT 10");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<?php
  $profit = 0;
  foreach ($orders as $order):?>
  <?php $profit+=$order["total_price"]; ?>
  <?php endforeach; ?>


  <div class="card" style="width: 20em; float: right;">
    <div class="card-body">
      <h5 class="card-title">Total Profit: $<?php safer_echo($profit);?></p></h5>       
    </div>
  </div>

<form method="_POST">
  <select class="form-select" aria-label="Default select example">
    <?php foreach ($categories as $ctg): ?>
      <option value="<?php safer_echo($ctg["category"]); ?>"
      ><?php safer_echo($ctg["category"]); ?></option>
    <?php endforeach; ?>
  </select>

</form>

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
          $profit = 0;
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