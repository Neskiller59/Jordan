<?php
namespace App\DataFixtures;
use App\Entity\Model;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
class AppFixtures extends Fixture
{
 public function load(ObjectManager $manager): void
 {
 for ($i=0; $i < 10; $i++) {
 $model = new Model();
 $model->setImage('images/produit' . $i . '.jpg');
 $model->setName('Model ' . $i);
 $model->setPrix(sprintf('%.2f', $i * 10 + 0.99));
 $manager->persist($model);
 }
 $manager->flush();
 }
}