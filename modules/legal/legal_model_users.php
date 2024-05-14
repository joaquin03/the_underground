<style>
  .modal {
    display: block; /* Show by default */
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
    margin-top: 2rem;
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
    color: #FFF;
    float: right;
    background-color: #FF4A4A;

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
        <h2 class="modal-title" id="memberModalLabel">Update in terms of service</h2>
        <p>The Terms of Service have been updated. You can review the updated Terms of Service <a target="_blank" href="/?mod=legal&file=terms">here</a>.</p>
        <p>Please review and accept the new terms to continue using our services. </p>
      </div>
      <div class="modal-body">
        <form id="updateMemberForm" method="post" action="/?mod=legal&file=tos_accept">
          <button type="submit" class="btn btn-primary">I accept the updated Terms of Service</button>
        </form>
      </div>
    </div>
  </div>
</div>
