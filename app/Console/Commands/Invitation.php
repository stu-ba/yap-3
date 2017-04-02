<?php

namespace Yap\Console\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;
use Yap\Exceptions\InvitationRegistrarException;
use Yap\Foundation\InvitationRegistrar;

class Invitation extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yap:invitation
                            {email : The email of the user being invited}
                            {--a|admin : Make user also an administrator}
                            {--f|force-resend : Force resending an email}
                            {--i|indefinite : Make an invitation valid indefinitely (until signed in)}
                            {--d|dont-send : Suppress all emails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates an invitation and sends notifying email.';

    /**
     * @var InvitationRegistrar
     */
    protected $registrar;


    /**
     * Create a new command instance.
     *
     * @param InvitationRegistrar $registrar
     */
    public function __construct(InvitationRegistrar $registrar)
    {
        parent::__construct();
        $this->registrar = $registrar;
    }


    /**
     * Execute the console command.
     *
     * @throws InvitationRegistrarException
     *
     * @return mixed
     */
    public function handle()
    {
        $email = $this->validateEmail();

        $this->info('Running invitation registrar...', 'vvv');
        $invitation = $this->registrar->invite($email, $this->makeOptions());
        $this->invite($invitation);
    }


    /**
     * @return string
     */
    private function validateEmail(): string
    {
        $email = $this->argument('email');
        $this->info('Checking validity of email.', 'vv');
        if ( ! is_email($email)) {
            throw new InvalidArgumentException('Provided email is not a valid email.');
        }

        return $email;
    }


    private function makeOptions(): array
    {
        return [
            'admin'        => (bool)$this->option('admin'),
            'force_resend' => (bool)$this->option('force-resend'),
            'indefinite'   => (bool)$this->option('indefinite'),
            'dont_send'    => (bool)$this->option('dont-send'),
        ];
    }


    /**
     * @param $invitation
     */
    private function invite($invitation): void
    {
        if ($invitation->is_depleted) {
            $this->info('User '.($invitation->user->name ?? $invitation->user->username).' was granted access and can freely login to '.config('yap.short_name').'.');
        } else {
            $this->info('Invitation link:');
            $this->info(route('register', ['token' => $invitation->token]));
        }
    }
}
