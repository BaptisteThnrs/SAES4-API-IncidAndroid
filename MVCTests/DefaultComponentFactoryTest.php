<?php

namespace tests\application;

use application\DefaultComponentFactory;
use controllers\CreationReservationController;
use model\Requete;
use PHPUnit\Framework\TestCase;
use yasmf\NoControllerAvailableForNameException;
use yasmf\NoServiceAvailableForNameException;

class DefaultComponentFactoryTest extends TestCase
{
    private DefaultComponentFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new DefaultComponentFactory();
    }

    public function testBuildControllerByName_Home()
    {
        // Vérifie que "Home" renvoie un CreationReservationController
        $controller = $this->factory->buildControllerByName("Home");
        $this->assertInstanceOf(CreationReservationController::class, $controller);
    }

    public function testBuildControllerByName_CreationReservation()
    {
        // Vérifie que "CreationReservation" renvoie un CreationReservationController
        $controller = $this->factory->buildControllerByName("CreationReservation");
        $this->assertInstanceOf(CreationReservationController::class, $controller);
    }

    public function testBuildControllerByName_Invalid()
    {
        // Vérifie qu'un nom de contrôleur invalide lève une exception
        $this->expectException(NoControllerAvailableForNameException::class);
        $this->factory->buildControllerByName("Inconnu");
    }

    public function testBuildServiceByName_CreationReservation()
    {
        // Vérifie que "CreationReservation" renvoie un objet Requete
        $service = $this->factory->buildServiceByName("CreationReservation");
        $this->assertInstanceOf(Requete::class, $service);
    }

    public function testBuildServiceByName_Invalid()
    {
        // Vérifie qu'un nom de service invalide lève une exception
        $this->expectException(NoServiceAvailableForNameException::class);
        $this->factory->buildServiceByName("Inconnu");
    }
}
