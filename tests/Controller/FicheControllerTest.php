<?php

namespace App\Test\Controller;

use App\Entity\Fiche;
use App\Repository\FicheRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FicheControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private FicheRepository $repository;
    private string $path = '/fiche/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Fiche::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Fiche index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'fiche[Id_Patient]' => 'Testing',
            'fiche[age]' => 'Testing',
            'fiche[RendezVous]' => 'Testing',
        ]);

        self::assertResponseRedirects('/fiche/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Fiche();
        $fixture->setId_Patient('My Title');
        $fixture->setAge('My Title');
        $fixture->setRendezVous('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Fiche');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Fiche();
        $fixture->setId_Patient('My Title');
        $fixture->setAge('My Title');
        $fixture->setRendezVous('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'fiche[Id_Patient]' => 'Something New',
            'fiche[age]' => 'Something New',
            'fiche[RendezVous]' => 'Something New',
        ]);

        self::assertResponseRedirects('/fiche/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getId_Patient());
        self::assertSame('Something New', $fixture[0]->getAge());
        self::assertSame('Something New', $fixture[0]->getRendezVous());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Fiche();
        $fixture->setId_Patient('My Title');
        $fixture->setAge('My Title');
        $fixture->setRendezVous('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/fiche/');
    }
}
