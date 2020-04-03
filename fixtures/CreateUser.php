<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Mautic\UserBundle\Entity\User;
use Mautic\UserBundle\Entity\Role;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadUserData.
 */
class CreateUser extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var Password
     */
    private $password='mautic';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        
		// заводим админскую роль
		if (!$this->hasReference('admin-role')) {
            $role = new Role();
            $role->setName('Administrators');
            $role->setDescription('Has access to everything.');
            $role->setIsAdmin(1);
            $manager->persist($role);
            $manager->flush();

            $this->addReference('admin-role', $role);
        }

    	// заводим юзверя
        $user = new User();
        $user->setFirstName('Andata');
        $user->setLastName('Andata');
        $user->setUsername('it@andata.ru');
        $user->setEmail('it@andata.ru');
        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($user)
        ;
        $user->setPassword($encoder->encodePassword( array_key_exists('MAUTIC_ADMIN_PASSWORD', $_ENV) ? $_ENV['MAUTIC_ADMIN_PASSWORD'] : $this->password, $user->getSalt()));
        $user->setRole($this->getReference('admin-role'));
        $manager->persist($user);
        $manager->flush();

        $this->addReference('admin-user', $user);
    
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}