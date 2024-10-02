<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   setcookie('user_id', create_unique_id(), time() + 60*60*24*30, '/');
   header('location:index.php');
}

if(isset($_POST['check'])){

   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }

   // if the hotel has total 30 rooms 
   if($total_rooms >= 30){
      $warning_msg[] = 'Rooms are not Available';
   }else{
      $success_msg[] = 'Rooms are Available';
   }

}

if(isset($_POST['book'])){

   $booking_id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $rooms = $_POST['rooms'];
   $rooms = filter_var($rooms, FILTER_SANITIZE_STRING);
   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);
   $check_out = $_POST['check_out'];
   $check_out = filter_var($check_out, FILTER_SANITIZE_STRING);
   $adults = $_POST['adults'];
   $adults = filter_var($adults, FILTER_SANITIZE_STRING);
   $childs = $_POST['childs'];
   $childs = filter_var($childs, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }

   if($total_rooms >= 30){
      $warning_msg[] = 'Rooms are not Available';
   }else{

      $verify_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ? AND name = ? AND email = ? AND number = ? AND rooms = ? AND check_in = ? AND check_out = ? AND adults = ? AND childs = ?");
      $verify_bookings->execute([$user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);

      if($verify_bookings->rowCount() > 0){
         $warning_msg[] = 'Room booked alredy!';
      }else{
         $book_room = $conn->prepare("INSERT INTO `bookings`(booking_id, user_id, name, email, number, rooms, check_in, check_out, adults, childs) VALUES(?,?,?,?,?,?,?,?,?,?)");
         $book_room->execute([$booking_id, $user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);
         $success_msg[] = 'Room booked Successfully!';
      }

   }

}

if(isset($_POST['send'])){

   $id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $message = $_POST['message'];
   $message = filter_var($message, FILTER_SANITIZE_STRING);

   $verify_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $verify_message->execute([$name, $email, $number, $message]);

   if($verify_message->rowCount() > 0){
      $warning_msg[] = 'Message sent already!';
   }else{
      $insert_message = $conn->prepare("INSERT INTO `messages`(id, name, email, number, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$id, $name, $email, $number, $message]);
      $success_msg[] = 'Message send Successfully!';
   }

}

?>

<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css?v=<?=$version?>"  >

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- home section starts  -->

<section class="home" id="home">

   <div class="swiper home-slider">

      <div class="swiper-wrapper">

         <div class="box swiper-slide">
            <img src="images/home-img-1.jpg" alt="">
            <div class="flex">
               <h3>Luxurious Rooms</h3>
               <a href="#availability" class="btn">Check Availability</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/home-img-2.jpg" alt="">
            <div class="flex">
               <h3>Foods and Drinks</h3>
               <a href="#reservation" class="btn">Book Now</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/home-img-3.jpg" alt="">
            <div class="flex">
               <h3>Luxurious Halls</h3>
               <a href="#contact" class="btn">Contact us</a>
            </div>
         </div>

      </div>

      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>

   </div>

</section>

<!-- home section ends -->

<!-- availability section starts  -->

<section class="availability" id="availability">

   <form action="" method="post">
      <div class="flex">
         <div class="box">
            <p>Check in <span>*</span></p>
            <input type="date" name="check_in" class="input" required>
         </div>
         <div class="box">
            <p>Check out <span>*</span></p>
            <input type="date" name="check_out" class="input" required>
         </div>
         <div class="box">
            <p>Adults <span>*</span></p>
            <select name="adults" class="input" required>
               <option value="1">1 Adult</option>
               <option value="2">2 Adults</option>
               <option value="3">3 Adults</option>
               <option value="4">4 Adults</option>
               <option value="5">5 Adults</option>
               <option value="6">6 Adults</option>
            </select>
         </div>
         <div class="box">
            <p>Childs <span>*</span></p>
            <select name="childs" class="input" required>
               <option value="-">0 Child</option>
               <option value="1">1 Child</option>
               <option value="2">2 Childs</option>
               <option value="3">3 Childs</option>
               <option value="4">4 Childs</option>
               <option value="5">5 Childs</option>
               <option value="6">6 Childs</option>
            </select>
         </div>
         <div class="box">
            <p>Rooms <span>*</span></p>
            <select name="rooms" class="input" required>
               <option value="1">1 Room</option>
               <option value="2">2 Rooms</option>
               <option value="3">3 Rooms</option>
               <option value="4">4 Rooms</option>
               <option value="5">5 Rooms</option>
               <option value="6">6 Rooms</option>
            </select>
         </div>
      </div>
      <input type="submit" value="check availability" name="check" class="btn">
   </form>

</section>

<!-- availability section ends -->

<!-- about section starts  -->

<section class="about" id="about">

   <div class="row">
      <div class="image">
         <img src="images/about-img-1.jpg" alt="">
      </div>
      <div class="content">
         <h3>Best Staff</h3>
         <p>Experience unparalleled service and hospitality with our dedicated team, ensuring every moment of your stay exceeds expectations. Discover the epitome of luxury and comfort at our hotel, where our staff's commitment to excellence sets us apart.</p>
         <a href="#reservation" class="btn">Book Now</a>
      </div>
   </div>

   <div class="row revers">
      <div class="image">
         <img src="images/about-img-2.jpg" alt="">
      </div>
      <div class="content">
         <h3>Best Foods</h3>
         <p>Savor exquisite culinary delights crafted by our talented chefs, where every dish is a masterpiece of flavor and presentation.</p>
         <a href="#contact" class="btn">Contact us</a>
      </div>
   </div>

   <div class="row">
      <div class="image">
         <img src="images/about-img-3.jpg" alt="">
      </div>
      <div class="content">
         <h3>Swimming Pool</h3>
         <p>Dive into pure bliss in our sparkling swimming pool oasis, where relaxation meets rejuvenation amidst serene surroundings.</p>
         <a href="#availability" class="btn">Check Availability</a>
      </div>
   </div>

</section>

<!-- about section ends -->

<!-- services section starts  -->

<section class="services">

   <div class="box-container">

      <div class="box">
         <img src="images/icon-1.png" alt="">
         <h3>Food & Drinks</h3>
         
      </div>

      <div class="box">
         <img src="images/icon-2.png" alt="">
         <h3>Outdoor Dining</h3>
         
      </div>

      <div class="box">
         <img src="images/icon-3.png" alt="">
         <h3>Beach View</h3>
         
      </div>

      <div class="box">
         <img src="images/icon-4.png" alt="">
         <h3>Decorations</h3>
         
      </div>

      <div class="box">
         <img src="images/icon-5.png" alt="">
         <h3>Swimming Pool</h3>
         
      </div>

      <div class="box">
         <img src="images/icon-6.png" alt="">
         <h3>Resort Beach</h3>
         
      </div>

   </div>

</section>

<!-- services section ends -->

<!-- reservation section starts  -->

<section class="reservation" id="reservation">

   <form action="" method="post">
      <h3>Book Now</h3>
      <div class="flex">
         <div class="box">
            <p>Name <span>*</span></p>
            <input type="text" name="name" maxlength="50" required placeholder="Enter Name" class="input">
         </div>
         <div class="box">
            <p>Email <span>*</span></p>
            <input type="email" name="email" maxlength="50" required placeholder="Enter Email" class="input">
         </div>
         <div class="box">
            <p>Phone <span>*</span></p>
            <input type="number" name="number" maxlength="10" min="0" max="9999999999" required placeholder="Enter Number" class="input">
         </div>
         <div class="box">
            <p>Rooms <span>*</span></p>
            <select name="rooms" class="input" required>
               <option value="1" selected>1 room</option>
               <option value="2">2 Rooms</option>
               <option value="3">3 Rooms</option>
               <option value="4">4 Rooms</option>
               <option value="5">5 Rooms</option>
               <option value="6">6 Rooms</option>
            </select>
         </div>
         <div class="box">
            <p>Check in <span>*</span></p>
            <input type="date" name="check_in" class="input" required>
         </div>
         <div class="box">
            <p>Check out <span>*</span></p>
            <input type="date" name="check_out" class="input" required>
         </div>
         <div class="box">
            <p>Adults <span>*</span></p>
            <select name="adults" class="input" required>
               <option value="1" selected>1 adult</option>
               <option value="2">2 Adults</option>
               <option value="3">3 Adults</option>
               <option value="4">4 Adults</option>
               <option value="5">5 Adults</option>
               <option value="6">6 Adults</option>
            </select>
         </div>
         <div class="box">
            <p>Childs <span>*</span></p>
            <select name="childs" class="input" required>
               <option value="0" selected>0 Child</option>
               <option value="1">1 Child</option>
               <option value="2">2 Childs</option>
               <option value="3">3 Childs</option>
               <option value="4">4 Childs</option>
               <option value="5">5 Childs</option>
               <option value="6">6 Childs</option>
            </select>
         </div>
      </div>
      <input type="submit" value="book now" name="book" class="btn">
   </form>

</section>

<!-- reservation section ends -->

<!-- gallery section starts  -->

<section class="gallery" id="gallery">

   <div class="swiper gallery-slider">
      <div class="swiper-wrapper">
         <img src="images/gallery-img-1.jpg" class="swiper-slide" alt="">
         <img src="images/gallery-img-2.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-3.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-4.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-5.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-6.webp" class="swiper-slide" alt="">
      </div>
      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- gallery section ends -->

<!-- contact section starts  -->

<section class="contact" id="contact">

   <div class="row">

      <form action="" method="post">
         <h3>Send Message</h3>
         <input type="text" name="name" required maxlength="50" placeholder="Enter Name" class="box">
         <input type="email" name="email" required maxlength="50" placeholder="Enter Email" class="box">
         <input type="number" name="number" required maxlength="10" min="0" max="9999999999" placeholder="Enter Number" class="box">
         <textarea name="message" class="box" required maxlength="1000" placeholder="Enter Message" cols="30" rows="10"></textarea>
         <input type="Submit" value="Send Message" name="send" class="btn">
      </form>

      <div class="faq">
         <h3 class="title">FAQ</h3>
         <div class="box active">
            <h3>How to cancel?</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Natus sunt aspernatur excepturi eos! Quibusdam, sapiente.</p>
         </div>
         <div class="box">
            <h3>Is there any vacancy?</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa ipsam neque quaerat mollitia ratione? Soluta!</p>
         </div>
         <div class="box">
            <h3>What are payment methods?</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa ipsam neque quaerat mollitia ratione? Soluta!</p>
         </div>
         <div class="box">
            <h3>How to claim coupons codes?</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa ipsam neque quaerat mollitia ratione? Soluta!</p>
         </div>
         <div class="box">
            <h3>What are the age requirements?</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa ipsam neque quaerat mollitia ratione? Soluta!</p>
         </div>
      </div>

   </div>

</section>

<!-- contact section ends -->

<!-- reviews section starts  -->

<section class="reviews" id="reviews">

   <div class="swiper reviews-slider">

      <div class="swiper-wrapper">
         <div class="swiper-slide box">
            <img src="images/pic-1.png" alt="">
            <h3>Dev Agrawal</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptates blanditiis optio dignissimos eaque aliquid explicabo.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-2.png" alt="">
            <h3>Priya Sharma</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptates blanditiis optio dignissimos eaque aliquid explicabo.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-3.png" alt="">
            <h3>Dhruv Saxena</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptates blanditiis optio dignissimos eaque aliquid explicabo.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-4.png" alt="">
            <h3>Neha Kapoor</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptates blanditiis optio dignissimos eaque aliquid explicabo.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-5.png" alt="">
            <h3>Abhimanyu Vishwakarma</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptates blanditiis optio dignissimos eaque aliquid explicabo.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-6.png" alt="">
            <h3>Shweta Sharma</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptates blanditiis optio dignissimos eaque aliquid explicabo.</p>
         </div>
      </div>

      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- reviews section ends  -->





<?php include 'components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<?php include 'components/message.php'; ?>

</body>
</html>