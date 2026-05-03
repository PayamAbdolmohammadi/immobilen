<?php

namespace Tests\Feature\Console;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RentsGenerateMonthlyCommandTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function command_runs_with_valid_period(): void
    {
        $this->artisan('rents:generate-monthly', ['--period' => '2026-06'])
            ->assertSuccessful()
            ->expectsOutputToContain('2026-06');
    }

    #[Test]
    public function command_fails_on_invalid_period(): void
    {
        $this->artisan('rents:generate-monthly', ['--period' => 'not-a-month'])
            ->assertFailed();
    }
}
