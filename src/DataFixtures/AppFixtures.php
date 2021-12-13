<?php

namespace App\DataFixtures;

use App\Entity\Account;
use App\Entity\Category;
use App\Entity\CategoryGroup;
use App\Entity\CurrentBalance;
use App\Entity\Settings;
use App\Entity\SplitTransaction;
use App\Entity\SubAccount;
use App\Entity\Transaction;
use App\Lib\SettingsHandler;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * @var ObjectManager
     */
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $account = new Account();
        $account->setName('This is a Testaccount');
        $account->setAccountHolder('Test');
        $account->setIban('test');
        $account->setBic('test');
        $account->setBankCode('test');
        $account->setUrl('test');

        $this->manager->persist($account);
        $this->manager->flush();

        $subaccount = new SubAccount();
        $subaccount->setAccount($account);
        $subaccount->setIsEnabled(true);
        $subaccount->setIban('test');
        $subaccount->setBic('test');
        $subaccount->setAccountNumber('test');
        $subaccount->setBlz('test');
        $subaccount->setDescription('This is a Testdescription');
        $this->manager->persist($subaccount);
        $this->manager->flush();

        $settings = new Settings();
        $settings->setName(SettingsHandler::SETTING_MAIN_ACCOUNT);
        $settings->setValue((string) $subaccount->getId());
        $this->manager->persist($settings);
        $this->manager->flush();
        unset($settings);

        $balance = new CurrentBalance();
        $balance->setSubAccount($subaccount);
        $balance->setBalance(500000);
        $this->manager->persist($balance);
        $this->manager->flush();

        $cG = $this->createCategoryGroup('group-1');

        $c = $this->createCategory('category-1-1', $cG);

        $settings = new Settings();
        $settings->setName(SettingsHandler::SETTING_LUXURY_CATEGORIES);
        $settings->setValue((string) $c->getId());
        $this->manager->persist($settings);
        $this->manager->flush();
        unset($settings);

        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-08-02'), 15.99, $c, $subaccount);
        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-08-25'), 30.65, $c, $subaccount);
        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-07-31'), 13.19, $c, $subaccount);
        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-09-01'), 62.59, $c, $subaccount);

        $t = $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-06-16'), 110, $c, $subaccount);
        $this->createSplitTransaction($t, \DateTime::createFromFormat('Y-m-d', '2020-06-10'), 10, $c);
        $this->createSplitTransaction($t, \DateTime::createFromFormat('Y-m-d', '2020-06-04'), 26, $c);
        $this->createSplitTransaction($t, \DateTime::createFromFormat('Y-m-d', '2020-06-09'), 54, $c);
        $this->createSplitTransaction($t, \DateTime::createFromFormat('Y-m-d', '2020-05-28'), 13, $c);
        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-06-11'), 67.59, $c, $subaccount);


        $c = $this->createCategory('category-1-2', $cG);
        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-08-02'), 36.4, $c, $subaccount);
        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-08-25'), 41.25, $c, $subaccount);
        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-07-31'), 9.14, $c, $subaccount);
        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-09-01'), 25.96, $c, $subaccount);
        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-06-21'), 384.29, $c, $subaccount);

        $cG = $this->createCategoryGroup('group-2');

        $c = $this->createCategory('category-2-1', $cG);

        $settings = new Settings();
        $settings->setName(SettingsHandler::SETTING_SAVINGS_CATEGORIES);
        $settings->setValue((string) $c->getId());
        $this->manager->persist($settings);
        $this->manager->flush();
        unset($settings);

        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-08-05'), 41.37, $c, $subaccount);
        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-08-12'), 55.9, $c, $subaccount);
        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-07-30'), 46.91, $c, $subaccount);
        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-09-02'), 41.17, $c, $subaccount);

        $c = $this->createCategory('category-2-2', $cG);
        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-08-05'), 6.1, $c, $subaccount);
        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-08-12'), 28.33, $c, $subaccount);
        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-07-30'), 25.19, $c, $subaccount);
        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-09-02'), 54.09, $c, $subaccount);

        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-08-11'), 31.8, null, $subaccount);

        $c = $this->createCategory('category-3', null, true);

        $settings = new Settings();
        $settings->setName(SettingsHandler::SETTING_SALARY_CATEGORIES);
        $settings->setValue((string) $c->getId());
        $this->manager->persist($settings);
        $this->manager->flush();
        unset($settings);

        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-08-08'), 7.02, $c, $subaccount);
        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-08-23'), 4.96, $c, $subaccount);
        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-07-18'), 9.44, $c, $subaccount);
        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-09-04'), 8.25, $c, $subaccount);

        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-09-15'), 120, null, $subaccount, 'credit');

        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-06-3'), 12.53, null, $subaccount);
        $this->createTransaction(\DateTime::createFromFormat('Y-m-d', '2020-06-13'), 247.28, null, $subaccount, 'debit', 'MUENCHEN HOTEL XY\\MUENCHEN\\DE', 'test1234');
    }

    /**
     * @param string $name
     *
     * @return CategoryGroup
     */
    private function createCategoryGroup(string $name)
    {
        $cG = new CategoryGroup();
        $cG->setName($name);
        $this->manager->persist($cG);
        $this->manager->flush();

        return $cG;
    }

    /**
     * @param string             $name
     * @param CategoryGroup|null $cG
     *
     * @return Category
     */
    private function createCategory(string $name, CategoryGroup $cG = null, bool $treeIgnore = false)
    {
        $c = new Category();
        $c->setName($name);
        $c->setCategoryGroup($cG);
        $c->setTreeIgnore($treeIgnore);
        $this->manager->persist($c);
        $this->manager->flush();

        return $c;
    }

    /**
     * @param \DateTime     $d
     * @param float         $amount
     * @param Category|null $c
     * @param SubAccount    $subAccount
     * @param string        $creditDebit
     * @param string|null   $name
     * @param string|null   $description
     *
     * @return Transaction
     */
    private function createTransaction(\DateTime $d, float $amount, Category $c = null, SubAccount $subAccount, string $creditDebit = 'debit', string $name = null, string $description = null)
    {
        $t = new Transaction();
        $t->setCategory($c);
        $t->setBookingDate($d);
        $t->setValutaDate($d);
        $t->setAmount(intval($amount*100));
        $t->setCreditDebit($creditDebit);
        $t->setName($name);
        $t->setDescription($description);
        $t->setBookingText('LASTSCHRIFT');
        $t->setAccountNumber('DE34302201900024938816');
        $t->setChecksum(md5($d->format('Y-m-d').$d->format('Y-m-d').$t->getAmount().time()));
        $t->setSubAccount($subAccount);

        $this->manager->persist($t);
        $this->manager->flush();

        return $t;
    }

    /**
     * @param Transaction   $t
     * @param \DateTime     $d
     * @param float         $amount
     * @param Category|null $c
     */
    private function createSplitTransaction(Transaction $t, \DateTime $d, float $amount, Category $c = null)
    {
        $s = new SplitTransaction();
        $s->setTransaction($t);
        $s->setValutaDate($d);
        $s->setAmount(intval($amount*100));
        $s->setCategory($c);
        $s->setDescription('Test');

        $this->manager->persist($s);
        $this->manager->flush();
    }
}
