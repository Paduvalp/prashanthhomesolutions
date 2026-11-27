<?php
// Copy this file to "mail-config.php" and fill in your details.
// mail-config.php holds your password: never commit it to GitHub.
return [
    'host'       => 'smtp.gmail.com',
    'port'       => 587,
    'encryption' => 'tls',                      // use 'ssl' with port 465

    'username'   => 'your-address@gmail.com',   // mailbox that sends the mail
    'password'   => 'your-app-password',        // Gmail: 16-character App Password

    'from_email' => 'your-address@gmail.com',   // normally the same as username
    'from_name'  => 'Prashanth Home Solutions Website',

    'to_email'   => 'prashanthhomesolutions02@gmail.com', // where enquiries arrive
];
