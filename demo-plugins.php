<?php
/*
 * Plugin Name:       Demo Plugins
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Handle the basics with this plugin.
 * Version:           1.10.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            John Smith
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       demo-plugin
 * Domain Path:       /languages
*/



function feedback_deactivation_click() {
    ?>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {

            // Show the feedback modal when the "Deactivate" button is clicked
              var deactivateButton = document.querySelector(".deactivate a");
              if (deactivateButton) {
                deactivateButton.addEventListener("click", function (event) {
                  event.preventDefault();
                  //console.log('found');
                  document.getElementById('feedback-modal').classList.remove('d-none');
                });
              }

              // Handle the form submission for feedback
              var feedbackForm = document.getElementById("feedback-form");
              if (feedbackForm) {
                feedbackForm.addEventListener("submit", function (event) {
                  event.preventDefault();
                  // collect all data 
                  var feedbackOption = document.querySelector('input[name="rio-feedback-op"]:checked');
                  var feedbackValue = feedbackOption ? feedbackOption.value : '';
                  var anonymousCheckbox = document.getElementById("rio-anonymous");
                  var isAnonymous = anonymousCheckbox.checked;
                  var otherFeedbackInput = document.getElementById("rio-other-feedback");
                  var otherFeedbackValue = feedbackValue === "other" ? otherFeedbackInput.value.trim() : "";
                  // Get the current date and time
                  var currentDate = new Date().toLocaleString();

                  // Get the username (if available and anonymous feedback is false)
                  var username = "";
                  if (!isAnonymous && typeof wpApiSettings !== "undefined" && wpApiSettings.hasOwnProperty("user_display_name")) {
                    username = wpApiSettings.user_display_name;
                  }

                  // Prepare the feedback data as a message
                  var feedbackMessage = "Selected Feedback Option: " + feedbackValue + "\n";
                  feedbackMessage += "Anonymous Feedback: " + isAnonymous + "\n";
                  if (username) {
                    feedbackMessage += "Username: " + username + "\n";
                  }
                  if (otherFeedbackValue) {
                    feedbackMessage += "Other Feedback: " + otherFeedbackValue + "\n";
                  }
                  feedbackMessage += "Date: " + currentDate + "\n";

                  // Send the feedback data via AJAX to a server-side PHP function to send the email
                  // var ajaxData = {
                  //   action: "send_feedback_email",
                  //   feedback: feedbackMessage,
                  // };

                  // // Send AJAX request
                  // jQuery.post(ajaxurl, ajaxData, function (response) {
                  //   // Optional: You can handle the server response here if needed
                  //   // For example, show a success message to the user
                  //   console.log("Feedback sent successfully.");
                  // });

                  // For simplicity, hide the modal after a short delay
                  setTimeout(function () {
                    var feedbackModal = document.getElementById("feedback-modal");
                    if (feedbackModal) {
                      feedbackModal.style.display = "none";
                      deactivatePlugin();
                    }
                  }, 1000);
                });
              }

        });

    function deactivatePlugin() {
      // AJAX request to deactivate the plugin
      var data = {
        action: 'deactivate_plugin',
        plugin: 'demo-plugins/demo-plugins.php'
      };
      // Send AJAX request
      jQuery.post(ajaxurl, data, function (response) {
        // If the response is successful, the plugin is deactivated
        if (response === 'success') {
            location.reload();
        }
      });
    }
    </script>
    <?php
}
add_action('admin_footer', 'feedback_deactivation_click');


add_action('wp_ajax_deactivate_plugin', 'feedback_deactivate_plugin_callback');
function feedback_deactivate_plugin_callback() {
    // Check if the current user has permission to deactivate plugins
    if (current_user_can('activate_plugins')) {
        // Get the plugin file to deactivate (change to your plugin file path)
        $plugin_file = 'demo-plugins/demo-plugins.php';

        // Deactivate the plugin
        deactivate_plugins($plugin_file);

        // Send a success response to the JavaScript function
        echo 'success';
    } else {
        // Send an error response if the user doesn't have the required permission
        echo 'error';
    }

    // Always exit to terminate the AJAX request
    wp_die();
}

// all modal html css and js code 
function rio_feedback_modal(){
    ?>
    <div class="feedback-modal d-none" id="feedback-modal">
        <div id="popup1" class="rio-feedback-overlay">
            <div class="rio-feedback-popup">
                <form id="feedback-form">
                    <div class="rio-feedback-header"><p>Quick Feedback</p></div>
                    <div class="rio-feedback-body">
                        <h3>If you have a moment, please let us know why you are deactivating:</h3>
                        <div class="rio-feedback-options">
                            <div>
                                <input type="radio" id="sudden_stop" name="rio-feedback-op" value="sudden stop">
                                <label for="sudden_stop">The plugin suddenly stopped working</label>
                            </div>
                            <div>
                                <input type="radio" id="short_perid" name="rio-feedback-op" value="need for a short period">
                                <label for="short_perid">I only needed the plugin for a short period</label>
                            </div>
                            <div>
                                <input type="radio" id="broke_website" name="rio-feedback-op" value="broke website">
                                <label for="broke_website">The plugin broke my site</label>
                            </div>
                            <div>
                                <input type="radio" id="no_longer_need" name="rio-feedback-op" value="no longer need">
                                <label for="no_longer_need">I no longer need the plugin</label>
                            </div>
                            <div>
                                <input type="radio" id="found_better_plugin" name="rio-feedback-op" value="found a better plugin">
                                <label for="found_better_plugin">I found a better plugin</label>
                            </div>
                            <div>
                                <input type="radio" id="temporary" name="rio-feedback-op" value="temporary deactivation">
                                <label for="temporary">It's a temporary deactivation. I'm just debugging an issue.</label>
                            </div>
                            <div>
                                <input type="radio" id="other" name="rio-feedback-op" value="other">
                                <label for="other">Other</label>
                            </div>
                            <div class="other_feedback d-none" id="other_feedback_form">
                                <label>Kindly tell us the reason so we can improve.</label>
                                <input type="text" class="tio-form-control" name="other_feedback" value="" id="rio-other-feedback">
                            </div>
                        </div>
                    </div>
                    <div class="rio-feedback-footer">
                        <div class="hidden" id="rio-anonymous-fb">
                            <input type="checkbox" id="rio-anonymous" name="rio-anonymous-feedback" value="anonymous">
                            <label for="anonymous"> Anonymous feedback</label>
                        </div>
                        <div>
                            <button class="rio-feedback-submit-btn" type="submit" id="submit-feedback-btn">Skip & Deactive</button>
                            <button class="rio-feedback-close" href="#" id="cancel-feedback-btn">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        const radioButtons = document.querySelectorAll('.rio-feedback-body input[type="radio"]');
        const targetDiv = document.getElementById('other_feedback_form');
        const anonymous_fb = document.getElementById('rio-anonymous-fb');
        const other_feedback = document.getElementById('rio-other-feedback');
        const rio_fb_submit = document.getElementById('submit-feedback-btn');

        radioButtons.forEach(radioButton => {
            radioButton.addEventListener('change', function() {
                if (this.checked) {
                    anonymous_fb.classList.remove('hidden');  // default anonymous opt hidden
                    rio_fb_submit.textContent = 'Submit & Deactive'; //change submit button text 
                    if (this.id === 'other') {
                        targetDiv.classList.remove('d-none');
                        if(other_feedback.value == ''){
                            rio_fb_submit.classList.add('disabled');
                        }
                    } else {
                        targetDiv.classList.add('d-none');
                        rio_fb_submit.classList.remove('disabled');
                    }
                }
            });
        });
        // check other feedback field is empty or not
        other_feedback.addEventListener('keyup', function() {
          const inputValue = other_feedback.value;
            if(inputValue == ''){
                rio_fb_submit.classList.add('disabled');  // empty
            }else{
                rio_fb_submit.classList.remove('disabled');
            }
        });
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        .rio-feedback-overlay {
          position: fixed;
          top: 0;
          bottom: 0;
          left: 0;
          right: 0;
          background: rgba(0, 0, 0, 0.7);
          transition: opacity 500ms;
          visibility: visible;
          opacity: 1;
        }

        .rio-feedback-popup {
          font-family: 'Poppins', sans-serif;
          margin: 70px auto;
          background: #fff;
          border-radius: 5px;
          width: 600px;
          position: relative;
          transition: all 0.5s ease-in-out;
        }

        .rio-feedback-popup .rio-feedback-content {
          overflow: auto;
        }

        @media screen and (max-width: 700px){
          .rio-feedback-popup{
            width: 96%;
          }
        }

        .rio-feedback-header{
            background: #5d5dff;
            color: #fff;
            padding: 2px 18px;
            font-size: 16px;
            font-weight: 600;
        }
        .rio-feedback-footer{
          padding: 18px;
          display: flex;
          justify-content: space-between;
        }
        .rio-feedback-footer button{
          margin: 0 7px;
          color: #2271b1;
          border-color: #2271b1;
          background: #f6f7f7;
          padding: 0 10px;
          cursor: pointer;
          border-width: 1px;
          border-style: solid;
          display: inline-block;
          text-decoration: none;
          font-size: 14px;
          line-height: 2.15;
          min-height: 30px;
          border-radius: 4px;
        }
        .rio-feedback-footer button:hover{
          background: #5d5dff;
          color: #fff;
        }
        .rio-feedback-body{
          padding: 18px;
            border-bottom: 1px solid #eee;
        }
        .rio-feedback-body h3{
          margin: 0 0 12px 0;
          color: #1d2327;
          font-size: 18px;
          font-weight: 600;
          line-height: 1.5;
        }
        .rio-feedback-body div{
          margin-bottom: 12px;
        }
        .rio-feedback-popup label{
          color: #3c434a;
          font-size: 14px;
        }
        .d-none{
          display: none;
        }
        .hidden{
          visibility: hidden;
        }
        .other_feedback{
          margin-left: 24px;
        }
        .other_feedback input{
          padding: 8px;
          display: block;
          width: 90%;
          margin-top: 4px;
          border-radius: 4px;
            border: 1px solid #c3c3c3;
        }
        .other_feedback input:focus{
          outline: none;
        }
        .disabled{
          border-color: #eee !important;
          color: #c9c7c7 !important;
          pointer-events: none;
        }
    </style>
    
    <?php
}
add_action('admin_footer', 'rio_feedback_modal');

// mail send php code 

// add_action("wp_ajax_send_feedback_email", "send_feedback_email_callback");
// add_action("wp_ajax_nopriv_send_feedback_email", "send_feedback_email_callback");
// function send_feedback_email_callback() {
//   if (isset($_POST["feedback"])) {
//     $feedback = $_POST["feedback"];

//     $headers = array(
//       "Content-Type: text/plain; charset=UTF-8",
//       "From: Your Website <noreply@example.com>", 
//     );

//     $email_sent = wp_mail("omar@echoasoft.com", "Feedback from Plugin Deactivation", $feedback, $headers);

//     if ($email_sent) {
//       echo "success";
//     } else {
//       echo "error";
//     }
//   }
//   wp_die();
// }