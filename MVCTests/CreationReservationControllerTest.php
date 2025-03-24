<?php

namespace tests\controllers;

use controllers\CreationReservationController;
use model\Requete;
use PHPUnit\Framework\TestCase;
use PDO;

class CreationReservationControllerTest extends TestCase
{
    private CreationReservationController $controller;
    private $requeteMock;
    private $pdoMock;

    public function setUp(): void
    {
        parent::setUp();
        // given a mock of PDO
        $this->pdoMock = $this->createMock(PDO::class);

        // given a mock of Requete
        $this->requeteMock = $this->createMock(Requete::class);

        // given the controller with the mocked Requete
        $this->controller = new CreationReservationController($this->requeteMock);
    }

    public function testIndexReturnsView()
    {
        // when we set up the mocked data for listeSalles, listeActivites, and affichageReservation
        $this->requeteMock->method('listeSalles')->willReturn(['Salle A', 'Salle B']);
        $this->requeteMock->method('listeActivites')->willReturn(['Activité 1', 'Activité 2']);
        $this->requeteMock->method('affichageReservation')->willReturn(['Reservation 1']);

        // when calling the index method
        $view = $this->controller->index($this->pdoMock);

        // then it should return the correct view with the expected variables
        self::assertEquals('../views/creationReservation', $view->getRelativePath());
        self::assertEquals(['Salle A', 'Salle B'], $view->getVar('listeSalles'));
        self::assertEquals(['Activité 1', 'Activité 2'], $view->getVar('listeActivites'));
        self::assertEquals(['Reservation 1'], $view->getVar('affichageReservation'));
    }

    public function testAjoutReservationHandlesInsertion()
    {
        // when we simulate an insertion with the mocked Requete
        $this->requeteMock->expects($this->once())
            ->method('insertionReservation')
            ->with(
                $this->pdoMock,
                'Salle A',
                'Activité 1',
                '2024-06-10',
                '08:00',
                '10:00',
                'Réunion',
                'Dupont',
                'Jean',
                '0606060606',
                'Détails supplémentaires',
                1
            );

        // simulate mocked data for the lists after insertion
        $this->requeteMock->method('listeSalles')->willReturn(['Salle A']);
        $this->requeteMock->method('listeActivites')->willReturn(['Activité 1']);
        $this->requeteMock->method('affichageReservation')->willReturn(['Reservation 1']);

        // Simulate GET parameters as if they were coming from an HTTP request
        $_GET = [
            'nomSalle' => 'Salle A',
            'nomActivite' => 'Activité 1',
            'date' => '2024-06-10',
            'heureDebut' => '08:00',
            'heureFin' => '10:00',
            'objet' => 'Réunion',
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'numTel' => '0606060606',
            'precisActivite' => 'Détails supplémentaires'
        ];

        // when calling the ajoutReservation method
        $view = $this->controller->ajoutReservation($this->pdoMock);

        // then it should return the correct view with the updated variables
        self::assertEquals(['Salle A'], $view->getVar('listeSalles'));
        self::assertEquals(['Activité 1'], $view->getVar('listeActivites'));
        self::assertEquals(['Reservation 1'], $view->getVar('affichageReservation'));
    }
}
