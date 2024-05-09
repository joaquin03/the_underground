<?php
  $bots = ["Googlebot", "Bingbot", "Slurp", "DuckDuckBot", "Baiduspider", "YandexBot", "Sogou"];
  $isBot = false;
  $userAgent = $_SERVER['HTTP_USER_AGENT'];

  foreach ($bots as $bot) {
    if (strpos($userAgent, $bot) !== false) {
      $isBot = true;
      break;
    }
  }
  if ($isBot) {
    return;
  }

?>

<style>
  .modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 999; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
  }

  .modal-content {
    background-color: #fefefe;
    margin: auto; /* Centered horizontally and vertically */
    padding: 40px;
    border: 1px solid #888;
    width: 50%; /* Adapt to the screen size */
    height: auto; /* Adapt content height */
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Shadow for 3D effect */
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
  }

  .modal-content p, .modal-content ul {
    text-align: justify; /* Justify text for better readability */
  }

  button {
    padding: 10px 20px;
    margin-top: 20px;
    cursor: pointer; /* Pointer/hand icon */
    background-color: #4CAF50; /* Green background */
    color: white; /* White text */
    border: none; /* No border */
    border-radius: 5px; /* Rounded corners for the button */
    font-size: 16px; /* Larger font size */
  }

  button:hover {
    opacity: 0.8; /* Slightly see-through */
  }
</style>
</head>
<body>
<?php
  // PHP code here if needed
?>
<div id="myModal" class="modal">
  <div class="modal-content">
    <h1>Warning: This Website is for Adults Only!</h1>
    <p>
      This Website is for use solely by individuals at least 18 years old and the age of majority or age of
      consent as determined by the laws of the jurisdiction from which they are accessing the Website. Age
      requirements might vary depending on local, state, or international laws, and it is your responsibility to
      verify that you meet the legal age requirement in your jurisdiction before accessing this Website. The
      materials available on this Website include graphic visual depictions or descriptions of nudity and sexual
      activity and must not be accessed by anyone who is below the age of majority or the age of consent in
      their jurisdiction. Accessing this Website while underage might be prohibited by law.
    </p>
    <!-- Long text -->
    <p>By clicking “I Agree” below, you state that the following statements are accurate:</p>
    <ul>
      <li>I am at least 18 years old, the age of majority or age of consent in my jurisdiction, and I have the legal right to access adult material in my community.</li>
      <li>I do not find images of nude adults, adults engaged in sexual acts, or other sexual material to be offensive or
        objectionable.</li>
      <li>I will leave this Website immediately if I am offended by its sexual content.</li>
      <li>I will not hold the Website’s owners or its employees responsible for any materials located on the Website.</li>
      <li>I acknowledge that the Website’s Terms of Use governs my use of the Website, and I have reviewed and agree to be bound by the
        <a href="/?mod=legal&file=terms">Terms of Use.</a> </li>
        <p><strong>If you do not agree, click on the “I Disagree” button below and leave the Website.</strong></p>      <!-- More items -->
    </ul>
    <div style="display: flex; width: 100%;">
      <button onclick="agree()" style="flex: 1; margin-right: 10px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; padding: 10px; font-size: 16px; cursor: pointer;">I Agree</button>
      <button onclick="disagree()" style="flex: 1; background-color: darkred; color: white; border: none; border-radius: 5px; padding: 10px; font-size: 16px; cursor: pointer;">I Disagree</button>
    </div>

    <p>Last update date: 5/9/2024</p>
  </div>
</div>
<script>
  window.onload = function() {
    if (sessionStorage.getItem("agreed") !== "true") {
      document.getElementById("myModal").style.display = "flex";
    }
  };

  function agree() {
    sessionStorage.setItem("agreed", "true");
    document.getElementById("myModal").style.display = "none";
  }

  function disagree() {
    window.location.href = "https://www.google.com";
  }
</script>
