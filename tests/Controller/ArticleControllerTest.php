<?php

namespace App\Test\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArticleControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/article/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Article::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Article index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'article[category_id]' => 'Testing',
            'article[subcategory_id]' => 'Testing',
            'article[title]' => 'Testing',
            'article[summary]' => 'Testing',
            'article[content]' => 'Testing',
            'article[active]' => 'Testing',
            'article[publicationDate]' => 'Testing',
            'article[users]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Article();
        $fixture->setCategory_id('My Title');
        $fixture->setSubcategory_id('My Title');
        $fixture->setTitle('My Title');
        $fixture->setSummary('My Title');
        $fixture->setContent('My Title');
        $fixture->setActive('My Title');
        $fixture->setPublicationDate('My Title');
        $fixture->setUsers('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Article');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Article();
        $fixture->setCategory_id('Value');
        $fixture->setSubcategory_id('Value');
        $fixture->setTitle('Value');
        $fixture->setSummary('Value');
        $fixture->setContent('Value');
        $fixture->setActive('Value');
        $fixture->setPublicationDate('Value');
        $fixture->setUsers('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'article[category_id]' => 'Something New',
            'article[subcategory_id]' => 'Something New',
            'article[title]' => 'Something New',
            'article[summary]' => 'Something New',
            'article[content]' => 'Something New',
            'article[active]' => 'Something New',
            'article[publicationDate]' => 'Something New',
            'article[users]' => 'Something New',
        ]);

        self::assertResponseRedirects('/article/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getCategory_id());
        self::assertSame('Something New', $fixture[0]->getSubcategory_id());
        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getSummary());
        self::assertSame('Something New', $fixture[0]->getContent());
        self::assertSame('Something New', $fixture[0]->getActive());
        self::assertSame('Something New', $fixture[0]->getPublicationDate());
        self::assertSame('Something New', $fixture[0]->getUsers());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Article();
        $fixture->setCategory_id('Value');
        $fixture->setSubcategory_id('Value');
        $fixture->setTitle('Value');
        $fixture->setSummary('Value');
        $fixture->setContent('Value');
        $fixture->setActive('Value');
        $fixture->setPublicationDate('Value');
        $fixture->setUsers('Value');

        $this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/article/');
        self::assertSame(0, $this->repository->count([]));
    }
}
