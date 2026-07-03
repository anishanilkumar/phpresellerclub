<?php

declare(strict_types=1);

namespace Resellerclub\Tests\Api;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Resellerclub\Api\Customer;
use Resellerclub\Tests\Support\MockTransport;
use Resellerclub\Validation\Validator;

#[CoversClass(Customer::class)]
final class CustomerTest extends TestCase
{
    public function testCreateCustomerValidatesAndPosts(): void
    {
        $transport = new MockTransport();
        $transport->willReturn('13560800');
        $customer = new Customer($transport->apiClient, new Validator());

        $result = $customer->createCustomer(self::validCustomer());

        $request = $transport->lastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('https://test.httpapi.com/api/customers/signup.json', (string) $request->getUri());
        self::assertSame(13560800, $result);
    }

    /**
     * @param callable(Customer): mixed $call
     */
    #[DataProvider('callProvider')]
    public function testIssuesExpectedRequest(callable $call, string $method, string $pathContains): void
    {
        $transport = new MockTransport();
        $transport->willReturn();
        $customer = new Customer($transport->apiClient, new Validator());

        $call($customer);

        $request = $transport->lastRequest();
        self::assertSame($method, $request->getMethod());
        self::assertStringContainsString($pathContains, (string) $request->getUri());
    }

    /**
     * @return iterable<string, array{callable(Customer): mixed, string, string}>
     */
    public static function callProvider(): iterable
    {
        yield 'editCustomer' => [
            fn (Customer $c) => $c->editCustomer('13560800', self::validCustomer()),
            'POST', '/api/customers/modify.json',
        ];
        yield 'getCustomerByUserName' => [
            fn (Customer $c) => $c->getCustomerByUserName('regina.phelange@example.com'),
            'GET', '/api/customers/details.json',
        ];
        yield 'getCustomerByCustomerId' => [
            fn (Customer $c) => $c->getCustomerByCustomerId('13560800'),
            'GET', '/api/customers/details-by-id.json',
        ];
        yield 'generateToken' => [
            fn (Customer $c) => $c->generateToken('regina.phelange@example.com', 'Rand@123om', '1.2.3.4'),
            'POST', '/api/customers/generate-token.json',
        ];
        yield 'authenticateToken' => [
            fn (Customer $c) => $c->authenticateToken('token-value'),
            'POST', '/api/customers/authenticate-token.json',
        ];
        yield 'changePassword' => [
            fn (Customer $c) => $c->changePassword('13560800', 'NewPass@123'),
            'POST', '/api/customers/change-password.json',
        ];
        yield 'generateTemporaryPassword' => [
            fn (Customer $c) => $c->generateTemporaryPassword('13560800'),
            'POST', '/api/customers/temp-password.json',
        ];
        yield 'searchCustomer' => [
            fn (Customer $c) => $c->searchCustomer(['status' => 'active'], 20, 1),
            'GET', '/api/customers/search.json',
        ];
        yield 'forgotPassword' => [
            fn (Customer $c) => $c->forgotPassword('regina.phelange@example.com'),
            'POST', '/api/customers/forgot-password.json',
        ];
        yield 'deleteCustomer' => [
            fn (Customer $c) => $c->deleteCustomer('13560800'),
            'POST', '/api/customers/delete.json',
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function validCustomer(): array
    {
        return [
            'username' => 'regina.phelange@example.com',
            'passwd' => 'Rand@123om',
            'name' => 'Regina Phelange',
            'company' => 'N/A',
            'address-line-1' => 'Test Address Line',
            'city' => 'New York',
            'state' => 'NY',
            'country' => 'US',
            'zipcode' => '567889',
            'phone-cc' => '91',
            'phone' => '9876543210',
            'lang-pref' => 'en',
        ];
    }
}
