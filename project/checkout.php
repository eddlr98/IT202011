<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (!is_logged_in()) {
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}
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
        <input type="text" name="streetLine1" class="form-control" placeholder="Street Line 1" >
    </div>
    <div class="input-group mb-3" style="width: 30rem;">
        <input type="text" name="streetLine2" class="form-control" placeholder="Street Line 2" >
    </div>
    <div class="input-group mb-3" style="width: 30.6rem;">
        <input type="text" name="city" class="form-control" placeholder="City" >
        <input type="text" name="state" class="form-control" placeholder="State" >
        <input type="text" name="zipCode" class="form-control" placeholder="Zip Code" >
    </div>

    
    
    <input type="hidden" name="subtotal" value="<?php echo $subtotal; ?>"/>
    <input type="hidden" name="cquantity" value="<?php echo $r["quantity"]; ?>"/>
    <input type="hidden" name="pquantity" value="<?php echo $r["pquantity"]; ?>"/>

<h4>2 Payment Method</h4>

<div class="form-group">
		<div class="form-check">
			<input class="form-check-input" type="radio" name="visibility" id="visibility" value="1">
			<label class="form-check-label" for="visibility">
				Cash
			</label>
		</div>
		<div class="form-check">
		<input class="form-check-input" type="radio" name="visibility" id="visibility" value="0" checked>
			<label class="form-check-label" for="visibility">
				American Express
			</label>
        </div>
        <div class="form-check">
		<input class="form-check-input" type="radio" name="visibility" id="visibility" value="0" checked>
			<label class="form-check-label" for="visibility">
				Visa
			</label>
        </div>
        <div class="form-check">
		<input class="form-check-input" type="radio" name="visibility" id="visibility" value="0" checked>
			<label class="form-check-label" for="visibility">
				MasterCard
			</label>
        </div>
        <div class="form-check">
		<input class="form-check-input" type="radio" name="visibility" id="visibility" value="0" checked>
			<label class="form-check-label" for="visibility">
				Paypal
			</label>
		</div>
	</div>

<h4>3 Review Items</h4>







<h6>Amount on Card</h6>
<div class="input-group mb-3" style="width: 20rem; float: inline-end">
  <div class="input-group-prepend">
    <span class="input-group-text">$</span>
  </div>
  <input type="text" class="form-control" aria-label="Card Amount">
</div>

<button style= "margin: 0; float: left;" type="submit" class="btn btn-success">Place your order</button>

<?php require(__DIR__ . "/partials/flash.php"); ?>