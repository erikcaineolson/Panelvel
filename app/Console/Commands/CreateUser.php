<?php namespace App\Console\Commands;

use App\User;
use Exception;
use Illuminate\Console\Command;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

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
        $emailTries = 0;
        $passwordTries = 0;

        // get the email, provided we don't already have a user in the system with this email
        do {
            if ($emailTries > 0) {
                $this->error('Email exists, please try again.');
            }

            $email = $this->ask('Please enter the email: ');
            $emailTries++;
        } while (User::where('email', $email)->count() > 0);

        $autoGeneratePassword = $this->confirm('Do you want to auto-generate the password? [y|N]');

        if($autoGeneratePassword){
            $password = file_get_contents(env('PASSWORD_GENERATOR_URL'));
        } else {
            // get the password, provided the passwords match
            do {
                if ($passwordTries > 0) {
                    $this->error('Password exists, please try again.');
                }

                $password = $this->secret('Please enter the user\'s password: ');
                $confirm = $this->secret('Please confirm the user\'s password: ');
                $passwordTries++;
            } while ($password != $confirm);
        }

        $firstName = $this->ask('Please enter the user\'s first name: ');
        $lastName = $this->ask('Please enter the user\'s last name: ');

        try {
            User::create([
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'email'      => $email,
                'password'   => bcrypt($password),
            ]);
        } catch (Exception $e) {
            $this->error($e->getMessage());
            exit(1);
        }

        $this->info('User ' . $email . ' with password ' . $password . ' has been successfully added!');
    }
}
