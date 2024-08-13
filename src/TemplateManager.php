<?php

namespace App;

use App\Context\ApplicationContext;
use App\Entity\Quote;
use App\Entity\Template;
use App\Entity\User;
use App\Repository\DestinationRepository;
use App\Repository\QuoteRepository;
use App\Repository\SiteRepository;
use RuntimeException;

class TemplateManager implements TemplateInterface
{
    private $quoteRepository;
    private $siteRepository;
    private $destinationRepository;
    private $applicationContext;

    public function __construct(
        QuoteRepository $quoteRepository,
        SiteRepository $siteRepository,
        DestinationRepository $destinationRepository
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->siteRepository = $siteRepository;
        $this->destinationRepository = $destinationRepository;
        $this->applicationContext = ApplicationContext::getInstance();
    }

    public function getTemplateComputed(Template $tpl, array $data): Template
    {
        if (!$tpl) {
            throw new RuntimeException('No template provided');
        }

        $tpl->subject = $this->computeText($tpl->subject, $data);
        $tpl->content = $this->computeText($tpl->content, $data);

        return $tpl;
    }

    private function computeText(string $text, array $data): string
    {
        $quote = $data['quote'] ?? null;
        if ($quote instanceof Quote) {
            $text = $this->processQuotePlaceholders($text, $quote);
        }

        $user = $data['user'] ?? $this->applicationContext->getCurrentUser();
        if ($user instanceof User) {
            $text = $this->processUserPlaceholders($text, $user);
        }

        return $text;
    }

    private function processQuotePlaceholders(string $text, Quote $quote): string
    {
        $quoteFromRepo = $this->quoteRepository->getById($quote->id);
        $site = $this->siteRepository->getById($quote->siteId);
        $destination = $this->destinationRepository->getById($quote->destinationId);

        $text = $this->replacePlaceholder($text, '[quote:summary_html]', Quote::renderHtml($quoteFromRepo));
        $text = $this->replacePlaceholder($text, '[quote:summary]', Quote::renderText($quoteFromRepo));
        $text = $this->replacePlaceholder($text, '[quote:destination_name]', $destination->countryName);

        $destinationLink = $site->url . '/' . $destination->countryName . '/quote/' . $quoteFromRepo->id;
        $text = $this->replacePlaceholder($text, '[quote:destination_link]', $destinationLink);

        return $text;
    }

    private function processUserPlaceholders(string $text, User $user): string
    {
        return $this->replacePlaceholder($text, '[user:first_name]', ucfirst(mb_strtolower($user->firstname)));
    }

    private function replacePlaceholder(string $text, string $placeholder, ?string $replacement): string
    {
        return str_replace($placeholder, $replacement ?? '', $text);
    }
}
