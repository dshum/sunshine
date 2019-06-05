<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\City;
use App\Forecast;

class UpdateWeather extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates city weather.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $cities = City::get();

        $context = stream_context_create([
            'http' => ['ignore_errors' => true],
        ]);

        foreach ($cities as $city) {
            $this->info($city->name);

            $today = Carbon::today();

            $forecast = Forecast::where('city_id', $city->id)->
                where('date', $today->format('Y-m-d'))->
                first();

            if ($forecast) continue;

            try {
                $params = [
                    'key' => config('services.apixu.key'),
                    'q' => $city->name,
                ];

                $data = file_get_contents(
                    'http://api.apixu.com/v1/current.json'.'?'.urldecode(http_build_query($params)),
                    false,
                    $context
                );
        
                $result = json_decode($data, true);

                if (isset($result['error'])) {
                    $this->error($result['error']['message']);
                    continue;
                }

                $forecast = new Forecast;

                $forecast->city_id = $city->id;
                $forecast->condition = $result['current']['condition']['text'];
                $forecast->date = $today->format('Y-m-d');

                $forecast->save();
            } catch (ErrorException $e) {
                $this->error($e->getMessage());
            }
        }

        $this->info('OK. Complete.');
    }
}
