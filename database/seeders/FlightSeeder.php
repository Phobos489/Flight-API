<?php
// database/seeders/FlightSeeder.php

namespace Database\Seeders;

use App\Models\Flight;
use App\Models\Airline;
use App\Models\Airport;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class FlightSeeder extends Seeder
{
    public function run(): void
    {
        $airlines = Airline::all();
        $airports = Airport::all();
        
        if ($airlines->isEmpty() || $airports->isEmpty()) {
            $this->command->warn('Please run AirlineSeeder and AirportSeeder first!');
            return;
        }

        $statuses = ['SCHEDULED', 'BOARDING', 'DELAYED', 'DEPARTED', 'CANCELLED'];
        $gates = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2', 'D1', 'D2', 'E1', 'E2'];
        $terminals = ['1', '2', '3'];

        // Generate flights for today
        $today = Carbon::today();
        
        for ($i = 0; $i < 50; $i++) {
            $airline = $airlines->random();
            $origin = $airports->random();
            $destination = $airports->where('id', '!=', $origin->id)->random();
            
            // Random departure time throughout the day
            $departureTime = $today->copy()->addHours(rand(0, 23))->addMinutes(rand(0, 59));
            $arrivalTime = $departureTime->copy()->addHours(rand(1, 5))->addMinutes(rand(0, 59));
            
            $status = $statuses[array_rand($statuses)];
            
            // Assign actual times if departed or arrived
            $actualDeparture = null;
            $actualArrival = null;
            $delayMinutes = 0;
            
            if (in_array($status, ['DEPARTED', 'ARRIVED'])) {
                $delayMinutes = rand(0, 60);
                $actualDeparture = $departureTime->copy()->addMinutes($delayMinutes);
                
                if ($status === 'ARRIVED') {
                    $actualArrival = $arrivalTime->copy()->addMinutes($delayMinutes);
                }
            }
            
            Flight::create([
                'airline_id' => $airline->id,
                'flight_number' => $airline->code . '-' . rand(100, 999),
                'origin_airport_id' => $origin->id,
                'destination_airport_id' => $destination->id,
                'scheduled_departure' => $departureTime,
                'scheduled_arrival' => $arrivalTime,
                'actual_departure' => $actualDeparture,
                'actual_arrival' => $actualArrival,
                'gate' => $gates[array_rand($gates)],
                'terminal' => $terminals[array_rand($terminals)],
                'status' => $status,
                'remarks' => $status === 'DELAYED' ? 'Weather conditions' : null,
                'delay_minutes' => $delayMinutes,
            ]);
        }

        // Generate flights for tomorrow
        $tomorrow = Carbon::tomorrow();
        
        for ($i = 0; $i < 30; $i++) {
            $airline = $airlines->random();
            $origin = $airports->random();
            $destination = $airports->where('id', '!=', $origin->id)->random();
            
            $departureTime = $tomorrow->copy()->addHours(rand(0, 23))->addMinutes(rand(0, 59));
            $arrivalTime = $departureTime->copy()->addHours(rand(1, 5))->addMinutes(rand(0, 59));
            
            Flight::create([
                'airline_id' => $airline->id,
                'flight_number' => $airline->code . '-' . rand(100, 999),
                'origin_airport_id' => $origin->id,
                'destination_airport_id' => $destination->id,
                'scheduled_departure' => $departureTime,
                'scheduled_arrival' => $arrivalTime,
                'gate' => $gates[array_rand($gates)],
                'terminal' => $terminals[array_rand($terminals)],
                'status' => 'SCHEDULED',
                'delay_minutes' => 0,
            ]);
        }

        $this->command->info('Flights seeded successfully!');
    }
}