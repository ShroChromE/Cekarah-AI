<?php

namespace Tests\Feature;

use App\Ai\Support\SafetyGuard;
use App\Models\EmergencyContact;
use Database\Seeders\EmergencyContactSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SafetyGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_redacts_nik_like_sequences(): void
    {
        $guard = new SafetyGuard;

        $result = $guard->redactSensitive('NIK saya 3275010101900001 tolong daftarkan');

        $this->assertTrue($result['redacted']);
        $this->assertStringContainsString('[NIK DISENSOR]', $result['text']);
        $this->assertStringNotContainsString('3275010101900001', $result['text']);
    }

    public function test_it_leaves_normal_text_untouched(): void
    {
        $guard = new SafetyGuard;

        $result = $guard->redactSensitive('Posko pengungsian di Aceh Tamiang di mana?');

        $this->assertFalse($result['redacted']);
        $this->assertSame('Posko pengungsian di Aceh Tamiang di mana?', $result['text']);
    }

    public function test_it_detects_life_threatening_messages(): void
    {
        $guard = new SafetyGuard;

        $this->assertTrue($guard->isLifeThreatening('Tolong kami terjebak banjir di atap rumah!'));
        $this->assertFalse($guard->isLifeThreatening('Bagaimana cara cek bansos PKH?'));
    }

    public function test_escalation_contacts_come_from_the_database(): void
    {
        $this->seed(EmergencyContactSeeder::class);

        $contacts = (new SafetyGuard)->escalationContacts();

        $this->assertNotEmpty($contacts);
        $this->assertContains('BNPB — Pusat Pengendalian Operasi', array_column($contacts, 'name'));
        $this->assertSame(EmergencyContact::count(), 8);
    }
}
