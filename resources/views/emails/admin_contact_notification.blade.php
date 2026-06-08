<!DOCTYPE html>
<html>
<head>
    <title>Nouveau Message de Contact Client</title>
</head>
<body>
    <h1 style="color: #FF6B6B;">Nouveau message de contact client reçu</h1>

    <p style="font-size: 16px;">
        Un client a envoyé un message via le formulaire de contact du Dashboard.
    </p>

    <hr style="border-top: 1px solid #eee;">

    <p><strong>Objet :</strong> {{ $subject }}</p>
    <p><strong>De :</strong> {{ $clientName }} ({{ $clientEmail }})</p>
    
    <div style="border: 1px solid #4ECDC4; padding: 15px; margin-top: 20px; border-radius: 8px; background-color: #f8f8f8;">
        <p style="margin: 0; font-weight: bold; color: #333;">Message du client :</p>
        <p style="white-space: pre-wrap; margin-top: 10px;">{{ $clientMessage }}</p>
    </div>

    <p style="margin-top: 30px; font-size: 14px; color: #888;">
        Pour répondre, utilisez la fonction "Répondre" de votre client mail.
    </p>
</body>
</html>