<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EmployeeSeedingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:new-employee-seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $data = [
            [
                'employee_code' => 'livline01',
                'employee_name' => 'Livline・山添',
                'department_code' => '990000',
                'password' => 'livline01',
                'authorization_code' => '0',
                'mail_address' => 'syohei.yamazoe@livline.jp',
                'purchasing_approval_request_email_notification_flag' => 0,
                'delete_flag' => 0,
            ],
            [
                'employee_code' => 'livline02',
                'employee_name' => 'Livline・稲垣',
                'department_code' => '990000',
                'password' => 'livline02',
                'authorization_code' => '0',
                'mail_address' => 'syohei.yamazoe@livline.jp',
                'purchasing_approval_request_email_notification_flag' => 0,
                'delete_flag' => 0,
            ],
            [
                'employee_code' => 'livline03',
                'employee_name' => 'Livline・河野',
                'department_code' => '990000',
                'password' => 'livline03',
                'authorization_code' => '0',
                'mail_address' => 'syohei.yamazoe@livline.jp',
                'purchasing_approval_request_email_notification_flag' => 0,
                'delete_flag' => 0,
            ],
            [
                'employee_code' => 'livline04',
                'employee_name' => 'Livline・マイケル',
                'department_code' => '990000',
                'password' => 'livline04',
                'authorization_code' => '0',
                'mail_address' => 'syohei.yamazoe@livline.jp',
                'purchasing_approval_request_email_notification_flag' => 0,
                'delete_flag' => 0,
            ],
        ];

        DB::table('employees')->insert($data);
        return Command::SUCCESS;
    }
}
