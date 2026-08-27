<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use App\Models\SiteSetting;
use App\Services\BookingMailService;
use Illuminate\Database\Seeder;

class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        SiteSetting::current()->update([
            'site_name' => 'Fügefa építésziroda',
            'phone' => '',
            'email' => 'info@example.com',
            'notification_email' => 'info@example.com',
            'address' => '',
            'hero_image' => '',
            'footer_text' => 'Építésziroda – szerkeszd a webhely tartalmát az admin felületen.',
        ]);

        $templates = [
            [
                'key' => BookingMailService::TEMPLATE_GUEST_RECEIVED,
                'name' => 'Foglalás érkezett – vendég',
                'subject' => 'Foglalási igényét megkaptuk – {{site_name}}',
                'description' => 'Webes foglalás után a vendégnek megy.',
                'body' => "Kedves {{guest_name}}!\n\nKöszönjük foglalási igényét a(z) {{accommodation}} szállásra.\n\nÉrkezés: {{check_in}}\nTávozás: {{check_out}}\nÉjszakák: {{nights}}\nVendégek: {{guests_count}} fő\nVárható összeg: {{total_price}} (szállás: {{accommodation_total}}, IFA: {{ifa_total}})\n\nHamarosan visszajelzünk a megerősítésről.\n\nÜdvözlettel,\n{{site_name}}\n{{site_phone}}\n{{site_email}}",
            ],
            [
                'key' => BookingMailService::TEMPLATE_ADMIN_RECEIVED,
                'name' => 'Új foglalás – admin értesítő',
                'subject' => 'Új foglalás: {{accommodation}} ({{check_in}} – {{check_out}})',
                'description' => 'Új foglaláskor az értesítési e-mail címre megy.',
                'body' => "Új foglalás érkezett.\n\nAzonosító: #{{booking_id}}\nSzállás: {{accommodation}}\nVendég: {{guest_name}} ({{guest_email}}, {{guest_phone}})\nÉrkezés: {{check_in}}\nTávozás: {{check_out}}\nÉjszakák: {{nights}}\nVendégek: {{guests_count}}\nÖsszeg: {{total_price}}\nStátusz: {{status}}\nMegjegyzés: {{notes}}",
            ],
            [
                'key' => BookingMailService::TEMPLATE_GUEST_CONFIRMED,
                'name' => 'Foglalás megerősítve – vendég',
                'subject' => 'Foglalása megerősítve – {{site_name}}',
                'description' => 'Amikor a foglalás státusza megerősítettre vált.',
                'body' => "Kedves {{guest_name}}!\n\nFoglalását megerősítettük.\n\nSzállás: {{accommodation}}\nÉrkezés: {{check_in}}\nTávozás: {{check_out}}\nVendégek: {{guests_count}} fő\nFizetendő: {{total_price}}\n\nVárjuk szeretettel!\n{{site_name}}\n{{site_address}}\n{{site_phone}}",
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::query()->updateOrCreate(
                ['key' => $template['key']],
                [
                    ...$template,
                    'is_active' => true,
                ],
            );
        }
    }
}
