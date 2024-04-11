<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magical Event Entry</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6vP0k0lVj8BcMxFpwW3KwPsAwVoiuBxHnxD4GKIObL5XsFL+2N8H5lyJvGoeK2o" crossorigin="anonymous">
</head>
<body>

<div class="navbar">
  <div class="logo">EzTickEntry</div>
  <div class="search-area">
  <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20">
    <path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128s57.3-128 128-128 128 57.2 128 128-57.3 128-128 128z"/>
  </svg>
    <input type="text" class="search-box" id="search-box" placeholder="Search events">
    <input type="date" class="date-picker" id="date-picker">
  </div>
  <div class="locale-switcher">
    <button onclick="changeLanguage('EN')">EN</button>
    <button onclick="changeLanguage('FR')">FR</button>
  </div>
  <!--<button class="host-event">Host An Event</button>-->
  <button class="create-account" onclick="openRegisterModal()">Host An Event</button>
  <button class="create-account" onclick="openRegisterModal()">Create Account</button>

  <button class="login" onclick="openLoginModal()">Log In</button>

</div>

<div id="registerModal" class="rmodal" style="z-index:1000000;">
  <div class="rmodal-content"style="width: 390px;">
    <span class="close" onclick="closeModal()">&times;</span>
    <?php include 'register.php'; ?>
  </div>
</div>


<div id="loginModal" class="lmodal" style="z-index:1000000;">
  <div class="lmodal-content" style="width: 390px;">
    <span class="close" onclick="closeLoginModal()">&times;</span>
    <?php include 'login.php'; ?>
  </div>
</div>


<div id="search-results"></div>

<div class="content-wrapper">
  <div class="envelope-container"></div> <!-- Envelopes will be dynamically added here -->
  <div class="modal" id="eventModal"> <!-- Modal Structure -->
    <div class="modal-content">
      <span class="close">&times;</span>
        <img src="https://rawcdn.githack.com/naomikonlack/WEBTECHGITDEMO/0b3bdde78c1994ae1499bd7e5a923b7fe37e6498/DALL%C2%B7E%202024-03-30%2015.42.48%20-%20Envision%20a%20dynamic%20and%20heartwarming%20scene%20at%20an%20event%20entrance,%20where%20a%20gatekeeper,%20donning%20a%20casual%20yet%20professional%20look,%20is%20using%20a%20smartphone%20to%20d.webp" alt="Event" class="modal-image" style="width: 600px;">

        <p>You've been invited to a magical event!</p>
        <button class="btn" onclick="openLoginModal()">Book a Ticket</button>
        
    </div>
  </div>

  <div class="parent-container">
    <div class="container">
      <div class="image1"></div>
      <div class="image2"></div>
    </div>
  </div>



  <img src="https://rawcdn.githack.com/naomikonlack/WEBTECHGITDEMO/d23fd3d71881b09759c77c0eb655c169375a1e22/happy-multicultural-friends-having-fun-summer-festival-cheerful-multiracial-students-embrace_158001-4204.jpg.avif" style="position: absolute; left: -2%; top: 10px; width: 1300px; z-index: 10001;-webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 100%, transparent);
  mask-image: linear-gradient(to right, transparent, black 10%, black 100%, transparent);"> 




    
  <img src="https://raw.githubusercontent.com/naomikonlack/WEBTECHGITDEMO/d23fd3d71881b09759c77c0eb655c169375a1e22/oakImage-1556811830330-superJumbo.jpg" style="position: absolute; left: 44%; top: 8px; width: 1120px; z-index: 10001;-webkit-mask-image: linear-gradient(to right, transparent, black 24%, black 90%, transparent);
  mask-image: linear-gradient(to right, transparent, black 24%, black 90%, transparent);">



  <div class="w_container" style="margin-top: -350px; z-index: 100000; margin-left:-1%">
    <div class="word"><div class="letter">G</div><div class="letter">E</div><div class="letter">T</div></div>
    <div class="word"><div class="letter">Y</div><div class="letter">O</div><div class="letter">U</div><div class="letter">R</div></div>
    <div class="word"><div class="letter">E</div><div class="letter">A</div><div class="letter">Z</div><div class="letter">Y</div></div>
    <div class="word"><div class="letter">T</div><div class="letter">I</div><div class="letter">C</div><div class="letter">K</div><div class="letter">E</div><div class="letter">T</div></div>
    <div class="word"><div class="letter">E</div><div class="letter">N</div><div class="letter">T</div><div class="letter">R</div><div class="letter">I</div><div class="letter">E</div><div class="letter">S</div></div>
    <div class="word"><div class="letter">F</div><div class="letter">O</div><div class="letter">R</div></div>
    <div class="word"><div class="letter">E</div><div class="letter">V</div><div class="letter">E</div><div class="letter">N</div><div class="letter">T</div><div class="letter">S</div></div>
  </div>

  <div class="w2_container" style="margin-top: 50px; z-index: 1000000; margin-left:-1%">
    <div class="word"><div class="letter">A</div><div class="letter">L</div><div class="letter">L</div></div>
    <div class="word"><div class="letter">A</div><div class="letter">R</div><div class="letter">O</div><div class="letter">U</div><div class="letter">N</div><div class="letter">D</div></div>
    <div class="word"><div class="letter">T</div><div class="letter">H</div><div class="letter">E</div></div>
    <div class="word"><div class="letter">W</div><div class="letter">O</div><div class="letter">R</div><div class="letter">L</div><div class="letter">D</div></div>
    </div>
    <div class="about" >
    <p style="font-size: 1.2em; text-align: justify;">
    Embark on an effortless journey to entertainment with EzTickEntry, your ultimate portal to the world of events. We're here to transform how you discover, book, and manage tickets for an extensive array of entertainment spectacles. Whether it's immersing yourself in cinema, feeling the pulse of live performances, or celebrating momentous private occasions, EzTickEntry is your ticket to an unforgettable experience.
    </p>
  </div>

  <br>
  <h2 class="Top Events" style="color: #4B0082; margin-top: 100px; padding-left: 174px;">Our Categories</h2>
  <div class="categories-container" style="padding-left: 174px;">
    <div class="category">
      <img src="https://rawcdn.githack.com/naomikonlack/WEBTECHGITDEMO/dccb68e43ece051e4b01ad41b121c945e049a524/people-s-emotions-cinema1-1024x683.jpg" alt="Cinema Tickets">
      <span class="category-name">Cinema <br>/Theatres Tickets</span>
    </div>
    <div class="category">
      <img src="https://rawcdn.githack.com/naomikonlack/WEBTECHGITDEMO/dccb68e43ece051e4b01ad41b121c945e049a524/rock-on-rear-view-of-a-music-fan-dancing-with-her-2023-01-04-20-11-49-utc.jpg" alt="Live Performances">
      <span class="category-name">Live Performances<br>/Festivals Tickets</span>
    </div>
  </div>


  <h2 style="color: #4B0082;  margin-top: 70px; padding-left: 174px;">Why Choose EzTickEntry?</h2>

  <div>
  <div style = "margin-top: -40px;" class="about" > 
  
    <h2 style= "display: flex; color: #FF8C00; justify-content: space-between; align-items: center; margin-bottom: -150px;" > Buying Of Tickets </h2>

 
  
    <div style="display: flex; justify-content: space-between; align-items: center;">
    <p style="flex: 1; margin-right: 20px; padding-left: 150px; text-align: left;"><strong>Secure your spot effortlessly for your next great adventure </strong> <br> <strong>With Ease from all around the world.<strong></p>
      <img src="https://rawcdn.githack.com/naomikonlack/WEBTECHGITDEMO/685520d3d02debcee05ea7c8ca7652dd4f2417d6/EZITIC%20KENTRY.heic" alt="Buy Tickets" style="width: 500px; margin-left: -190px;">
      <img src="https://rawcdn.githack.com/naomikonlack/WEBTECHGITDEMO/966e1544270dc9d978ba8394988d4b2907bb43d4/Buy%20ticket%202.heic" alt="Buy Tickets" style="width: 500px; margin-left: -100px; ">
    </div>
  </div>
  </div>
  
  <br>
    
  <div style = "margin-top: 50px;" class="about1" > 
    
  <h2 style= "display: flex; color: #FF8C00; justify-content: space-between; align-items: center; margin-bottom: -50px;" > Organizing Events</h2>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">


    <div class="w3_container" style="flex: 1; margin-right: 20px; padding-left: 150px; text-align: left;">
      <div class="line">
        <div class="word3"><div class="letter3">H</div><div class="letter3">O</div><div class="letter3">S</div><div class="letter3">T</div></div>
        <div class="spacer"></div>
        <div class="word3"><div class="letter3">A</div><div class="letter3">N</div><div class="letter3">D</div></div>
      </div>
      <div class="line">
        <div class="word3"><div class="letter3">M</div><div class="letter3">A</div><div class="letter3">N</div><div class="letter3">A</div><div class="letter3">G</div><div class="letter3">E</div></div>
        <div class="spacer"></div>
        <div class="word3"><div class="letter3">Y</div><div class="letter3">O</div><div class="letter3">U</div><div class="letter3">R</div></div>
      </div>
      <div class="line">
        <div class="word3"><div class="letter3">E</div><div class="letter3">V</div><div class="letter3">E</div><div class="letter3">N</div><div class="letter3">T</div><div class="letter3">S</div></div>
        <div class="spacer"></div>
        <div class="word3"><div class="letter3">W</div><div class="letter3">I</div><div class="letter3">T</div><div class="letter3">H</div></div>
        <div class="spacer"></div>
        <div class="word3"><div class="letter3">E</div><div class="letter3">A</div><div class="letter3">S</div><div class="letter3">E</div></div>
     </div>
    </div>
      <img src="https://rawcdn.githack.com/naomikonlack/WEBTECHGITDEMO/d23fd3d71881b09759c77c0eb655c169375a1e22/EZTICKEN.png" alt="Create Events" style="width: 800px; -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 100%, transparent);
      mask-image: linear-gradient(to right, transparent, black 10%, black 100%, transparent);"> 

    </div>
  </div>
    <br>
    <div style="color: #4B0082;  margin-top: 150px; padding-left: 174px;">
      <h2 style="color: #4B0082;">Your next adventure is a click away.</h2>
      <p style="color: #FF8C00;">Open the Invitations bellow to get started</p>
    </div>
</div>


</div>

<div class="footer">
    <div class="footer-content"> <!-- Wrapper for flexbox -->
        <ul class="social-links">
            <li><a href="https://www.instagram.com/yourusername" target="_blank"><i class="fab fa-instagram"></i> Instagram</a></li>
            <li><a href="https://www.youtube.com/yourchannel" target="_blank"><i class="fab fa-youtube"></i> YouTube</a></li>
            <!-- Add more social links here -->
        </ul>
        <ul class="footer-links">
            <li><a href="/about">About Us</a></li>
            <li><a href="/contact">Contact</a></li>
            <li><a href="/privacy">Privacy Policy</a></li>
            <!-- Add more internal links here -->
        </ul>
    </div>
    <p>© 2024 EzTickEntry. All rights reserved.</p>
</div>

</div>


<style>
/* General Styles */
body, html {
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
    font-family: 'Arial', sans-serif;
    background: linear-gradient(to top right, rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0.6)), 
            linear-gradient(to top right, rgba(33, 150, 243, 0.3) 0%, rgba(200, 200, 200, 0.3) 100%);
background: linear-gradient(to top right, rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0.6)), 
            linear-gradient(to top right, rgba(106, 17, 203, 0.3) 0%, rgba(37, 117, 252, 0.3) 50%, rgba(200, 200, 200, 0.3) 100%);
background: linear-gradient(to top right, rgba(255, 255, 255, 0.7) 0%, rgba(200, 200, 200, 0.3) 100%);
    overflow-x: hidden;
}

.content-wrapper {
    /* This is a general suggestion, adjust based on your actual content */
    min-height: 100vh; /* Minimum height to fill the viewport */
    position: relative; /* Establish a stacking context */
    padding-bottom: 1000px; /* Space for the footer */
}

.container {
  position: relative;
  width: 1000px;
  height: 562px; /* Adjust based on your images' aspect ratio */
  left: 20%; 
  top: -50px;
  
}


.word {
  display: flex;
  justify-content: flex-start; /* Align items to the start */
  gap: 1px; /* Reduced space between letters */
  margin-left: 100px; 
  /* Slight margin to push content to the right */
  z-index: 100000;
}



.word {
    display: flex;
    justify-content: flex-start;
    gap: 2px; /* Adjust the space between letters */
    z-index: 100000;
  left: 100%;
  }
  .letter {
    color: #4B0082; /* Indigo */
    font-size: 2em;
    font-weight: bold;
    text-shadow: 0 0 8px #FFD700, /* Golden glow */
                 0 0 10px #FFD700, 
                 0 0 12px #FFD700, 
                 0 0 14px #FF8C00, /* Slight fiery glow */
                 0 0 16px #FF8C00;
    transform: rotate(-10deg);
    animation: dance 2s infinite alternate;
    z-index: 100000;
  }
  @keyframes dance {
    0% { transform: rotate(-5deg) translateY(0px); }
    50% { transform: rotate(5deg) translateY(-10px); }
    100% { transform: rotate(-5deg) translateY(0px); }
  }

  .word3 {
    display: flex;
    justify-content: flex-start;
    gap: 2px; /* Adjust the space between letters */
    z-index: 100000;
  left: 100%;
  }
  .letter3 {
    color: white; /* Indigo */
    font-size: 2em;
    font-weight: bold;
 
    z-index: 100000;
  }

  .line {
  display: flex;
  }

  .spacer {
    width: 10px; /* Adjust this value to control the gap size */
    display: inline-block; /* This makes sure the spacer is in line with the words */
  }



/* Envelope Styles */
.envelope {
    position: absolute;
    bottom: 0; /* Position envelope at the bottom of the page */
    width: 120px; /* Adjust based on your preference */
    height: 80px; /* Adjust based on your preference */
    background-color: #fdd835; /* Initial color - will change per envelope */
    border-radius: 10px;
    cursor: pointer;
    animation: float 5s ease-in-out infinite;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Arial', sans-serif;
    font-size: 12px;
    font-weight: bold;
    background: linear-gradient(to bottom, rgba(0,0,0,0.5), rgba(0,0,0,0.9));
    color: #fff;
    text-align: center;
    text-shadow: 0px 1px 2px rgba(0,0,0,0.7);
    /* Envelope shadow for 3D effect */
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    z-index: 10; /* Lower z-index than the modal */
}

.envelope::before {
    content: '';
    position: absolute;
    bottom: 100%;
    left: 0;
    right: 0;
    height: 50%;
    background: inherit;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
}

/* Revised animation for floating envelopes without rotation */
@keyframes float {
    0%, 100% {
        transform: translateY(0); /* Start and end at the original position */
        opacity: 10;
    }
    50% {
        transform: translateY(-20vh); /* Move up by 20vh at the midpoint */
        opacity: 1;
    }
}

.modal {
    display: none;
    position: fixed;
    z-index: 1000; /* Higher z-index to be above floating envelopes */
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
    z-index: 10001;
}


.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 50%;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    border-radius: 10px;
    z-index: 10001;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover, .close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

/* Login/Register Button Styles */
.login-register {
    position: fixed;
    top: 20px;
    right: 20px;
    display: flex;
    gap: 10px;
}

.btn {
    border: none;
    padding: 10px 20px;
    background-color: #0000FF;
    color: #fff;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #e91e63;
}

/* About Section Styles */
.about {
    position: relative;
    bottom: -90px;
    left: 50%;
    transform: translateX(-50%);
    width: 80%;
    background-color: rgba(255, 255, 255, 0.9);
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    z-index: 10000;
}

.about h2, .about p {
    margin: 10px 0;
}


.about1 {
    position: relative;
    bottom: -90px;
    left: 50%;
    transform: translateX(-50%);
    width: 80%;
    background: linear-gradient(to bottom, rgb(115, 85, 175), rgb(58, 0, 103), rgb(25, 25, 112)); 
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    z-index: 10000;
}


.about1 h2, .about1 p {
    margin: 10px 0;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .modal-content {
        width: 90%;
    }
}

/* Footer Styles */
.footer {
    background-color: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(10px);/* Dark background for contrast */
    color: #fff; /* White text color */
    text-align: center; /* Center-align the text */
    padding: 20px; /* Padding for spacing */
    width: 100%; /* Full width */
    position: relative; /* Added to make z-index effective */
    z-index: 10003333; /* High z-index to ensure it's above most elements */
}


.footer-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    /* Ensure the footer is at least at the bottom */
    position: relative;
    height: 60px; /* Set this to the height of your footer */
    bottom: -100;
    clear: both;
}


.social-links, .footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    gap: 20px;
}

.social-links li a, .footer-links li a {
    color: #fff;
    text-decoration: none;
    transition: color 0.3s ease;
}

.social-links li a:hover, .footer-links li a:hover {
    color: #e91e63; /* A bright color for hover states */
}

.footer p {
    text-align: center;
    margin-top: 20px;
    font-size: 14px;
    opacity: 0.8;
}



.navbar {
    position: sticky;
    top: 0;
  display: flex;
  align-items: center;
  background-color: #ffffff;
  padding: 10px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  z-index: 1000000;
}

.navbar .logo {
  color: #4B0082;
  font-size: 24px;
  font-weight: bold;
  margin-right: 20px;
  cursor: pointer;
  transition: transform 0.2s;
}

.navbar .logo:hover {
  transform: scale(1.1);
}

.navbar .search-box {
  flex: 1;
  padding: 10px;
  margin: 0 15px;
  border: 2px solid #4B0082;
  border-radius: 2px;
  transition: border-width 0.2s;
}

.navbar .search-box:focus {
  border-width: 3px;
}

.navbar button {
  background-color: #e7e7e7;
  border: none;
  padding: 10px 15px;
  margin-left: 10px;
  cursor: pointer;
  transition: background-color 0.2s, box-shadow 0.2s;
}

.navbar button:hover {
  background-color: #ddd;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.navbar .login {
  background-color: #4B0082;
  color: white;
}

.navbar .locale-switcher {
  display: flex;
}

.navbar .search-area {
  display: flex;
  flex: 1;
  align-items: center;
  margin: 0 15px;
}

.navbar .search-box,
.navbar .date-picker {
  padding: 10px;
  border: 2px solid #4B0082;
  border-radius: 2px;
  margin-right: 5px;
}

.navbar .date-picker {
  cursor: pointer;
}

#search-results {
  position: absolute;
  top: 50px;
  left: calc(50% - 150px);
  width: 300px;
  background: white;
  border: 1px solid #ddd;
  border-radius: 0 0 4px 4px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  display: none;
}

#search-results p {
  padding: 10px;
  margin: 0;
  border-bottom: 1px solid #eee;
  cursor: pointer;
  transition: background-color 0.2s;
}

#search-results p:last-child {
  border-bottom: none;
}

#search-results p:hover {
  background-color: #f9f9f9;
}

.categories-container {
    display: flex;
    overflow-x: auto;
    gap: 20px; /* Increase space between items */
    padding: 20px 0; /* Add padding for top and bottom */
    align-items: center; /* Align items vertically */
    top: 100000px;
  }

  .category {
    border-radius: 10px; /* Larger border-radius */
    overflow: hidden;
    position: relative;
    width: 450px; /* Increase width */
    height: 200px; /* Specify height */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Add shadow for depth */
    flex-shrink: 0; /* Prevents resizing */

  }

  .category img {
    width: 100%;
    height: 100%; /* Match height of parent */
    object-fit: cover; /* Ensure image covers the area */
    display: block;
  }

  .category-name {
    position: absolute;
    bottom: 20px; /* Increase spacing from the bottom */
    left: 20px; /* Increase spacing from the left */
    color: white;
    font-size: 24px; /* Increase font size */
    font-weight: bold;
    text-shadow: 2px 2px 6px #000000; /* Enhance text shadow */
  }

  /* The Modal (background) */
.rmodal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content Box */
.rmodal-content {
  position: relative;
  background-color: #fefefe; 
  margin: 10% auto; /* 10% from the top and centered */
  padding: 20px;
  border: 1px solid #888;
  width: 50%; /* Adjust based on your preference */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  border-radius: 5px; /* Rounded corners */
  z-index: 20000; /* Increased z-index */
}

  /* The Modal (background) */
  .lmodal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content Box */
.lmodal-content {
  position: relative;
  background-color: #fefefe; 
  margin: 10% auto; /* 10% from the top and centered */
  padding: 20px;
  border: 1px solid #888;
  width: 50%; /* Adjust based on your preference */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  border-radius: 5px; /* Rounded corners */
  z-index: 20000; /* Increased z-index */
}


/* The Close Button */
.close {
  color: #aaa;
  position: absolute;
  top: 10px;
  right: 25px;
  font-size: 28px;
  font-weight: bold;
  cursor: pointer;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
}

/* Prevent scrolling when modal is open */
.no-scroll {
  overflow: hidden;
}


</style>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const envelopeContainer = document.querySelector('.envelope-container');
    const modal = document.getElementById('eventModal');
    const closeModal = document.querySelector('.close');
    const colors = ['#e57373', '#f06292', '#ba68c8', '#9575cd', '#7986cb']; // Define more colors as needed


    function generateEnvelopes() {
    const numberOfEnvelopes = 24; // Total number of envelopes
    const envelopesPerRow = 6; // Number of envelopes per row
    const envelopeWidth = 120; // Match the width in your CSS
    const envelopeHeight = 80; // Match the height in your CSS
    const horizontalGap = 30; // Horizontal gaps between envelopes
    const verticalGap = 60; // Increased vertical gaps between rows
    const rowHeight = envelopeHeight + verticalGap; // Calculate row height considering the increased vertical gap
    const totalRowWidth = envelopesPerRow * envelopeWidth + (envelopesPerRow - 1) * horizontalGap; // Total width of all envelopes and gaps in a row

    // Calculate the left offset to center the envelopes
    const containerWidth = envelopeContainer.offsetWidth; // Get the width of the container
    const leftOffset = (containerWidth - totalRowWidth) / 2; // Calculate the left offset to center the envelopes

    // Calculate the total number of rows needed
    const totalRows = Math.ceil(numberOfEnvelopes / envelopesPerRow);
    // Calculate the total height needed for all rows
    const totalHeightNeeded = totalRows * rowHeight - verticalGap; // Subtract one verticalGap because there's no gap after the last row

    // Calculate the starting bottom position to align envelopes at the bottom of the container
    const containerHeight = envelopeContainer.offsetHeight; // Get the height of the container
    const bottomOffset = -1000; // Increase this value to move the envelopes higher from the bottom
    const startBottomPos = containerHeight - totalHeightNeeded - bottomOffset;

      for (let i = 0; i < numberOfEnvelopes; i++) {
          const envelope = document.createElement('div');
          envelope.classList.add('envelope');
          envelope.textContent = 'Open...';
          envelope.style.backgroundColor = colors[i % colors.length]; // Cycle through colors array for each envelope

          const rowNumber = Math.floor(i / envelopesPerRow);
          const positionInRow = i % envelopesPerRow;

          // Set horizontal position with gap and additional left offset
          envelope.style.left = `${leftOffset + positionInRow * (envelopeWidth + horizontalGap)}px`;

          // Adjust bottom position based on the row number, starting from the calculated startBottomPos
          envelope.style.bottom = `${startBottomPos - rowNumber * rowHeight}px`;

          envelope.addEventListener('click', function() {
              modal.style.display = 'block';
          });

          envelopeContainer.appendChild(envelope);
      }
    }

    generateEnvelopes();

    // Close modal events
    closeModal.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
});

</script>

<script>
// Function to open the login modal
function openLoginModal() {
  document.getElementById('loginModal').style.display = 'block';
  document.getElementById('registerModal').style.display = 'none'; // Ensure register modal is closed
  document.body.classList.add('no-scroll'); // Prevent scrolling
}

// Function to open the register modal
function openRegisterModal() {
  document.getElementById('registerModal').style.display = 'block';
  document.getElementById('loginModal').style.display = 'none'; // Ensure login modal is closed
  document.body.classList.add('no-scroll'); // Prevent scrolling
}

// Function to close the modals
function closeModal() {
  document.getElementById('loginModal').style.display = 'none';
  document.getElementById('registerModal').style.display = 'none';
  document.body.classList.remove('no-scroll'); // Re-enable scrolling
}

// Function to validate registration form
function validateRegistrationForm() {
    var firstName = document.getElementById('firstName').value;
    var lastName = document.getElementById('lastName').value;
    var email = document.getElementById('email').value;
    var phone = document.getElementById('phone').value;
    var password = document.getElementById('password').value;
    var confirmPassword = document.getElementById('confirmPassword').value;

    // Regular expressions for validation
    var nameRegex = /^[a-zA-Z '-]+$/;
    var emailRegex = /^[a-z0-9._%+-]+@(ashesi\.edu\.gh|gmail\.com|yahoo\.com|hotmail\.com|outlook\.com)$/;
    var phoneRegex = /^\+(?:[1-9]{1}[0-9]{1,2})\s?(?:[0-9]{6,14})$/;
    var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{10,}$/;

    // Perform validation
    if (!nameRegex.test(firstName)) {
        alert('Please enter a valid first name.');
        return false;
    }
    if (!nameRegex.test(lastName)) {
        alert('Please enter a valid last name.');
        return false;
    }
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address.');
        return false;
    }
    if (!phoneRegex.test(phone)) {
        alert('Please enter a valid phone number in international format.');
        return false;
    }
    if (!passwordRegex.test(password)) {
        alert('Password must be at least 10 characters long and contain at least one uppercase letter, one lowercase letter, one digit, and one special character.');
        return false;
    }
    if (password !== confirmPassword) {
        alert('Passwords do not match.');
        return false;
    }

    // If all validations pass, return true
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
  // Event listeners for close buttons
  document.querySelectorAll('.close').forEach(button => {
    button.addEventListener('click', closeModal);
  });

  // Close the modal if the user clicks outside of it
  window.onclick = function(event) {
    if (event.target == document.getElementById('loginModal') || event.target == document.getElementById('registerModal')) {
      closeModal();
    }
  };

  // Switching from the login form to the register form
  var loginToRegisterLink = document.querySelector('#loginModal .form-container a');
  if (loginToRegisterLink) {
    loginToRegisterLink.addEventListener('click', function(event) {
      event.preventDefault(); // Prevent default link behavior
      openRegisterModal();
    });
  }

  // Switching from the register form to the login form
  var registerToLoginLink = document.querySelector('#registerModal .form-container a');
  if (registerToLoginLink) {
    registerToLoginLink.addEventListener('click', function(event) {
      event.preventDefault(); // Prevent default link behavior
      openLoginModal();
    });
  }
});
</script>



</body>
</html>

