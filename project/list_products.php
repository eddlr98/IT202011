<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
$query = "";
$results = [];
$selectedCtg = '';
$dbQuery = "SELECT name, id, price, category, quantity, description, visibility, user_id from Products WHERE 1 = 1 AND visibility = 1";
$sort = "default";
$params = [];
if (isset($_POST["query"])) {
    $query = $_POST["query"];
}

$db = getDB();
$stmt = $db->prepare("SELECT distinct category from Products;");
$r = $stmt->execute();
if ($r) {
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
else {
    flash("There was a problem fetching the results");
}

if (isset($_POST["Search"])) {

    $selectedCtg = $_POST['category'];
    if ($selectedCtg != -1){
        $dbQuery .= " AND category = :cat ";
        $params[":cat"] = $selectedCtg; 
    }
    if ($query != "") {
        $dbQuery .= " AND name LIKE :q ";
        $params[":q"] = $query;
    }
    if(isset($_POST["sort"])) {
        $sort = "price";
        $dbQuery .= " ORDER BY $sort ASC";
    }
    
    $dbQuery .= " LIMIT 10";

    $db = getDB();
    $stmt = $db->prepare($dbQuery);
    $r = $stmt->execute($params);
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results");
    }
}


?>
<h3>Search Products</h3>
<form method="POST">
    <div class="form-group">    
    <select name="category" value="<?php echo $result["category"];?>" >
            <option value="-1">None</option>
            <?php foreach ($categories as $ctg): ?>
                <option value="<?php safer_echo($ctg["category"]); ?>"
                ><?php safer_echo($ctg["category"]); ?></option>
            <?php endforeach; ?>
        </select>
        <input name="query" placeholder="Search" value="<?php safer_echo($query); ?>"/>
        <input type="submit" value="Search" name="Search"/>
        <label>Sort by Ascending Price<input type="radio" value ="sort" name = "sort"/></label>
        
    </div>
</form>

<div class="results">
    <?php if (count($results) > 0): ?>
        <div class="list-group">
        <?php foreach ($results as $r): ?>
                    <div class="list-group-item">
                    <div>
                        <div>Name: <?php safer_echo($r["name"]); ?></div>
                    </div>
                    <div>
                        <div>Price: $<?php safer_echo($r["price"]); ?></div>
                    </div>
                    <div>
                        <?php if ($r["quantity"] < 10): ?>
                        
                        <div><?php safer_echo( $r["quantity"] . " left in stock."); ?></div>
                   <?php endif;?>
                    </div>
                    <div>
                        <div>Category: <?php safer_echo($r["category"]); ?></div>
                    </div>
                    <div>
                        <a type="button" href="view_products.php?id=<?php safer_echo($r['id']); ?>">View Details</a>
                    </div>
                </div> 
        <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>
<?php require(__DIR__ . "/partials/flash.php");