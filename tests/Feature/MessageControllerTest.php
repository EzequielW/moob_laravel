<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Interfaces\MessageStrategy;
use App\Factories\MessageStrategyFactory;
use Illuminate\Bus\PendingBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $fakeBatch = Mockery::mock(PendingBatch::class);
        $fakeBatch->shouldReceive('finally')->andReturnSelf();
        $fakeBatch->shouldReceive('dispatch');

        $strategyMock = Mockery::mock(MessageStrategy::class);
        $strategyMock->shouldReceive('sendMassMessage')->andReturn($fakeBatch);

        $this->mock(MessageStrategyFactory::class, function ($mock) use ($strategyMock) {
            $mock->shouldReceive('make')->andReturn($strategyMock);
        });
    }

    protected function authenticatedUser(): User
    {
        return User::factory()->create();
    }

    public function test_valid_telegram_request_passes_validation(): void
    {
        $user = $this->authenticatedUser();

        $response = $this->actingAs($user)->post(route('messages.store'), [
            'platform'   => 'telegram',
            'recipients' => ['@zequielW'],
            'content'    => 'Hello world!'
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_invalid_telegram_username_fails_validation()
    {
        $user = $this->authenticatedUser();

        $response = $this->actingAs($user)->post(route('messages.store'), [
            'platform'   => 'telegram',
            'recipients' => ['@a'],
            'content'    => 'Hi!',
        ]);

        $response->assertSessionHasErrors(['recipients']);
    }

    public function test_valid_whatsapp_number_passes_validation()
    {
        $user = $this->authenticatedUser();

        $response = $this->actingAs($user)->post(route('messages.store'), [
            'platform'   => 'whatsapp',
            'recipients' => ['+5491123456789'],
            'content'    => 'Hi from WhatsApp!',
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_invalid_discord_tag_fails_validation()
    {
        $user = $this->authenticatedUser();

        $response = $this->actingAs($user)->post(route('messages.store'), [
            'platform'   => 'discord',
            'recipients' => ['WrongFormat'],
            'content'    => 'Message',
        ]);

        $response->assertSessionHasErrors(['recipients']);
    }

    public function test_valid_slack_email_passes_validation()
    {
        $user = $this->authenticatedUser();

        $response = $this->actingAs($user)->post(route('messages.store'), [
            'platform'   => 'slack',
            'recipients' => ['user@example.com'],
            'content'    => 'Hello Slack!',
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_invalid_slack_email_fails_validation()
    {
        $user = $this->authenticatedUser();

        $response = $this->actingAs($user)->post(route('messages.store'), [
            'platform'   => 'slack',
            'recipients' => ['not-an-email'],
            'content'    => 'Hello!',
        ]);

        $response->assertSessionHasErrors(['recipients']);
    }

    public function test_store_returns_json_response_when_requested() {
        $this->authenticatedUser();

        $this->postJson(route('api.messages.store'), [
                'platform' => 'telegram',
                'recipients' => ['@validUser'],
                'content' => 'Rest api test',
            ])
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Messages are being processed.',
            ]);
    }

    public function test_file_upload_fails_if_file_too_large()
    {
        $user = $this->authenticatedUser();
        Config::set('uploads.max_size_mb', 10);
        $file = UploadedFile::fake()->create('too_big.pdf', 11264);

        $response = $this->actingAs($user)->post(route('messages.store'), [
            'platform' => 'telegram',
            'recipients' => ['@validUser'],
            'content' => 'Test with big file',
            'attachment' => $file,
        ]);

        $response->assertSessionHasErrors(['attachment']);
    }

    public function test_file_upload_succeeds_within_allowed_size()
    {
        $user = $this->authenticatedUser();
        Config::set('uploads.max_size_mb', 10);
        $file = UploadedFile::fake()->create('too_big.pdf', 5120);

        $response = $this->actingAs($user)->post(route('messages.store'), [
            'platform' => 'telegram',
            'recipients' => ['@validUser'],
            'content' => 'Test with big file',
            'attachment' => $file,
        ]);

        $response->assertRedirect();
        $this->assertTrue(Storage::disk('public')->exists('attachments/' . $file->hashName()));
    }
}
