<?php

require 'recaptcha.php';
use Phelium\Component\reCAPTCHA;

require 'mail/class.smtp.php';
require 'mail/class.phpmailer.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$reCAPTCHA = new reCAPTCHA('PUBLIC_KEY', 'SECRET_KEY');

$errors = array();
$data = array();


/* NAME */
if (empty($_POST["name"])) {
    $errors['name'] = "Name is required";
}


/* EMAIL */
if (empty($_POST["email"])) {
   $errors['email'] = "Email is required";
} else if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "Invalid email format";
}


/* MSG SUBJECT */
if (empty($_POST["msg_subject"])) {
    $errors['msg_subject'] = "Subject is required";
}


/* MESSAGE */
if (empty($_POST["msg_text"])) {
    $errors['msg_text'] = "Message is required";
}

if ( !$reCAPTCHA->isValid($_POST['grecaptcha']))
    {
        $errors['grecaptcha'] = 'Please try again. We are unable to send your form.';
    }

	$msg = "Name: ".$_POST["name"].", Email: ".$_POST["email"].", Subject: ".$_POST["msg_subject"].", Message:".$_POST["msg_text"];

if(!empty($errors)) {
        
        $data['success'] = false;
        $data['errors'] = $errors;
        $data['message']  = 'There was an error with your form. Please Review.';
} else {

			$mail = new PHPMailer;
            
			$mail->IsSMTP();
            $mail->SMTPDebug = 0;
			$mail->Host = "SERVER";
			$mail->SMTPAuth = true;
			$mail->Username = "SMTP_USERNAME";
			$mail->Password = "SMTP_PASSWORD";
            $mail->SMTPSecure = 'DEFINE EITHER TLS OR SSL';
            $mail->Port = "DEFINE PORT NUMBER AS A NUMBER NOT STRING";
			$mail->From = "FROM_EMAIL"; //<-- do not fake
			$mail->FromName = 'FROM NAME';
			$mail->AddAddress('ADDRESS_TO_SEND_TO');
			$mail->AddReplyTo($_POST["email"]); //optional
			$mail->IsHTML(false); //disable for plain text or true if you want to render html
			$mail->Subject = $_POST['msg_subject'];
			$mail->Body = $msg;
            
    
    if(!$mail->Send()) {
        $data['success'] = false;
        $data['message'] = 'There were errors with your form, please refer to the warnings!';

    } else {
        $data['success'] = true;
        $data['message'] = 'Success! Your message was sent. I will contact you shortly.';
    }
}

    echo json_encode($data);

?>