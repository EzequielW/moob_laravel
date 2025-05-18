<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'platform'   => ['required', 'in:telegram,whatsapp,discord,slack'],
            'recipients' => ['required', 'array', 'min:1'],
            'recipients.*' => ['required', 'string', 'max:255'],
            'content'    => ['required', 'string', 'max:1000'],
            'attachment' => ['nullable', 'file', 'max:' . (config('uploads.max_size_mb') * 1024)],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $platform = $this->input('platform');
            $recipients = $this->input('recipients', []);

            foreach ($recipients as $recipient) {
                if (!$this->isValidRecipient($recipient, $platform)) {
                    $validator->errors()->add('recipients', "Invalid recipient '$recipient' for platform '$platform'.");
                }
            }
        });
    }

    protected function isValidRecipient(string $recipient, string $platform): bool
    {
        return match ($platform) {
            'telegram' => preg_match('/^@?[a-zA-Z0-9_]{5,}$/', $recipient), // Telegram username
            'whatsapp' => preg_match('/^\+\d{6,15}$/', $recipient),       // International phone number
            'discord'  => preg_match('/^.{2,32}#[0-9]{4}$/', $recipient), // DiscordTag format
            'slack'    => filter_var($recipient, FILTER_VALIDATE_EMAIL), // Slack email
            default    => false,
        };
    }

    public function authorize(): bool
    {
        return true;
    }
}