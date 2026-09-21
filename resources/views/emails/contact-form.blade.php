<div style="font-family: Georgia, 'Times New Roman', serif; font-size: 16px; line-height: 1.65; color: #1c241f; max-width: 640px;">
    <p style="margin: 0 0 1rem;">Új üzenet érkezett a <strong>{{ $siteName }}</strong> kapcsolati űrlapjáról.</p>

    <table style="width: 100%; border-collapse: collapse; margin: 0 0 1.25rem;">
        <tr>
            <td style="padding: 0.4rem 0; color: #5a655e; width: 7rem;">Név</td>
            <td style="padding: 0.4rem 0; font-weight: 600;">{{ $name }}</td>
        </tr>
        <tr>
            <td style="padding: 0.4rem 0; color: #5a655e;">E-mail</td>
            <td style="padding: 0.4rem 0;"><a href="mailto:{{ $email }}">{{ $email }}</a></td>
        </tr>
        @if (filled($phone))
            <tr>
                <td style="padding: 0.4rem 0; color: #5a655e;">Telefon</td>
                <td style="padding: 0.4rem 0;">{{ $phone }}</td>
            </tr>
        @endif
    </table>

    <p style="margin: 0 0 0.35rem; color: #5a655e; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.06em;">Üzenet</p>
    <div style="padding: 1rem; background: #f4f5f2; border: 1px solid #e2e5df; white-space: pre-wrap;">{{ $body }}</div>
</div>
