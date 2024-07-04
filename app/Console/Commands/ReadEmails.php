<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;
use App\Models\Email;

class ReadEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:read';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read emails from the IMAP server';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Create an IMAP client
        $client = Client::account('default');
        $client->connect();

        // Get all folders
        $folders = $client->getFolders();

        foreach ($folders as $folder) {
            // Get all messages in the folder
            $messages = $folder->messages()->all()->get();

            foreach ($messages as $message) {
                // Process each message
                $this->info("Subject: " . $message->getSubject());
                $this->info("From: " . $message->getFrom()[0]->mail);
                $this->info("Body: " . $message->getTextBody());

                // Save email to database
                Email::create([
                    'subject' => $message->getSubject(),
                    'from'    => $message->getFrom()[0]->mail,
                    'body'    => $message->getTextBody(),
                ]);
            }
        }

        return 0;
    }
}
