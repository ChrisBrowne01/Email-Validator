<?php
/**
 * Project: Email Validator
 * 
 * Description: An email validator tool that checks mulitple email addresses entered by users are valid or invalid.
 */

/**
 * Validates an email address using a regular expression.
 * @param string $email The email address to validate.
 * @return bool True if the email address is valid, false otherwise.
 */
function validateEmail($email) {
  // Regular expression for baisc email validation
  $regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,63}$/i";
  
  if (preg_match($regex, $email)) {
    return true;
  } else {
    return false;
  }
}
?>