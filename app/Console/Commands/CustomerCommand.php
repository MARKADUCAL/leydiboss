<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use Illuminate\Http\UploadedFile;

class CustomerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:update {email? : Optional argument of the user\'s email} {--uppercase : Update the user\'s given name to uppercase} {--lowercase : Update the user\'s given name to lowercase} {--avatar : Update the user\'s avatar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates customer given name to uppercase/lowercase or updates their avatar';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $upperName = $this->option('uppercase');
        $lowerName = $this->option('lowercase');
        $updateAvatar = $this->option('avatar');

        if (!$upperName && (!$lowerName) && (!$updateAvatar)) {
            $this->alert('Please specify what to update using --uppercase, --lowercase or --avatar flag.');
            return;
        }

        $query = Customer::query();

        if ($email) {
            $query->where('email', $email);
        }

        $customers = $query->get();

        if ($customers->isEmpty()) {
            $this->alert('No customers found.');
            return;
        }

        $this->info('Updating ' . $customers->count() . ' customer(s)...');
        
        $this->withProgressBar($customers, function ($customer) use ($upperName, $lowerName, $updateAvatar) {
            

            if ($upperName || $lowerName) {
                $parts = explode(' ', $customer->name);
                if (count($parts) > 0) {
                    if ($upperName) {
                        $parts[0] = strtoupper($parts[0]);
                    } elseif ($lowerName) {
                        $parts[0] = strtolower($parts[0]);
                    }
                    $customer->name = implode(' ', $parts);
                }
            }

            if ($updateAvatar) {
                try {
                    $file = UploadedFile::fake()->createWithContent(
                        'avatar.png',
                        file_get_contents('https://i.pravatar.cc/300?img=' . fake()->numberBetween(1, 70))
                    );
                    
                    $path = $file->store('profile-photos', 'public');
                    $customer->profile_photo_path = $path;
                } catch (\Exception $e) {

                }
            }

           
            $customer->save();
        });
        
        $this->newLine();
        $this->info('Update completed successfully.');
    }
}
