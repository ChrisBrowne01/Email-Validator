<?php
/**
 * Project: Email Validator
 * 
 * Description: An email validator tool that checks mulitple email addresses entered by users are valid or invalid.
 *  and highlights invalid email addresses with explainations for why they are invalid. 
 * Provides a "strict mode" option that enforces additional rules (e.g., no consecutive dots in the username).
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

// Declar array to store valid and invalid emails
$validEmails = [];
$invalidEmails = [];

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $emailList = $_POST['email'] ?? '';
  
  // Split the email list into an array of emails
  $emails = explode('\n', $emailList);
  foreach ($emails as $email) {
    // Remove any leading or trailing whitespace
    $email = trim($email);
    // Sort emails into vaild and invalid arrays
    if (validateEmail($email)) {
      $validEmails[] = $email;
    } else {
      $invalidEmails[] = $email;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Email Validator</title>
      <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="bg-gray-100 p-8">
    <div class="container mx-auto p-6 bg-white rounded-lg shadow-md">
      <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Email Validator</h1>

      <!-- A form that allows users to enter multiple email addresses (one per line) -->
      <form action="regex_matcher.php" method="post" class="space-y-4">
        
        <!-- A textarea for the user to enter a sample text -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Enter email addresses (one per line):</label>
          <textarea 
            name="email" 
            id="email" 
            rows="8" 
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" 
            placeholder="Enter email addresses (one per line)"
          ><?php echo htmlspecialchars($_POST['email'] ?? ''); ?></textarea>
        </div>

        <!-- A checkbox for the user to enable or disable the strict mode -->
        <div>
          <div class="flex flex-wrap space-x-4 mt-1">
            <label class="inline-flex items-center">
              <input type="checkbox" name="strictMode" <?php echo in_array('i', $_POST['modifiers'] ?? []) ? 'checked' : ''; ?> class="rounded text-indigo-600 focus:ring-indigo-500">
              <span class="ml-2 text-sm text-gray-700"> Strict Mode</span>
            </label>
          </div>
        </div>

        <!-- Button to submit to validate emails -->
        <div>
          <button type="submit" class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Validate Emails
          </button>
        </div>
              
      </form>

    </div>
  </body>
</html>