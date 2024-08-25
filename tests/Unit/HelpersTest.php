<?php

namespace Tests\Unit;

use App\Models\Patient;
use Tests\TestCase;


class HelpersTest extends TestCase
{
    protected $tenancy = true;

    public function test_template_replace_tags()
    {
        $patient = Patient::factory()->create([
            'first_name' => 'Patient',
            'last_name' => 'Last'
        ]);
        $text = 'Dear {{patientFirstName}}';
        $text = template_replace_tags([
            'body' => $text,
            'patient_id' => $patient->id
        ]);
        $this->assertStringContainsString($patient->first_name, $text);
    }
}
