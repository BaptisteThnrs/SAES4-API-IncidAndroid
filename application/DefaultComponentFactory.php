<?php
namespace application;

use controllers\CreationReservationController;
use model\Requete;
use yasmf\ComponentFactory;
use yasmf\NoControllerAvailableForNameException;
use yasmf\NoServiceAvailableForNameException;

class DefaultComponentFactory implements ComponentFactory
{
    private ?Requete $requete = null;

    public function buildControllerByName(string $controller_name): mixed 
    {
        return match ($controller_name) {
            "CreationReservation" => $this->buildCreationReservationController(),
            default => throw new NoControllerAvailableForNameException($controller_name)
        };
    }

    public function buildServiceByName(string $service_name): mixed
    {
        return match ($service_name) {
            "CreationReservation" => $this->buildCreationReservationModel(),
            default => throw new NoServiceAvailableForNameException($service_name)
        };
    }

    private function buildCreationReservationController(): CreationReservationController
    {
        return new CreationReservationController($this->buildServiceByName("CreationReservation"));
    }

    private function buildCreationReservationModel(): Requete
    {
        if ($this->requete == null) {
            $this->requete = new Requete();
        }
        return $this->requete;
    }
}
?>