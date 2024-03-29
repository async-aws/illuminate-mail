<?php

declare(strict_types=1);

namespace AsyncAws\Illuminate\Mail;

use AsyncAws\Illuminate\Mail\Transport\AsyncAwsSesTransport;
use AsyncAws\Ses\SesClient;
use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider as AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        /** @var MailManager $manager */
        $manager = $this->app['mail.manager'];

        $manager->extend('async-aws-ses', \Closure::fromCallable([$this, 'createTransport']));
    }

    public function createTransport(array $config): AsyncAwsSesTransport
    {
        $clientConfig = [];
        if (isset($config['key']) && isset($config['secret'])) {
            $clientConfig['accessKeyId'] = $config['key'];
            $clientConfig['accessKeySecret'] = $config['secret'];
            $clientConfig['sessionToken'] = $config['token'] ?? null;
        }

        if (!empty($config['region'])) {
            $clientConfig['region'] = $config['region'];
        }

        $sesClient = new SesClient($clientConfig);

        return new AsyncAwsSesTransport($sesClient, $config);
    }
}
