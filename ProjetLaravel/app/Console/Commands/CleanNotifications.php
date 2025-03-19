<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanNotifications extends Command
{
    protected $signature = 'notifications:clean';
    protected $description = 'Supprime les notifications de plus de 30 jours';

    public function handle()
    {
        $date = Carbon::now()->subDays(30);
        
        // Supprime les notifications de plus de 30 jours
        $count = Notification::where('date_envoi', '<', $date)->delete();

        $this->info("{$count} notifications ont été supprimées.");
    }
} 