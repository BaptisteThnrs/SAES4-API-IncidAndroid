<?php

use PHPUnit\Framework\TestCase;
use API\Donnees;
use PHPUnit\Framework\MockObject\MockObject;
use PDO;
use PDOStatement;

class DonneesTests extends TestCase
{
    private Donnees $donnees;
    private MockObject|PDO $mockPDO;
    private MockObject|PDOStatement $mockStmt;

    protected function setUp(): void
    {
        // Créer un mock de PDO
        $this->mockPDO = $this->createMock(PDO::class);

        // Créer un mock de PDOStatement
        $this->mockStmt = $this->createMock(PDOStatement::class);

        // Configurer le mock PDO pour retourner notre mock de PDOStatement
        $this->mockPDO->method('prepare')->willReturn($this->mockStmt);

        // Créer l'instance de Donnees avec le mock de PDO
        $this->donnees = $this->getMockBuilder(Donnees::class)
                               ->onlyMethods(['connecteBD'])
                               ->getMock();

        // Remplacer la méthode connecteBD par une méthode qui retourne notre mock PDO
        $this->donnees->method('connecteBD')->willReturn($this->mockPDO);
    }

    public function testGetReservation()
    {
        // Préparer les résultats attendus
        $data = [
            [
                'id_reservation' => 1,
                'nom_salle' => 'Salle A',
                'nom_employe' => 'Dupont',
                'prenom_employe' => 'Pierre',
                'nom_activite' => 'Réunion',
                'date' => '2025-03-24',
                'heure_debut' => '09:00',
                'heure_fin' => '10:00',
            ]
        ];

        // Configurer le mock PDOStatement pour retourner les résultats simulés
        $this->mockStmt->method('fetchAll')->willReturn($data);

        // Configurer sendJSON pour ne pas exécuter réellement la fonction (on va vérifier son appel après)
        $this->donnees->method('sendJSON')->willReturn(true);

        // Appeler la méthode à tester
        $this->donnees->getReservation("1");

        // Vérifier que sendJSON a été appelé avec les bons arguments
        $this->expectOutputString(json_encode($data, JSON_PRETTY_PRINT));
    }

    public function testGetIncident()
    {
        $data = [
            [
                'id_incident' => 1,
                'resume' => 'Problème technique',
                'description' => 'La salle est défectueuse',
                'service_technique' => 'Maintenance',
                'id_reservation' => 1,
                'intitule' => 'Incident 1',
                'date_reservation' => '2025-03-24',
                'heure_debut' => '09:00',
                'heure_fin' => '10:00',
                'nom' => 'Salle A',
                'date_signalement' => '2025-03-24',
                'heure_signalement' => '09:30',
            ]
        ];

        $this->mockStmt->method('fetchAll')->willReturn($data);
        $this->donnees->method('sendJSON')->willReturn(true);

        $this->donnees->getIncident();

        $this->expectOutputString(json_encode($data, JSON_PRETTY_PRINT));
    }

    public function testPutIncident()
    {
        // Test de la méthode putIncident
        $data = [
            'resume' => 'Problème de chauffage',
            'description' => 'Le chauffage est en panne',
            'service_technique' => 'Chauffage',
            'id_gravite' => '3',
            'id_incident' => '1',
        ];

        $this->mockStmt->method('execute')->willReturn(true);
        $this->donnees->method('sendJSON')->willReturn(true);

        $this->donnees->putIncident(
            $data['resume'],
            $data['description'],
            $data['service_technique'],
            $data['id_gravite'],
            $data['id_incident']
        );

        $this->expectOutputString(json_encode(["Statut" => "OK"], JSON_PRETTY_PRINT));
    }

    public function testPostIncidentWithMissingData()
    {
        $data = [
            'RESUME' => 'Problème technique',
            'SERVICE_TECHNIQUE' => '',
            'ID_GRAVITE' => '2',
            'ID_RESERVATION' => '5'
        ];

        // Simuler l'erreur de données incomplètes
        $this->donnees->method('sendError')->willReturn(true);

        $this->donnees->postIncident($data);

        $this->expectOutputString(json_encode(['Erreur' => 'Données incomplètes'], JSON_PRETTY_PRINT));
    }

}
?>
