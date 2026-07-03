<?php

declare(strict_types=1);

namespace Resellerclub\Tests\Api;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Resellerclub\Api\Billing;
use Resellerclub\Tests\Support\MockTransport;
use Resellerclub\Validation\Validator;

#[CoversClass(Billing::class)]
final class BillingTest extends TestCase
{
    public function testDiscountInvoiceBuildsWellFormedBody(): void
    {
        $transport = new MockTransport();
        $transport->willReturn();
        $billing = new Billing($transport->apiClient, new Validator());

        // Regression: the legacy code used ('discount-without-tax', $discount),
        // producing a malformed array entry. It must now be a proper key=value.
        $billing->discountInvoice(555, 12.5, 'tx-key', 'customer');

        $body = (string) $transport->lastRequest()->getBody();
        self::assertStringContainsString('invoice-id=555', $body);
        self::assertStringContainsString('discount-without-tax=12.5', $body);
        self::assertStringContainsString('transaction-key=tx-key', $body);
        self::assertStringContainsString('role=customer', $body);
    }

    public function testSuspendOrderBuildsWellFormedBody(): void
    {
        $transport = new MockTransport();
        $transport->willReturn();
        $billing = new Billing($transport->apiClient, new Validator());

        // Regression: legacy code used ('reason', $reason).
        $billing->suspendOrder(123, 'fraud');

        $body = (string) $transport->lastRequest()->getBody();
        self::assertStringContainsString('order-id=123', $body);
        self::assertStringContainsString('reason=fraud', $body);
    }

    /**
     * @param callable(Billing): mixed $call
     */
    #[DataProvider('callProvider')]
    public function testIssuesExpectedRequest(callable $call, string $method, string $pathContains): void
    {
        $transport = new MockTransport();
        $transport->willReturn();
        $billing = new Billing($transport->apiClient, new Validator());

        $call($billing);

        $request = $transport->lastRequest();
        self::assertSame($method, $request->getMethod());
        self::assertStringContainsString($pathContains, (string) $request->getUri());
    }

    /**
     * @return iterable<string, array{callable(Billing): mixed, string, string}>
     */
    public static function callProvider(): iterable
    {
        yield 'getCustomerPricing' => [
            fn (Billing $b) => $b->getCustomerPricing('13560800'),
            'GET', '/api/products/customer-price.json',
        ];
        yield 'getResellerPricing' => [
            fn (Billing $b) => $b->getResellerPricing(999),
            'GET', '/api/products/reseller-price.json',
        ];
        yield 'getResellerCostPricing' => [
            fn (Billing $b) => $b->getResellerCostPricing(999),
            'GET', '/api/products/reseller-cost-price.json',
        ];
        yield 'getCustomerTransactionDetails' => [
            fn (Billing $b) => $b->getCustomerTransactionDetails([1, 2, 3]),
            'GET', '/api/products/customer-transactions.json',
        ];
        yield 'getResellerTransactionDetails' => [
            fn (Billing $b) => $b->getResellerTransactionDetails(1),
            'GET', '/api/products/reseller-transactions.json',
        ];
        yield 'payTransactions' => [
            fn (Billing $b) => $b->payTransactions([1], [2]),
            'POST', '/api/billing/customer-pay.json',
        ];
        yield 'cancelInvoiceDebitNote' => [
            fn (Billing $b) => $b->cancelInvoiceDebitNote([1], [2]),
            'POST', '/api/billing/customer-transactions/cancel.json',
        ];
        yield 'getCustomerBalance' => [
            fn (Billing $b) => $b->getCustomerBalance('13560800'),
            'GET', '/api/billing/customer-balance.json',
        ];
        yield 'executeOrderWithoutPayment' => [
            fn (Billing $b) => $b->executeOrderWithoutPayment([1], true),
            'POST', '/api/billing/execute-order-without-payment.json',
        ];
        yield 'searchCustomerTransaction' => [
            fn (Billing $b) => $b->searchCustomerTransaction(['type' => 'invoice'], 1, 10),
            'GET', '/api/billing/customer-transactions/search.json',
        ];
        yield 'searchResellerTransaction' => [
            fn (Billing $b) => $b->searchResellerTransaction(['type' => 'invoice'], 1, 10),
            'GET', '/api/billing/reseller-transactions/search.json',
        ];
        yield 'getResellerBalance' => [
            fn (Billing $b) => $b->getResellerBalance(999),
            'GET', '/api/billing/reseller-balance.json',
        ];
        yield 'addFundsCustomer' => [
            fn (Billing $b) => $b->addFundsCustomer('13560800', ['amount' => 100]),
            'POST', '/api/billing/add-customer-fund.json',
        ];
        yield 'addFundsReseller' => [
            fn (Billing $b) => $b->addFundsReseller(999, ['amount' => 100]),
            'POST', '/api/billing/add-reseller-fund.json',
        ];
        yield 'addDebitNoteCustomer' => [
            fn (Billing $b) => $b->addDebitNoteCustomer('13560800', ['amount' => 100]),
            'POST', '/api/billing/add-customer-debit-note.json',
        ];
        yield 'addDebitNoteReseller' => [
            fn (Billing $b) => $b->addDebitNoteReseller(999, ['amount' => 100]),
            'POST', '/api/billing/add-reseller-debit-note.json',
        ];
        yield 'unsuspendOrder' => [
            fn (Billing $b) => $b->unsuspendOrder(123),
            'POST', '/api/orders/unsuspend.json',
        ];
        yield 'getCurrentActions' => [
            fn (Billing $b) => $b->getCurrentActions(['status' => 'current'], 1, 10),
            'GET', '/api/actions/search-current.json',
        ];
        yield 'getArchiveActions' => [
            fn (Billing $b) => $b->getArchiveActions(['status' => 'archived'], 1, 10),
            'GET', '/api/actions/search-archived.json',
        ];
        yield 'getLegalAgreement' => [
            fn (Billing $b) => $b->getLegalAgreement('customer'),
            'GET', '/api/commons/legal-agreements.json',
        ];
        yield 'getAllowedPaymentGatewayCustomer' => [
            fn (Billing $b) => $b->getAllowedPaymentGatewayCustomer('13560800', 'AddFund'),
            'GET', '/api/pg/allowedlist-for-customer.json',
        ];
        yield 'getAllowedPaymentGatewayCustomer without type' => [
            fn (Billing $b) => $b->getAllowedPaymentGatewayCustomer('13560800'),
            'GET', '/api/pg/allowedlist-for-customer.json',
        ];
        yield 'getAllowedPaymentGatewayReseller' => [
            fn (Billing $b) => $b->getAllowedPaymentGatewayReseller(),
            'GET', '/api/pg/list-for-reseller.json',
        ];
        yield 'getCurrencyDetails' => [
            fn (Billing $b) => $b->getCurrencyDetails(),
            'GET', '/api/currency/details.json',
        ];
        yield 'getCountryList' => [
            fn (Billing $b) => $b->getCountryList(),
            'GET', '/api/country/list.json',
        ];
        yield 'getStateList' => [
            fn (Billing $b) => $b->getStateList('US'),
            'POST', '/api/country/state-list.json',
        ];
    }
}
