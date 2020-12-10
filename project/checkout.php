<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (!is_logged_in()) {
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}
?>




<?php
$id = get_user_id();
$db = getDB();
$stmt = $db->prepare("SELECT cart.id, cart.product_id, cart.quantity, cart.price, Product.name as product FROM Cart as cart JOIN Users on cart.user_id = Users.id LEFT JOIN Products Product on Product.id = cart.product_id where cart.user_id = :id ORDER by product");
$r = $stmt->execute([":id" => $id]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>




<div class="card" style="width: 30rem; float: right;">
  <div class="card-body">
    <button style= "margin: 0; float: right;" type="submit" class="btn btn-success">Place your order</button>
    <h5 class="card-title">Order Summary</h5>
    <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>
    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
  </div>
</div>



<h2>Checkout</h2>

<h4>1 Shipping Address</h4>

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
   
    
<h4>2 Payment Method</h4>

<div class="form-group">
		<div class="form-check">
			<input class="form-check-input" type="radio" name="payMethod" id="cash" value="1" checked>
			<label class="form-check-label" for="payMethod1">
				Discover
			</label>
		</div>
		<div class="form-check">
		<input class="form-check-input" type="radio" name="payMethod" id="amex" value="2" >
			<label class="form-check-label" for="payMethod2">
				American Express
			</label>
        </div>
        <div class="form-check">
		<input class="form-check-input" type="radio" name="payMethod" id="visa" value="3" >
			<label class="form-check-label" for="payMethod3">
				Visa
			</label>
        </div>
        <div class="form-check">
		<input class="form-check-input" type="radio" name="payMethod" id="mstrcard" value="4" >
			<label class="form-check-label" for="payMethod4">
				MasterCard
			</label>
        </div>
        <div class="form-check">
		<input class="form-check-input" type="radio" name="payMethod" id="paypal" value="5" >
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

<h4>3 Review Items</h4>

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
        $total = 0;
        foreach ($results as $product):?>
        <tr>
            <td><?php safer_echo($product["product"]); ?></td>
            <td><?php safer_echo($product["price"]); ?></td>
            <td><?php safer_echo($product["quantity"]); ?></td>
            <td> $<?php safer_echo($product["price"]*$product["quantity"]); $total+=$product["price"]*$product["quantity"]; ?></td>              
        </tr>
        <?php endforeach; ?>
    </div>
</table>

<div class="card" style="width: 30rem; float: left;">
  <div class="card-header">Order Total: $<?php safer_echo($total); ?></div>
  <div class="card-body">
    <button style= "margin: 0; float: left;" type="submit" class="btn btn-success">Place your order</button>
  </div>
</div>

<?php require(__DIR__ . "/partials/flash.php"); ?>