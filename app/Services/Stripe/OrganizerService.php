<?php

declare(strict_types=1);

namespace App\Services\Stripe;

use Exception;
use PhpParser\Node\Stmt\TryCatch;
use Stripe\Account;
use Stripe\AccountLink;

class OrganizerService extends BaseService
{
    public function createAccount(string $email): array|bool
    {
        try {
            $account = Account::create([
                'type' => 'express',
                'country' => 'BR',
                'email' => $email,
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ]
            ]);

            $linkUrl = $this->getLink($account->id);

            if (!is_string($linkUrl)) {
                throw new Exception("Erro ao gerar o link de criação/conclusão da conta na Stripe.");
            }

            return [
                'id' => $account->id,
                'link' => $linkUrl
            ];
        } catch (\Throwable $th) {
            log_message('error', '[STRIPE ERROR] {exception}', ['exception' => $th]);
            return false;
        }
    }

    public function getLink(string $stripeAccountId): string|bool
    {
        try {

            $cacheKey = "stripe_connect_link_{$stripeAccountId}";

            $cacheLink = cache($cacheKey);

            if ($cacheLink) {
                return $cacheLink;
            }

            $url = url_to('dashboard.organizer');

            $link = AccountLink::create([
                'account' => $stripeAccountId,
                'refresh_url' => $url,
                'return_url' => $url,
                'type' => 'account_onboarding'
            ]);

            cache()->save($cacheKey, $link->url, 7200);

            return $link->url;
        } catch (\Throwable $th) {
            log_message('error', '[STRIPE ERROR] {exception}', ['exception' => $th]);
            return false;
        }
    }

    public function getAccount(string $stripeAccountId): Account|bool
    {
        try {

            $account = Account::retrieve($stripeAccountId);

            return $account;
        } catch (\Throwable $th) {
            log_message('error', '[STRIPE ERROR] {exception}', ['exception' => $th]);
            return false;
        }
    }

    public function accountIsCompleted(Account $account): bool
    {
        $stripeAccountIsCompleted =
            $account->details_submitted &&
            $account->charges_enabled &&
            $account->payouts_enabled &&
            empty($account->requirements->currently_due);

        return $stripeAccountIsCompleted;
    }

    public function getDashboardLink(string $stripeAccountId): string|bool
    {
        try {

            $cacheKey = "stripe_dashboard_link_{$stripeAccountId}";

            $cacheLink = cache($cacheKey);

            if ($cacheLink) {
                return $cacheLink;
            }

            $link = Account::createLoginLink($stripeAccountId);

            cache()->save($cacheKey, $link->url, 7200);

            return $link->url;
        } catch (\Throwable $th) {
            log_message('error', '[STRIPE ERROR] {exception}', ['exception' => $th]);
            return false;
        }
    }
}
