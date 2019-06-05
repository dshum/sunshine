<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\City;
use App\Forecast;

class LoadInitialData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loads initial weather data.';

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
        $cities = [
            'Moscow' => [
                'Sunny' => 50,
                'Cloudy' => 150,
                'Rain' => 50,
                'Snow' => 50,
            ],
            'Novosibirsk' => [
                'Sunny' => 100,
                'Cloudy' => 50,
                'Rain' => 50,
                'Snow' => 100,
            ],
            'Vladivostok' => [
                'Sunny' => 50,
                'Cloudy' => 100,
                'Rain' => 100,
                'Snow' => 50,
            ],
        ];

        foreach ($cities as $city_name => $conditions) {
            $this->info($city_name);

            $city = new City;
            $city->name = $city_name;
            $city->save();

            $date = Carbon::today();

            for ($offset = 0; $offset < 365; $offset++) {
                $condition = $this->random_probability($conditions);
                
                $forecast = new Forecast;
                $forecast->city_id = $city->id;
                $forecast->condition = $condition;
                $forecast->date = $date;
                $forecast->save();

                // $this->line($date->format('Y-m-d').' - '.$condition);

                $date->subDay();
            }
        }

        $this->info('OK. Complete.');
    }

    private function random_probability($probabilities) {
        $rand = rand(0, array_sum($probabilities));

        do {
            $sum = array_sum($probabilities);

            if($rand <= $sum && $rand >= $sum - end($probabilities)) {
                return key($probabilities);
            }
        } while(array_pop($probabilities));
    }
}
