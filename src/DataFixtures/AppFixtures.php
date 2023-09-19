<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(protected SluggerInterface $slugger, protected UserPasswordHasherInterface $encoder)
    {
        
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        
        $admin = new User();
        $hashPassword = $this->encoder->hashPassword($admin, "password");

        $admin->setEmail("admin@gmail.com")
            ->setFullname("Admin")
            ->setPassword($hashPassword)
            ->setRoles(['ROLE_ADMIN']);
        
        $manager->persist($admin);
        $users = [];
        for($u=0; $u<5; $u++){
            $user = new User();
            $hashPassword = $this->encoder->hashPassword($user, "password");
            $user->setEmail("user$u@gmail.com")
                ->setFullName($faker->name)
                ->setPassword($hashPassword);

            $users[] = $user;

            $manager->persist($user);
        }

        $products = [];
        for($c=0; $c < 3; $c++){
            $category = new Category;
            $category->setName($faker->department)
                ;//->setSlug(strtolower($this->slugger->slug($category->getName())));
                $manager->persist($category);
        
            for($p=0; $p < mt_rand(15,20); $p++){
                $product = new Product;
                $product->setName($faker->productName)
                    ->setPrice($faker->price(4000,20000))
                    //->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setCategory($category)
                    ->setShortDescription($faker->paragraph())
                    ->setMainPicture($faker->imageUrl(400,400, true));

                $products[] = $product;

                $manager->persist($product);
            }
        }

        for($p = 0; $p < mt_rand(20,40); $p++){
            $purchase = new Purchase();
            $purchase->setFullName($faker->name);
            $purchase->setAdress($faker->streetAddress());
            $purchase->setCity($faker->city);
            $purchase->setPostalCode($faker->postCode);

            $selectedProducts = $faker->randomElements($products, mt_rand(3,5));
            
            foreach($selectedProducts as $product){
                $purchaseItem = new PurchaseItem();
                $purchaseItem->setProduct($product)
                            ->setQuantity(mt_rand(1,3))
                            ->setProductName($product->getName())
                            ->setProductPrice($product->getPrice())
                            ->setTotal($product->getPrice() * $purchaseItem->getQuantity())
                            ->setPurchase($purchase);
                $manager->persist($purchaseItem);
            }
            
            if($faker->boolean(90)){
                $purchase->setStatus(Purchase::STATUS_PAID);
            }
            $purchase->setUser($faker->randomElement($users));
            $purchase->setTotal(mt_rand(2000,3000));
            $purchase->setPurchasedAt($faker->dateTimeBetween('-6 months'));

            $manager->persist($purchase);
        }
        $manager->flush();
    }
}
