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
    // fetch from orders and orderitems to check if item is there and this user bought it. 
    $userID = get_user_id();
    $db = getDB();
    $stmt = $db->prepare("SELECT Orders.id,OrderItems.product_id FROM Orders JOIN OrderItems where Orders.user_id = :id AND OrderItems.order_id = Orders.id");
    $r = $stmt->execute([":id"=>$userID]);
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // set flag to true if this user bought the item.
    $ordered = false;
    foreach($orderItems as $item):
        if($item["product_id"]==$_GET["id"]){
            $ordered = true;
        }
    endforeach;
?>


<?php
// get rating info from form and add it to table
if(isset($_POST["rating"])){
    $rating = $_POST["rating"];
    $comment = $_POST["comment"];
    $uid = get_user_id();
    $pid = $_GET["id"];
    $created = date('Y-m-d H:i:s');

    $db = getDB();
    $stmt = $db->prepare("INSERT INTO Ratings(product_id, user_id, rating, comment, created) VALUES(:pid, :user, :rating, :comment, :created)");
    $r = $stmt->execute([":pid"=>$pid, ":user"=>$uid, ":rating"=>$rating, ":comment"=>$comment, ":created"=>$created]);

    if($r) {
        flash("Rating submitted successfully, thank you!");
    }else{
        flash("Error: rating denied, try again.");
    }
}
?>

<?php if (isset($result) && !empty($result)): ?>
    <div class="card">
        <div class="card-title">
            <h3><?php safer_echo($result["name"]); ?></h3>
        </div>
        <div class="card-body">
            <div>
                <h5>-- Stats --</h5>
                <div><b>Quantity: </b><?php safer_echo($result["quantity"]); ?> @ $<?php safer_echo($result["price"]); ?> per item.</div>
                <div><b>Description: </b><?php safer_echo($result["description"]); ?></div>
                <div><b>Category: </b><?php safer_echo($result["category"]); ?></div>
                <div><b>Sold by: </b><?php safer_echo($result["username"]); ?></div>
            </div>
        </div>
    </div>
<?php else: ?>
    <p>Error looking up id...</p>
<?php endif; ?>

<?php if($ordered):?>
<br>
<h3>Rate This Item</h3>
    <div>
        <form method="POST">
            <br>
            <label><h6>Rating (1-5):</h6></label>
            <br>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="rating" id="rat1" value="1">
                <label class="form-check-label" for="rating1">
                    1 Star
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="rating" id="rat2" value="2">
                <label class="form-check-label" for="rating2">
                    2 Star
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="rating" id="rat3" value="3">
                <label class="form-check-label" for="rating3">
                    3 Star
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="rating" id="rat4" value="4">
                <label class="form-check-label" for="rating4">
                    4 Star
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="rating" id="rat5" value="5">
                <label class="form-check-label" for="rating5">
                    5 Star
                </label>
            </div>
            <br>
            <label><h6>Comment:</h6></label>
            <div class="input-group mb-3" style="width: 30rem;">
                <input type="text" name="comment" class="form-control" placeholder="Leave a comment here!" required>
            </div>

            <button style= "margin: 0; float: left;" name="rate" type="submit" value="Rate Product"  class="btn btn-success">Submit Rating</button>
        </form>
    </div>
<?php endif; ?>

<?php

$id = $_GET["id"];
$db = getDB();
$stmt = $db->prepare("SELECT Ratings.comment, Ratings.rating, Ratings.created, Users.username FROM Ratings JOIN Users where product_id=:id and Ratings.user_id = Users.id LIMIT 10");
$stmt->bindValue(":id", $id);
$stmt->execute();
$ratings = $stmt->fetchall(PDO::FETCH_ASSOC);

$rated = false;
if($ratings){
    $rated = true;
}

?>
<?php if($rated): ?>
    <div class="results">
        <br>
        <br>
    <div>
        <div><h3>Product Reviews:</h3></div>
    </div>
    <div>
        <br>
    </div>
        <table class="table table-striped" style="width: 50rem;">
            <thead>
            <tr>
                <th scope="col">Reviewed By</th>
                <th scope="col">Rating</th>
                <th scope="col">Comment</th>
                <th scope="col">Date</th>
            </tr>
            </thead>
            <tbody>
            <div class="results">     
                <?php
                foreach ($ratings as $rating):?>
                    <tr>
                    <td><?php safer_echo($rating["username"]); ?></td>
                    <td><?php safer_echo($rating["rating"]); ?> &#9733</td>
                    <td><?php safer_echo($rating["comment"]); ?></td>
                    <td><?php safer_echo($rating["created"]); ?></td>              
                    </tr>
                <?php endforeach; ?>
            </div>
        </table>
    </div>
    <?php else: ?>
    <br>
    <br>
    <div>
        <div><h3>Product Reviews: </h3></div>
    </div>
        <div>Looks like there are no ratings for this item. Be the first to rate it!</div>    
<?php endif; ?>

<?php require(__DIR__ . "/partials/flash.php");