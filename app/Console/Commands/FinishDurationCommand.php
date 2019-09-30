<?php

namespace App\Console\Commands;

use App\Group;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FinishDurationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finish:duration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finish duration group';

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
      $updated =  Group::where('end_duration','<',Carbon::now()->toDateTimeString())->where('admin_status','accept')->update(['admin_status'=>'finish_duration']);

      $admins_group = Group::whereDate('end_duration','<=',Carbon::now()->addDays(2)->toDateTimeString())->where('admin_status','accept')->get();

      $object = new Controller();

      foreach ($admins_group as $admin)
      $object->sendNotification(null,$admin->user_id,$admin->id,'end_duration');

    }
}
