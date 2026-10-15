{{-- Modèl e-mail kliyan (style andedan, pou Gmail/Outlook). --}}
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>{{ $title }}</title></head>
<body style="margin:0;padding:0;background:#EEF1F5;font-family:Arial,Helvetica,sans-serif;color:#14213D">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#EEF1F5;padding:24px 12px">
    <tr><td align="center">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border:1px solid #D8DEE6;border-radius:12px">
            <tr><td style="padding:20px 28px;border-bottom:1px solid #E7EBF0">
                <span style="display:inline-block;background:#1F3FA0;color:#fff;font-weight:bold;font-size:13px;padding:5px 8px;border-radius:5px;letter-spacing:1px">AG</span>
                <span style="font-weight:bold;font-size:17px;margin-left:8px;vertical-align:middle">{{ $agency['name'] }}</span>
            </td></tr>
            <tr><td style="padding:28px">
                <p style="margin:0 0 6px;color:#5F6D84;font-size:14px">Bonjour {{ $name }},</p>
                <h1 style="margin:0 0 18px;font-size:22px;line-height:1.25">{{ $title }}</h1>

                @if (!empty($code))
                    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 18px">
                        <tr><td style="border:2px dashed #1F3FA0;background:#E5EBF9;border-radius:8px;padding:12px 18px">
                            <div style="font-size:12px;color:#5F6D84">Code de retrait</div>
                            <div style="font-size:28px;font-weight:bold;letter-spacing:4px;color:#1F3FA0;font-family:'Arial Narrow',Arial,sans-serif">{{ $code }}</div>
                        </td></tr>
                    </table>
                @endif

                @if (!empty($plate))
                    <p style="margin:0 0 14px"><span style="display:inline-block;border:1.5px solid #14213D;border-radius:5px;padding:3px 9px;font-weight:bold;letter-spacing:1px;color:#1F3FA0;font-family:'Arial Narrow',Arial,sans-serif">{{ $plate }}</span></p>
                @endif

                @foreach ($lines as $line)
                    <p style="margin:0 0 12px;font-size:15px;line-height:1.55;color:#3A4863">{{ $line }}</p>
                @endforeach

                @if (!empty($action))
                    <p style="margin:22px 0 0"><a href="{{ $action[1] }}" style="display:inline-block;background:#1F3FA0;color:#ffffff;text-decoration:none;font-weight:bold;padding:12px 20px;border-radius:8px">{{ $action[0] }}</a></p>
                @endif
            </td></tr>
            <tr><td style="padding:16px 28px;border-top:1px solid #E7EBF0;font-size:12px;color:#5F6D84;line-height:1.5">
                {{ $agency['name'] }}, {{ $agency['address'] }}<br>
                {{ $agency['phone'] }}, {{ $agency['email'] }}<br>
                {{ $agency['hours'] }}
            </td></tr>
        </table>
    </td></tr>
</table>
</body>
</html>
