<?php

$con = mysqli_connect("localhost", "root", "","ecommerce");

function getSession(){
	if(isset($_COOKIE['sessionId'])){
		$id = $_COOKIE['sessionId'];
	}
	else {
	session_start();
	$id = session_id();
	setcookie('sessionId', $id);
	}
	
	return $id;
}

function display_product($run_product) {
	while($row_product=mysqli_fetch_array($run_product)) {
		$pro_id = $row_product['product_id'];
		$pro_cat = $row_product['product_cat'];
		$pro_title = $row_product['product_title'];
		$pro_price = $row_product['product_price'];
		$pro_image = $row_product['product_image'];
		
		echo "
				<div class='col-xs-4 col-md-3'>
					<div class='product_card'>
					<a href='details.php?pro_id=$pro_id'><img src='admin/product_images/$pro_image' height='200px' width='200px'/></a>
					<p><b>$pro_title</br></p>
					<p style='float:right; color:green; padding-right:2px;'><b>$$pro_price</b></p>
					</br>
					</div>
				</div>
			";
	}	
}

function getCategory(){
	global $con;
	$get_category = "select * from categories";
	$run_category = mysqli_query($con, $get_category);
	
	while ($row_category = mysqli_fetch_array($run_category)) {
		$cat_id = $row_category['cat_id'];
		$cat_title = $row_category['cat_title'];
		
		echo "<li><p><a href='category.php?category=$cat_id&'>$cat_title</a></p></li>";
	}
}

function getRand(){
	global $con;
	global $display_product;
	
	$get_product = "select * from products order by RAND() LIMIT 0,8";
	$run_product = mysqli_query($con, $get_product);
	
	display_product($run_product);
}

function getNew($cat_id){
	global $con;
	global $display;
	
	if ($cat_id == '0'){
		$get_product = "select * from products order by product_id desc limit 8";
	} else {
		$get_product = "select * from products where product_cat= '$cat_id' order by product_id desc limit 8";	
	}
	
	$run_product = mysqli_query($con, $get_product);
	display($run_product);
}

function getPopular($cat_id){
	global $con;
	global $display_product;
	
	if ($cat_id == '0'){
		$get_product = "select * from products where popular='1' limit 8";
	} else {
		$get_product = "select * from products where product_cat= '$cat_id' and popular='1' order by RAND() limit 8";	
	}
	
	$run_product = mysqli_query($con, $get_product);
	display_product($run_product);
}

function getCategoryProduct($cat_id){
	global $con;
	global $display_product;
	
	if($cat_id == 0){
		$get_product = "select * from products order by product_id";
	} else {
	$get_product = "select * from products where product_cat='$cat_id' order by product_id";
	}
	
	$run_product = mysqli_query($con, $get_product);
	display_product($run_product);
}

function getDetails($pro_id){
	global $con;
	
	$get_product = "select * from products where product_id='$pro_id'";
	$run_product = mysqli_query($con, $get_product);
	
	while($row_product=mysqli_fetch_array($run_product)) {
		$pro_id = $row_product['product_id'];
		$pro_cat = $row_product['product_cat'];
		$pro_title = $row_product['product_title'];
		$pro_price = $row_product['product_price'];
		$pro_desc = $row_product['product_desc'];
		$pro_image = $row_product['product_image'];
		
		echo "
				<div class='col-xs-6'>
					<p>$pro_title</p>
					<a href='details.php?pro_id=$pro_id'><img src='admin/product_images/$pro_image' height='200px' width='200px'/></a>
					<p><b>$$pro_price</b></p>
					<br>
				</div>
				<div class='col-xs-6'>
					<p>$pro_desc</p>
					<form>
					<input type='number' id='qty'></input>
					<button type='button' id='add_cart' value=$pro_id>Add to Cart</button>
				</div>
			";
	}
}

if(isset($_GET['add_cart'])) {
		
	global $con;
	$cart_id = getSession();
	$pro_id = $_GET['add_cart'];
	$qty = $_GET['qty'];
	
	$check_pro = "select * from cart where cart_id = '$cart_id' AND p_id='$pro_id'";
	$run_check = mysqli_query($con, $check_pro);
	
		if(mysqli_num_rows($run_check)>0) {
			$update_pro = "update cart set qty = qty + $qty where cart_id = '$cart_id' AND p_id='$pro_id'";
			$run_pro = mysqli_query($con, $update_pro);
		}
		else {
			$insert_pro = "insert into cart (p_id, qty, cart_id) values ('$pro_id', '$qty', '$cart_id')";
			$run_pro = mysqli_query($con, $insert_pro);
		}
}

?>