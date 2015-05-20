<?php

/*
 * This file is part of the Toyota Legacy PHP framework package.
 *
 * (c) Toyota Industrial Equipment <cyril.cottet@toyota-industries.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CelcatManagement\LDAPManagerBundle\LDAP\Tests\Platform\Test;

use CelcatManagement\LDAPManagerBundle\LDAP\Tests\TestCase;
use CelcatManagement\LDAPManagerBundle\LDAP\Platform\Test\Driver;
use CelcatManagement\LDAPManagerBundle\LDAP\Exception\ConnectionException;

class DriverTest extends TestCase
{
    /**
     * Tests connect
     *
     * @return void
     */
    public function testConnect()
    {
        $driver = new Driver();

        $conn = $driver->connect('host', 999, true, true);
        $this->assertEquals('host', $driver->getHostname());
        $this->assertEquals(999, $driver->getPort());
        $this->assertTrue($driver->hasSSL());
        $this->assertTrue($driver->hasTLS());
        $this->assertInstanceOf('CelcatManagement\LDAPManagerBundle\LDAP\Platform\Test\Connection', $conn);
        $this->assertEquals($conn, $driver->getConnection());

        $driver->connect('host', 999, false, true);
        $this->assertFalse($driver->hasSSL());
        $this->assertTrue($driver->hasTLS());

        $driver->setFailureFlag();
        try {
            $driver->connect('other');
            $this->fail('ConnectionException is raised when driver flag is set');
        } catch (ConnectionException $e) {
            $this->assertRegExp('/Cannot connect/', $e->getMessage());
        }

        try {
            $driver->connect('other');
            $this->fail('Flag is still set so exceptions keep throwing');
        } catch (ConnectionException $e) {
            $this->assertRegExp('/Cannot connect/', $e->getMessage());
        }

        $driver->setFailureFlag(false);
        $driver->connect('other');
        $this->assertEquals('other', $driver->getHostname());
        $this->assertEquals(389, $driver->getPort());
        $this->assertFalse($driver->hasSSL());
        $this->assertFalse($driver->hasTLS());
    }
}
