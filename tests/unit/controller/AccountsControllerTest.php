<?php
namespace OCA\FirefoxSync\Controller;

class AccountsControllerTest extends \PHPUnit_Framework_TestCase {

    private $container;
    private $storage;

    protected function setUp() {
    }

    /**
     */
    public function testHkdf() {
        // Test RFC 5869 Test Case 1
        $rfc = bin2hex(AccountsController::hkdf(pack('H*','0b0b0b0b0b0b0b0b0b0b0b0b0b0b0b0b0b0b0b0b0b0b'), 'sha256', pack('H*','000102030405060708090a0b0c'), 42, pack('H*','f0f1f2f3f4f5f6f7f8f9')));

        $this->assertSame($rfc, '3cb25f25faacd57a90434f64d0362f2a2d2d0a90cf1a5a4c5db02d56ecc4c5bf34007208d5b887185865'); 

        // Test Mozilla HKDF test
        // https://github.com/mozilla/fxa-auth-server/blob/master/test/local/hkdf_tests.js
        $mozilla = bin2hex(AccountsController::hkdf(pack('H*', 'c16d46c31bee242cb31f916e9e38d60b76431d3f5304549cc75ae4bc20c7108c'), 'sha256', pack('H*', '00f000000000000000000000000000000000000000000000000000000000034d'), 2*32, 'identity.mozilla.com/picl/v1/mainKDF'));
    
        $this->assertSame($mozilla, '00f9b71800ab5337d51177d8fbc682a3653fa6dae5b87628eeec43a18af59a9d6ea660be9c89ec355397f89afb282ea0bf21095760c8c5009bbcc894155bbe2a');
    }

}
