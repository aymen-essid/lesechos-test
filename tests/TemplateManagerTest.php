<?php

use App\Context\ApplicationContext;
use App\Entity\Quote;
use App\Entity\Template;
use App\Repository\DestinationRepository;
use App\Repository\QuoteRepository;
use App\Repository\SiteRepository;
use App\TemplateManager;
use Faker\Factory;

class TemplateManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Init the mocks
     */
    public function setUp()
    {
    }

    /**
     * Closes the mocks
     */
    public function tearDown()
    {
    }

    /**
     * @test
     */
    public function test()
    {
        $faker = Factory::create();

        $destinationId       = $faker->randomNumber();
        $expectedDestination = DestinationRepository::getInstance()->getById($destinationId);
        $expectedUser        = ApplicationContext::getInstance()->getCurrentUser();

        $quote = new Quote($faker->randomNumber(), $faker->randomNumber(), $destinationId, $faker->date());

        $template = new Template(
            1,
            'Votre livraison à [quote:destination_name]',
            "
            Bonjour [user:first_name],

            Merci de nous avoir contacté pour votre livraison à [quote:destination_name].

            Bien cordialement,

            L'équipe de Shipper
            ");
        $templateManager = new TemplateManager(new QuoteRepository, new SiteRepository, new DestinationRepository);

        $message = $templateManager->getTemplateComputed(
            $template,
            [
                'quote' => $quote
            ]
        );

        $this->assertEquals('Votre livraison à ' . $expectedDestination->countryName, $message->subject);
        $this->assertEquals("
            Bonjour " . $expectedUser->firstname . ",

            Merci de nous avoir contacté pour votre livraison à " . $expectedDestination->countryName . ".

            Bien cordialement,

            L'équipe de Shipper
            ", $message->content);
    }
}
