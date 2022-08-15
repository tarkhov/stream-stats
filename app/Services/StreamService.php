<?php
namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use App\Models\Stream;

class StreamService
{
    const PER_PAGE = 100;

    public static function seed(): void
    {
        $clientId = config('services.twitch.client_id');
        $clientSecret = config('services.twitch.client_secret');
        $oauth = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded'
        ])
            ->post("https://id.twitch.tv/oauth2/token?client_id=$clientId&client_secret=$clientSecret&grant_type=client_credentials")
            ->throw()
            ->json();

        $max = 1000 - self::PER_PAGE;
        $count = 0;
        $page = null;
        while ($count <= $max) {
            $data = self::getPage($clientId, $oauth['access_token'], $page);
            $count += self::PER_PAGE;
            $page = $data['pagination']['cursor'];
            $data = Arr::shuffle(Arr::map($data['data'], function ($stream) {
                unset($stream['id'], $stream['started_at']);

                if (is_array($stream['tag_ids'])) {
                    $stream['tag_ids'] = implode(', ', $stream['tag_ids']);
                }

                return $stream;
            }));
            Stream::insert($data);
        }
    }

    protected static function getPage(string $clientId, string $token, ?string $page): array
    {
        $params = [
            'first' => self::PER_PAGE
        ];
        if ($page !== null) {
            $params['after'] = $page;
        }

        return Http::withToken($token)
            ->withHeaders([
                'Client-Id' => $clientId
            ])
            ->get('https://api.twitch.tv/helix/streams', $params)
            ->throw()
            ->json();
    }
}
