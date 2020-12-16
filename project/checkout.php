<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (!is_logged_in()) {
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}
?>

<h2>Checkout</h2>

<?php
$id = get_user_id();
$db = getDB();
$stmt = $db->prepare("SELECT cart.id, cart.product_id, cart.quantity, cart.price, Product.name as product FROM Cart as cart JOIN Users on cart.user_id = Users.id LEFT JOIN Products Product on Product.id = cart.product_id where cart.user_id = :id ORDER by product");
$r = $stmt->execute([":id" => $id]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<form method="POST">
  <div class="card" style="width: 50em; float: right;">
    <div class="card-body">
      <button style= "margin: 0; float: right;" id="placeOrder" name="submit" type="submit" form="form1" value="Submit"  class="btn btn-success">Place your order</button>
      <h5 class="card-title">Order Summary</h5>
      <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>
      <p class="card-text">
      <table class="table table-striped" style="width: 50rem;">
        <thead>
          <tr>
            <th scope="col">Product Name</th>
            <th scope="col">Price</th>
            <th scope="col">Quantity</th>
            <th scope="col">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <div class="results">     
              <?php
              $totPrice = 0;
              foreach ($results as $product):?>
              <tr>
                  <td><?php safer_echo($product["product"]); ?></td>
                  <td><?php safer_echo($product["price"]); ?></td>
                  <td><?php safer_echo($product["quantity"]); ?></td>
                  <td>$<?php safer_echo($product["price"]*$product["quantity"]); $totPrice+=$product["price"]*$product["quantity"]; ?></td>              
              </tr>
              <?php endforeach; ?>
          </div>
      </table>
          <b>Order Total: $<?php safer_echo($totPrice);?></b></p>
      </div>
  </div>
</form>
<?php
  if(isset($_POST["submit"])) {
    
    // 1. street address validaiton
    $totAddr = null;

    $addrLine1 = $_POST["addrLine1"];
    $substr = explode(" ", $addrLine1);
    // looking for types and "123 main st." format for address line 1
    if ((sizeof($substr) >= 3) && gettype($substr[0] == "integer") && (is_string($_POST["city"])) && (is_string($_POST["state"]))) {
      $totAddr = $_POST["addrLine1"] . ", " . $_POST["city"] . ", " .$_POST["state"]."  ".$_POST["zipCode"];
      if(isset($_POST["addrLine2"])){
        $totAddr = $_POST["addrLine1"] . " " . $_POST["addrLine2"] . ", " . $_POST["city"] . ", " .$_POST["state"]."  ".$_POST["zipCode"]; 
      }
    } else {
        flash("Please enter a valid shipping address.");
    }

    // 2. payment validation
    $payment = null;
    $price = $totPrice;
    $created = date('Y-m-d H:i:s');
    $id = get_user_id();

    if(isset($_POST["payMethod"])){
      $payment = $_POST["payMethod"];
      if($payment==-1){
        flash("Please select a valid payment method.");
      }
    }
    //check shop quantity of product before order palced
    $db = getDB();
    $stmt = $db->prepare("SELECT Cart.product_id, Cart.quantity, Products.name, Products.quantity as stock FROM Cart Join Products on Cart.product_id = Products.id JOIN Users on Cart.user_id = Users.id where Cart.user_id=:id");
    $r = $stmt->execute([":id" => $id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $inStock = true;
    foreach($items as $item):
      if($item["quantity"]>$item["stock"]){
        flash("You need to update your cart, ".$item["name"]." only has ".$item["stock"]."  left.");
        $inStock = false;
      }elseif($item["stock"]==0){
        flash("You need to update your cart, ".$item["name"]." is out of stock.");
        $inStock = false;
      }
    endforeach;

    // Finalize validity
    if ($totAddr && ($payment!="-1") && $inStock) {
      $db = getDB();
      $stmt = $db->prepare("INSERT INTO Orders (user_id,created,address,payment_method,total_price) VALUES(:uid,:created,:address,:pay,:price)");
      $r = $stmt->execute([
        ":uid"=>$id,
        ":created"=>$created,
        ":address"=>$totAddr,
        ":pay"=>$payment,
        ":price"=>$price
      ]);
      if(!$r){
          $e = $stmt->errorInfo();
          flash("Error placing order: " . var_export($e, true));
      }

      //gets latest order id
      $db = getDB();
      $stmt = $db->prepare("SELECT id from Orders where user_id = :id ORDER by created DESC LIMIT 1");
      $r = $stmt->execute([":id"=>$id]);
      $order = $stmt->fetch(PDO::FETCH_ASSOC);

      $oid = $order["id"];
      //add order info into orderitems
      $db = getDB();
      $stmt = $db->prepare("SELECT cart.product_id, cart.quantity, cart.price FROM Cart as cart JOIN Users on cart.user_id = Users.id LEFT JOIN Products Product on Product.id = cart.product_id where cart.user_id = :id ORDER by Product.id");
      $r = $stmt->execute([":id" => $id]);
      $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

      foreach($orderItems as $orderItem):
        $pid = $orderItem["product_id"];
        $Quantity = $orderItem["quantity"];
        $unitPrice = $orderItem["price"];

        $db = getDB();
        $stmt = $db->prepare("INSERT INTO OrderItems (order_id,product_id,quantity,unit_price) VALUES(:order,:pid,:quan,:uprice)");
        $r = $stmt->execute(["order"=>$oid,":pid"=>$pid,":quan"=>$Quantity,":uprice"=>$unitPrice]);

        //reduce product stock by amount bought
        $db = getDB();
        $stmt = $db->prepare("UPDATE Products SET quantity=quantity-:q where id=:pid");
        $r = $stmt->execute([":pid"=>$pid,":q"=>$Quantity]);
    endforeach;

      $userID = get_user_id();
      $db = getDB();
      $stmt = $db->prepare("DELETE FROM Cart where user_id=:id");
      $r = $stmt->execute([":id" => $userID]);

      // redirect to order confirmation page
      flash("Order Confirmed");
      die(header("Location: orderconfirmation.php"));
    }
}
?>

<form method="POST" id="form1">

<h4>- Shipping Address -</h4>

    <div class="input-group mb-3" style="width: 30rem;">
        <input type="text" name="addrLine1" class="form-control" placeholder="Street address, P.O. box, company name, c/o" required>
    </div>
    <div class="input-group mb-3" style="width: 30rem;">
        <input type="text" name="addrLine2" class="form-control" placeholder="Apartment, suite, unit, building, floor, etc." >
    </div>
    <div class="input-group mb-3" style="width: 30.6rem;">
        <input type="text" name="city" class="form-control" placeholder="City" required>
        <input type="text" name="state" class="form-control" placeholder="State" required>
        <input type="text" name="zipCode" class="form-control" placeholder="Zip Code" pattern="[0-9]{5}" required>
    </div>
   
    
<h4>- Payment Method -</h4>

<div class="form-group">
    <div class="form-check">
			<input class="form-check-input" type="radio" name="payMethod" id="cash" value="-1" checked>
			<label class="form-check-label" for="payMethod0">
				--Please Select--
			</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" name="payMethod" id="cash" value="discover" >
			<label class="form-check-label" for="payMethod1">
				Discover
			</label>
		</div>
		<div class="form-check">
		<input class="form-check-input" type="radio" name="payMethod" id="amex" value="amex" >
			<label class="form-check-label" for="payMethod2">
				American Express
			</label>
        </div>
        <div class="form-check">
		<input class="form-check-input" type="radio" name="payMethod" id="visa" value="visa" >
			<label class="form-check-label" for="payMethod3">
				Visa
			</label>
        </div>
        <div class="form-check">
		<input class="form-check-input" type="radio" name="payMethod" id="mstrcard" value="mastercard" >
			<label class="form-check-label" for="payMethod4">
				MasterCard
			</label>
        </div>
        <div class="form-check">
		<input class="form-check-input" type="radio" name="payMethod" id="paypal" value="paypal" >
			<label class="form-check-label" for="payMethod5">
				Paypal
			</label>
		</div>
	</div>

<h6>Amount on Card</h6>
<div class="input-group mb-3" style="width: 20rem; float: inline-end">
  <div class="input-group-prepend">
    <span class="input-group-text">$</span>
  </div>
  <input type="text" class="form-control" aria-label="Card Amount">
</div>



</form>

<?php require(__DIR__ . "/partials/flash.php"); ?>