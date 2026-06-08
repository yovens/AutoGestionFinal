<!DOCTYPE html>
<html>
<head>
    <style>
        .container { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .message-box { background: #f9f9f9; border-left: 5px solid #10b981; padding: 15px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h3>Bonjour,</h3>
        <p>L'administrateur a répondu à votre message :</p>
        
        <div class="message-box">
            {{ $replyMessage }}
        </div>

        <p>Cordialement,<br>L'équipe AutoGestion</p>
    </div>
</body>
</html>