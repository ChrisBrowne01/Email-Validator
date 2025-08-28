<?php
/**
 * Project: Email Validator
 * 
 * Description: An email validator tool that checks mulitple email addresses entered by users are valid and 
 * highlights invalid email addresses with explainations for why they are invalid. Provides a "strict mode" 
 * option that enforces additional rules (e.g., no consecutive dots in the username).
 */

/**
 * Validates an email address using a regular expression.
 * @param string $email The email address to validate.
 * @param bool $strictMode Whether to apply strict validation rules.
*/
function validateEmail($email, $strictMode = false) {
  // Trim whitespace to ensure clean validation
  $email = trim($email);
  
  // Check for the @ symbol
  if (strpos($email, '@') === false) {
    return 'Missing the "@" symbol.';
  }

  // Split the email into username and domain parts
  list($username, $domain) = explode('@', $email, 2);

  // Validate the username part
  // Using a more specific regex for the username
  if (!preg_match('/^[a-zA-Z0-9._-]+$/', $username)) {
    return 'Invalid username format. It can only contain alphanumeric characters, dots, underscores, and hyphens.';
  }

  // Strict mode check for consecutive dots in the username
  if ($strictMode && strpos($username, '..') !== false) {
    return 'Strict Mode: Consecutive dots are not allowed in the username.';
  }

  // Validate the domain name
  // The domain part can contain subdomains, separated by dots.
  if (!preg_match('/^[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/', $domain)) {
    return 'Invalid domain name. It can only contain alphanumeric characters and hyphens.';
  }
  
  // Validate the top-level domain (TLD)
  $tld_pattern = '/\.[a-zA-Z]{2,63}$/';
  if (!preg_match($tld_pattern, $domain)) {
    return 'Invalid top-level domain (TLD). It must be 2-63 characters long and contain only letters.';
  }

  // Return true if all check pass
  return true;
}

// Declar array to store valid and invalid emails
$validEmails = [];
$invalidEmails = [];
$strictMode = false;

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $emailList = $_POST['emails'] ?? '';
  $strictMode = isset($_POST['strictMode']);
  
  // Split the email list into an array of emails
  $emails = explode("\n", $emailList);
  foreach ($emails as $email) {
    // Remove any leading or trailing whitespace
    $email = trim($email);
    // Check if the email is empty
    if (!empty($email)) {
      $validationResult = validateEmail($email, $strictMode);
      // Sort emails into vaild and invalid arrays
      if ($validationResult === true) {
        $validEmails[] = $email;
      } else {
        $invalidEmails[] = ['email' => $email, 'reason' => $validationResult];
      }
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
      <form action="email_validator.php" method="post" class="space-y-4">
        
        <!-- A textarea for the user to enter a sample text -->
        <div>
          <label for="emails" class="block text-sm font-medium text-gray-700">Enter email addresses (one per line):</label>
          <textarea 
            name="emails" 
            id="emails" 
            rows="10" 
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" 
            placeholder="Enter email addresses..."
          ><?php echo htmlspecialchars($_POST['emails'] ?? ''); ?></textarea>
        </div>

        <!-- A checkbox for the user to enable or disable the strict mode -->
        <div>
          <div class="flex flex-wrap space-x-4 mt-1">
            <label class="inline-flex items-center">
              <input type="checkbox" name="strictMode" <?php echo $strictMode ? 'checked' : ''; ?> class="rounded text-indigo-600 focus:ring-indigo-500">
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
  
      <?php if ($_SERVER["REQUEST_METHOD"] == "POST") : ?>
        <?php if (isset($_POST['emails'])) : ?>
          <?php if (empty($emailList)) : ?>
            <!-- Displays a message to the user if no emails were entered.  -->
            <div class='mt-6 p-4 bg-red-100 text-red-700 border border-red-200 rounded-md'>Please enter at least one email address to validate.</div>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($validEmails) || !empty($invalidEmails)) : ?>
          <div class="mt-6 p-6 bg-gray-50 rounded-lg shadow-inner">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Results:</h2>

            <!-- Check vaild emails exist before looping through. -->
            <?php if (!empty($validEmails)) : ?>
              <h3 class="text-lg font-semibold mt-4 text-gray-800">Vaild Emails:</h3>
              <ul class="list-disc list-inside space-y-1 mt-2 text-gray-700">
                <!-- Display valid emails. -->
                <?php foreach ($validEmails as $email) : ?>
                  <li class="mt-6 p-4 bg-green-100 text-green-700 border border-green-200 rounded-md"><?php echo htmlspecialchars($email); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
                      
            <!-- Check invalid emails exist before looping through. -->
            <?php if (!empty($invalidEmails)) : ?>
              <h3 class="text-lg font-semibold mt-4 text-gray-800">Invalid Emails:</h3>
              <ul class="list-disc list-inside space-y-1 mt-2 text-gray-700">
                <!-- Display invalid emails and their reasons for being invalid. -->
                <?php foreach ($invalidEmails as $item) : ?>
                  <li class="mt-6 p-4 bg-red-100 text-red-700 border border-red-200 rounded-md"><?php echo htmlspecialchars($item['email']); ?>
                  <div class="mt-2"><strong>Reason:</strong><br><?php echo htmlspecialchars($item['reason']); ?></div>
                </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>

          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </body>
</html>

<!-- example emails:
adam@live.co.uk
berty..browne@live.co.uk
Christina Clarke@live.co.uk
Dee23.Doe@live.co.uk
Erin,Elise@live.co.uk
Fred,Fruit@live.co.uk
gary,gone@live.co,uk
henry,hat@live.couk
iren,ice@live.co\.uk
jack,jump@live.co.uk2
kallum,kick@live.co.uk0ddd
info@webdesignandit.co.uk
services@webdesignandit.co.ukservices@webdesignandit.co.uk
help@webdesignandit.co.uk;help@webdesignandit.co.uk
contact@webdesignandit.co.uk contact@webdesignandit.co.uk

support@web.design.andit.co.uk 
sales.webdesignandit.co.uk
leah.leap.live.co.uk
mike.miles@.org.uk
norman.nob
@live.co.uk
.co.uk
sally @ live co uk
sam silly@live.co.uk
 

john.doe@example.com
-->