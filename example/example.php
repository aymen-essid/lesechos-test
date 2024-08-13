<?php

require_once '../vendor/autoload.php';


use App\Entity\Quote;
use App\Entity\Template;
use App\Repository\DestinationRepository;
use App\Repository\QuoteRepository;
use App\Repository\SiteRepository;
use App\TemplateManager;
use Faker\Factory;

$faker = Factory::create();

$template = new Template( 1, 'Votre livraison à [quote:destination_name]', " Bonjour [user:first_name], Merci de nous avoir contacté pour votre livraison à [quote:destination_name]. Bien cordialement,L'équipe de Shipper");

$templateManager = new TemplateManager(new QuoteRepository, new SiteRepository, new DestinationRepository);

$message = $templateManager->getTemplateComputed(
    $template,
    [
        'quote' => new Quote($faker->randomNumber(), $faker->randomNumber(), $faker->randomNumber(), $faker->date())
    ]
);

echo $message->subject . "\n" . $message->content;
