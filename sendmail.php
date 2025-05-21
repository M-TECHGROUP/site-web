<?php
// filepath: c:\xampp\htdocs\ssss\sendmail.php

// Configuration
$to = "makadofabien90@gmail.com"; // Remplacez par votre adresse email
$subject = "Nouveau message depuis le site M-Tech Group";

// Fonction pour nettoyer les entrées
function clean($value) {
    return htmlspecialchars(trim($value));
}

// Récupération des champs
$name    = clean($_POST["name"] ?? '');
$email   = clean($_POST["email"] ?? '');
$phone   = clean($_POST["phone"] ?? '');
$service = clean($_POST["service"] ?? '');
$message = clean($_POST["message"] ?? '');

// Construction du corps du mail
$body = "Nom: $name\n";
$body .= "Email: $email\n";
$body .= "Téléphone: $phone\n";
$body .= "Service: $service\n";
$body .= "Message:\n$message\n";

// Gestion du fichier joint
$file_attached = isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK;

if ($file_attached) {
    $file_tmp_name = $_FILES['file']['tmp_name'];
    $file_name     = basename($_FILES['file']['name']);
    $file_size     = $_FILES['file']['size'];
    $file_type     = $_FILES['file']['type'];
    $file_content  = chunk_split(base64_encode(file_get_contents($file_tmp_name)));

    $boundary = md5(uniqid(time()));

    // Headers
    $headers  = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

    // Message
    $message_body  = "--$boundary\r\n";
    $message_body .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
    $message_body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $message_body .= $body . "\r\n";

    // Pièce jointe
    $message_body .= "--$boundary\r\n";
    $message_body .= "Content-Type: $file_type; name=\"$file_name\"\r\n";
    $message_body .= "Content-Transfer-Encoding: base64\r\n";
    $message_body .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n\r\n";
    $message_body .= $file_content . "\r\n";
    $message_body .= "--$boundary--";

    $sent = mail($to, $subject, $message_body, $headers);
} else {
    // Sans pièce jointe
    $headers  = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $sent = mail($to, $subject, $body, $headers);
}

// Redirection ou message d'erreur
if ($sent) {
    header("Location: merci.html");
    exit;
} else {
    echo "Erreur lors de l'envoi du message. Veuillez réessayer.";
}
?>