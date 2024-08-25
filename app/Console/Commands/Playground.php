<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Playground extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:playground';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

    }

    function encrypt($data, $key)
    {
        $encrypted = openssl_encrypt($data, "aes-128-ecb", $key, 0,);
        return ($encrypted);
    }

    function decrypt($encrypted, $key)
    {
        $rawData = ($encrypted);
        $decrypted = openssl_decrypt($rawData, 'AES-128-ECB', $key, 1);
        $data = ($decrypted);
        return $data;
    }

    function padZero($data, $blocksize = 16)
    {
        $pad = $blocksize - (strlen($data) % $blocksize);
        return $data . str_repeat("\0", $pad);
    }

    function unpadZero($data)
    {
        return rtrim($data, "\0");
    }
}
