<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use App\Models\StadiumSeat;
use App\Models\EventSeatPricing;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StarterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Users
        User::updateOrCreate(['email'=>'admin@dmatch.test'], [
            'name'=>'Admin DMATCH','password'=>Hash::make('password'),'role'=>'admin'
        ]);
        User::updateOrCreate(['email'=>'kasir@dmatch.test'], [
            'name'=>'Kasir DMATCH','password'=>Hash::make('password'),'role'=>'kasir'
        ]);
        User::updateOrCreate(['email'=>'user@dmatch.test'], [
            'name'=>'Pembeli','password'=>Hash::make('password'),'role'=>'pembeli'
        ]);

        // Event contoh
        $event = Event::create([
            'title'=>'Liga DMATCH 2025 - Opening',
            'description'=>'Pertandingan pembuka musim.',
            'start_at'=>now()->addDays(7)->setTime(19,0),
            'end_at'=>now()->addDays(7)->setTime(21,0),
            'venue'=>'Stadion Utama',
            'capacity'=>0,
            'status'=>'published',
        ]);

        // Bangun kursi (Section A, 10 row x 15 seat)
        $total = 0;
        foreach (range(1,10) as $row) {
            foreach (range(1,15) as $seat) {
                $ss = StadiumSeat::firstOrCreate([
                    'section'=>'A',
                    'row_label'=> (string)$row,
                    'seat_number'=> (string)$seat,
                ]);
                EventSeatPricing::firstOrCreate([
                    'event_id'=>$event->id,
                    'stadium_seat_id'=>$ss->id,
                ],[
                    'price'=> 75000 + ($row*1000),
                    'status'=>'available'
                ]);
                $total++;
            }
        }
        $event->update(['capacity'=>$total]);
    }
}
