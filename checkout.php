<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

if(isset($_POST['order_btn'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $number = $_POST['number'];
   $age=$_POST['age'];
   $gender=$_POST['gender'];
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $method = mysqli_real_escape_string($conn, $_POST['method']);
   $address = mysqli_real_escape_string($conn, 'flat no. '. $_POST['flat'].', '. $_POST['street'].', '. $_POST['city'].', '. $_POST['country'].' - '. $_POST['pin_code']);
   $placed_on = date('d-M-Y');

   $cart_total = 0;
   $cart_products[] = '';

   $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
   if(mysqli_num_rows($cart_query) > 0){
      while($cart_item = mysqli_fetch_assoc($cart_query)){
         $cart_products[] = $cart_item['name'];
         // $sub_total = ($cart_item['price'] * $cart_item['quantity']);
         $cart_total =$cart_total+$cart_item['price'];
      }
   }

   $total_products = implode(', ',$cart_products);

   $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE name = '$name' AND number = '$number' AND age = '$age' AND gender='$gender' AND email = '$email' AND method = '$method' AND address = '$address' AND total_products = '$total_products' AND total_price = '$cart_total'") or die('query failed');

   if($cart_total == 0){
      $message[] = 'your cart is empty';
   }else{
      if(mysqli_num_rows($order_query) > 0){
         $message[] = 'course already enrolled!'; 
      }else{
         if($age>=18){
         mysqli_query($conn, "INSERT INTO `orders`(user_id, name, number, age, gender, email, method, address, total_products, total_price, placed_on) VALUES('$user_id', '$name', '$number', '$age', '$gender','$email', '$method', '$address', '$total_products', '$cart_total', '$placed_on')") or die('query failed');
         $message[] = 'course enrolled successfully!';
         mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
         }else{
            $message[]='you are under 18! you are not eligible!';
         }
      }
   }
   
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>checkout</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="images/steering.png" type="image/png">

</head>

<body>

    <?php include 'header.php'; ?>

    <div class="heading">
        <h3>Enrollment</h3>
        <p> <a href="home.php">Home</a> | Enroll </p>
    </div>

    <section class="display-order">

        <?php  
      $grand_total = 0;
      $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
      if(mysqli_num_rows($select_cart) > 0){
         while($fetch_cart = mysqli_fetch_assoc($select_cart)){
            // $total_price = ($fetch_cart['price'] * $fetch_cart['quantity']);
            $total_price=$grand_total+$fetch_cart['price'];
            $grand_total =$grand_total+$fetch_cart['price'];
   ?>
        <p> <?php echo $fetch_cart['name']; ?> <span>(<?php echo 'Ksh'.$fetch_cart['price']; ?>)</span> </p>
        <?php
      }
   }else{
      echo '<p class="empty">your cart is empty</p>';
   }
   ?>
        <div class="grand-total"> Total : <span>Ksh<?php echo $grand_total; ?>/-</span> </div>

    </section>

    <section class="checkout">

        <form action="" method="post">
            <h3>Course Enrollment Form</h3>
            <div class="flex">
                <div class="inputBox">
                    <span>Your Name :</span>
                    <input type="text" name="name" required placeholder="enter your name">
                </div>
                <div class="inputBox">
                    <span>Your Phone number:</span>
                    <input type="number" name="number" required placeholder="enter your number">
                </div>
                <div class="inputBox">
                    <span>Your age:</span>
                    <input type="number" name="age" required placeholder="enter your age">
                </div>
                <div class="inputBox">
                    <span>Gender :</span>
                    <select name="gender">
                        <option value="none" selected>select</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">other</option>
                    </select>
                </div>
                <div class="inputBox">
                    <span>Your Email :</span>
                    <input type="email" name="email" required placeholder="enter your email">
                </div>
                <div class="inputBox">
                    <span>Payment method :</span>
                    <select name="method">
                        <option value="cash on delivery">Cash</option>
                        <option value="credit card">Credit card</option>
                        <option value="paypal">Paypal</option>
                        <option value="m-pesa">M-pesa</option>
                    </select>
                </div>
                <div class="inputBox">
                    <span>Address line 01 :</span>
                    <input type="number" min="0" name="flat" required placeholder="e.g. flat no.">
                </div>
                <div class="inputBox">
                    <span>Address line 01 :</span>
                    <input type="text" name="street" required placeholder="e.g. street name">
                </div>
                <div class="inputBox">
                    <span>City :</span>
                    <input type="text" name="city" required placeholder="e.g. Nairobi">
                </div>
                <div class="inputBox">
                    <span>State :</span>
                    <input type="text" name="state" required placeholder="e.g. Kajiado">
                </div>
                <div class="inputBox">
                    <span>Country :</span>
                    <input type="text" name="country" required placeholder="e.g. kenya">
                </div>
                <div class="inputBox">
                    <span>Pin code :</span>
                    <input type="number" min="0" name="pin_code" required placeholder="e.g. 123456">
                </div>
            </div>
            <input type="submit" value="Enroll now" class="btn" name="order_btn">
        </form>

    </section>









    <?php include 'footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>