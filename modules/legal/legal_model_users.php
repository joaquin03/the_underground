<style>
  .modal {

    display: block;
    position: fixed;
    z-index: 999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.98);
    font-size: 13px;
  }

  .modal-content {
    margin-top: 50px;
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
  }
  .modal .title-modal {
    text-align: center;
    display: block;
    margin: auto;
  }

  .modal-content p, .modal-content ul {
    text-align: justify; /* Justify text for better readability */
  }
  .modal-content a{
    text-decoration: underline !important;
    color: #FF4A4A;
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

  .logo-image {
    display: inline-block;
    background-image: url(/images/layout/logo_under.png);

    float: left;

    width: 29px;
    height: 84px;
    margin: 0px;
  }
  @media (max-width: 768px) {
    .modal-content {
     width: 80%;
    }
  }

  .modal-content button:hover {
    opacity: 0.8; /* Slightly see-through */
  }
</style>
</head>
<body>
<?php
  // PHP code here if needed
?>
<div class="modal fade" id="memberModal" tabindex="-1" role="dialog" aria-labelledby="memberModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <span class="title-container">
          <img src="/images/layout/logo_under.png" class="logo">
          <span class="title-modal">Update in terms of service</span>
        </span>
        <p>The Terms of Service have been updated. You can review the updated Terms of Service <a target="_blank" href="/?mod=legal&file=terms">here</a>.</p>
        <p>Please review and accept the new terms to continue using our services. </p>
      </div>
      <div class="modal-body">
        <form id="updateMemberForm" method="post" action="/?mod=legal&file=tos_accept">
          <button type="submit" class="agree">I accept the updated Terms of Service</button>
        </form>
      </div>
    </div>
  </div>
</div>
