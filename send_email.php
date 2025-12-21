<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Empfänger-E-Mail - HIER DEINE E-MAIL EINTRAGEN!
    $to = "be@manul.at";
    
    // Daten aus dem Formular
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $anfrage = htmlspecialchars(trim($_POST['anfrage'] ?? ''));
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));
    
    // Validierung
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name ist erforderlich.";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Gültige E-Mail-Adresse ist erforderlich.";
    }
    
    if (empty($anfrage)) {
        $errors[] = "Anfrageart ist erforderlich.";
    }
    
    if (empty($message)) {
        $errors[] = "Nachricht ist erforderlich.";
    }
    
    // Wenn Fehler vorhanden
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => implode(" ", $errors)
        ]);
        exit;
    }
    
    // Betreff
    $subject = "Neue Kontaktanfrage von $name - $anfrage";
    
    // E-Mail-Inhalt
    $email_content = "Neue Kontaktanfrage von Ihrer Website:\n\n";
    $email_content .= "Name: $name\n";
    $email_content .= "E-Mail: $email\n";
    $email_content .= "Anfrageart: $anfrage\n\n";
    $email_content .= "Nachricht:\n$message\n";
    
    // E-Mail-Header
    $headers = "From: $name <$email>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=utf-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // E-Mail senden
    if (mail($to, $subject, $email_content, $headers)) {
        // Bestätigungs-E-Mail an Absender (optional)
        $confirmation_subject = "Bestätigung Ihrer Anfrage - Lukas Manul";
        $confirmation_message = "Hallo $name,\n\n";
        $confirmation_message .= "vielen Dank für Ihre Anfrage! Ich habe Ihre Nachricht erhalten und werde mich innerhalb von 24 Stunden bei Ihnen melden.\n\n";
        $confirmation_message .= "Mit freundlichen Grüßen,\nLukas Manul\n\n";
        $confirmation_message .= "Manul Kulinarik\nKaiser-Josef-Platz 1\n8010 Graz\nbe@manul.at\n+43 677 624 099 10";
        
        $confirmation_headers = "From: Lukas Manul <be@manul.at>\r\n";
        $confirmation_headers .= "Reply-To: be@manul.at\r\n";
        $confirmation_headers .= "Content-Type: text/plain; charset=utf-8\r\n";
        
        mail($email, $confirmation_subject, $confirmation_message, $confirmation_headers);
        
        echo json_encode([
            'success' => true,
            'message' => 'Ihre Nachricht wurde erfolgreich gesendet. Sie erhalten in Kürze eine Bestätigungs-E-Mail.'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Es gab ein Problem beim Senden Ihrer Nachricht. Bitte versuchen Sie es später erneut.'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Ungültige Anfragemethode.'
    ]);
}
?>