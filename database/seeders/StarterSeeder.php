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

        $layout = [
            ['class'=>'regular','ring'=>1,'count'=>64,'price'=>75000],
            ['class'=>'vip',    'ring'=>2,'count'=>40,'price'=>150000],
            ['class'=>'vvip',   'ring'=>3,'count'=>24,'price'=>300000],
        ];

        $total = 0;
        foreach ($layout as $tier) {
            for ($i=0; $i < $tier['count']; $i++) {
                $angle = (int) round(360 * ($i / $tier['count']));
                $ss = StadiumSeat::firstOrCreate(
                    [
                        'section'     => strtoupper($tier['class'][0]), // R / V / V (tetap unik dengan row+seat)
                        'row_label'   => (string)$tier['ring'],         // pakai ring sbg row
                        'seat_number' => (string)($i+1),
                    ],
                    [
                        'seat_class' => $tier['class'],
                        'ring'       => $tier['ring'],
                        'angle_deg'  => $angle,
                    ]
                );

                EventSeatPricing::firstOrCreate(
                    ['event_id'=>$event->id,'stadium_seat_id'=>$ss->id],
                    ['price'=>$tier['price'],'status'=>'available']
                );
                $total++;
            }
        }
        $event->update(['capacity'=>$total]);
        }
}
