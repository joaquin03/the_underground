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
    display: none;
    position: fixed;
    z-index: 999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.98);
  }

  .modal-content {
    margin: auto;
    padding: 10px 30px;
    border: 1px solid #888;
    width: 60%;
    height: auto;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: #222;
    color: #e2e2e2;
    box-shadow: 0 0 40px 5px rgba(255,255,255,.15);
  }

  .modal .title-container {
    display: flex;
    text-align: center;
    margin: auto;
  }
  .modal .title-container .logo{
    height: 50px;
    margin-right: 20px;
  }
  .modal .title-modal {
    font-size: 24px;
    text-align: center;
    display: block;
    margin-top: 6px;
  }

  .modal-content p, .modal-content ul {
    text-align: justify; /* Justify text for better readability */
  }

  .modal-content button {
    padding: 10px 20px;
    margin-top: 20px;
    cursor: pointer; /* Pointer/hand icon */

    border: none; /* No border */
    border-radius: 5px; /* Rounded corners for the button */
    font-size: 16px; /* Larger font size */
  }
  .agree{
    flex: 1;
    margin-right: 10px;
    background-color: #FF4A4A;
    color: #FFF;
    border: none;
    border-radius: 5px;
    padding: 10px;
    font-size: 16px;
    cursor: pointer;
  }
  .disagree{
    flex: 1;
    background-color: #454545;
    color: white;
    border: none;
    border-radius: 5px;
    adding: 10px;
    font-size: 16px;
    cursor: pointer;
  }
  .modal-content button:hover {
    opacity: 0.8; /* Slightly see-through */
  }


  @media (max-width: 768px) {
    .modal-content {
      padding: 10px 15px;
    }

    .modal p{
      font-size: 11px !important;
    }
    .modal .title-modal{
      font-size: 19px !important;
    }
    .modal-content {
      height: fit-content;
      width: 95%;
      margin: 0.5rem;
    }
    .modal-content ul {
      padding: 0;
      margin-top: 0px;
    }
    .modal-content ul li {
      list-style-type: none;
      font-size: 11px;
    }
    .modal-content button {
      margin-top: 0px;
    }
  }


  }
</style>
</head>
<body>
<?php
  // PHP code here if needed
?>
<div id="myModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <span class="title-container">
         <img src="/images/layout/logo_under.png" class="logo">
        <span class="title-modal">This Website is for Adults Only</span>
      </span>
    </div>
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
      <button onclick="agree()" class="agree">I Agree</button>
      <button onclick="disagree()" class="disagree">I Disagree</button>
    </div>

    <p>Last update date: 05/09/2024</p>
  </div>
</div>
<script>
  window.onload = function() {
    if (localStorage.getItem("agreed") !== "true") {
      document.getElementById("myModal").style.display = "flex";
    }
  };

  function agree() {
    localStorage.setItem("agreed", "true");
    document.getElementById("myModal").style.display = "none";
  }

  function disagree() {
    window.location.href = "https://www.google.com";
  }
</script>
