<?php
namespace application;

use controllers\CreationReservationController;
use model\Requete;
use yasmf\ComponentFactory;
use yasmf\NoControllerAvailableForNameException;
use yasmf\NoServiceAvailableForNameException;
require_once __DIR__ . '/../controllers/CreationReservationController.php';
require_once __DIR__ . '/../model/Requete.php';

class DefaultComponentFactory implements ComponentFactory
{
    private ?Requete $requete = null;
    
    public function buildControllerByName(string $controller_name): mixed 
    {
        // var_dump($controller_name); // Ajoute ceci pour voir quel contrôleur est demandé
        return match ($controller_name) {
            "Home" => $this->buildCreationReservationController(),
            "CreationReservation" => $this->buildCreationReservationController(),
            default => throw new NoControllerAvailableForNameException($controller_name)
        };
    }
    
    private function buildCreationReservationModel(): Requete
    {
        if ($this->requete == null) {
            $this->requete = new Requete();
        }
        return $this->requete;
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
}
?>