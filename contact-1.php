<?php
/*
THIS FILE USES PHPMAILER INSTEAD OF THE PHP MAIL() FUNCTION
*/

require 'PHPMailer-master/PHPMailerAutoload.php';

/*
*  CONFIGURE EVERYTHING HERE
*/

// an email address that will be in the From field of the email.
$fromEmail = 'info@ir-bison-bau.de';


// an email address that will receive the email with the output of the form
$sendToEmail = 'info@ir-bison-bau.de';


// subject of the email
$subject = 'Neue Nachricht auf der IR Bison Bau Website';

// form field names and their translations.
// array variable name => Text to appear in the email
$fields = array('name1' => 'Name', 'surname1' => 'Nachname', 'phone1' => 'Tel.Nr.', 'email1' => 'Email', 'message1' => 'Nachricht');

// message that will be displayed when everything is OK :)
$okMessage = 'Ihre Nachricht wurde erfolgreich übertragen. Vielen Dank! Wir melden uns bei Ihnen.';

// If something goes wrong, we will display this message.
$errorMessage = 'Es gab einen Fehler bei der Übertragung Ihrer Nachricht. Bitte prüfen Sie Ihre Eingabe und versuchen Sie es nochmal.';

/*
*  LET'S DO THE SENDING
*/

// if you are not debugging and don't need error reporting, turn this off by error_reporting(0);
error_reporting(E_ALL & ~E_NOTICE);

try
{
    
    if(count($_POST) == 0) throw new \Exception('Form is empty');
    
    $emailTextHtml = "<h1>Neue Nachricht per Kontaktformular:</h1><hr>";
    $emailTextHtml .= "<table>";
    
    foreach ($_POST as $key => $value) {
        // If the field exists in the $fields array, include it in the email
        if (isset($fields[$key])) {
            $emailTextHtml .= "<tr><th align='left'>$fields[$key]</th><td>$value</td></tr>";
        }
    }
    $emailTextHtml .= "</table><hr>";
    $emailTextHtml .= "<p>Diese Mail wurde automatisch erstellt, bitte nicht darauf antworten!</p>";
    
    $mail = new PHPMailer;
    $mail->CharSet = "UTF-8";
    $mail->setFrom($fromEmail);
    $mail->addAddress($sendToEmail); // you can add more addresses by simply adding another line with $mail->addAddress();
    $mail->addReplyTo($_POST['email1']);
    
    $mail->isHTML(true);
    
    $mail->Subject = $subject;
    $mail->msgHTML($emailTextHtml); // this will also create a plain-text version of the HTML email, very handy
    
    
    if(!$mail->send()) {
        throw new \Exception('I could not send the email.' . $mail->ErrorInfo);
    }
    
    $responseArray = array('type' => 'success', 'message' => $okMessage);
}
catch (\Exception $e)
{
    // $responseArray = array('type' => 'danger', 'message' => $errorMessage);
    $responseArray = array('type' => 'danger', 'message' => $e->getMessage());
}


// if requested by AJAX request return JSON response
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $encoded = json_encode($responseArray);
    
    header('Content-Type: application/json');
    
    echo $encoded;
}
// else just display the message
else {
    echo $responseArray['message'];
}