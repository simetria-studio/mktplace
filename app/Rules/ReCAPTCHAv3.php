<?php

namespace App\Rules;

use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Validation\Rule;

class ReCAPTCHAv3 implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('recaptcha.v3.private_key'),
                'response' => $value,
                'remoteip' => \Request::ip(),
            ]);
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            return false;
        }
        return $this->getScore($response) >= config('recaptcha.v3.minimum_score');
    }
    private function getScore($response)
    {
        return \GuzzleHttp\json_decode($response->getBody(), true)['score'];
    }
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Failed on reCAPTCHA verification.';
    }
}
