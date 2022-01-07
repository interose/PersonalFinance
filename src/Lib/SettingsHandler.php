<?php

namespace App\Lib;

use App\Entity\Category;
use App\Entity\Settings;
use App\Entity\SubAccount;
use App\Repository\CategoryRepository;
use App\Repository\SettingsRepository;
use App\Repository\SubAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SettingsHandler
{
    const SETTING_MAIN_ACCOUNT = 'main_account';
    const SETTING_SALARY_CATEGORIES = 'salary_categories';
    const SETTING_SAVINGS_CATEGORIES = 'savings_categories';
    const SETTING_LUXURY_CATEGORIES = 'luxury_categories';

    const AVAILABLE_SETTINGS = [
        self::SETTING_MAIN_ACCOUNT,
        self::SETTING_SALARY_CATEGORIES,
        self::SETTING_SAVINGS_CATEGORIES,
        self::SETTING_LUXURY_CATEGORIES,
    ];

    const CATEGORY_SETTINGS = [
        self::SETTING_SALARY_CATEGORIES,
        self::SETTING_SAVINGS_CATEGORIES,
        self::SETTING_LUXURY_CATEGORIES,
    ];

    private SettingsRepository $settingsRepository;
    private SubAccountRepository $subAccountRepository;
    private CategoryRepository $categoryRepository;
    private EntityManagerInterface $em;
    private FormFactoryInterface $formFactory;
    private TranslatorInterface $translator;

    /**
     * @param SettingsRepository     $settingsRepository
     * @param SubAccountRepository   $subAccountRepository
     * @param CategoryRepository     $categoryRepository
     * @param EntityManagerInterface $em
     * @param FormFactoryInterface   $formFactory
     * @param TranslatorInterface    $translator
     */
    public function __construct(SettingsRepository $settingsRepository, SubAccountRepository $subAccountRepository, CategoryRepository $categoryRepository, EntityManagerInterface $em, FormFactoryInterface $formFactory, TranslatorInterface $translator)
    {
        $this->settingsRepository = $settingsRepository;
        $this->subAccountRepository = $subAccountRepository;
        $this->categoryRepository = $categoryRepository;
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->translator = $translator;
    }

    /**
     * @return FormInterface
     */
    public function getForm(): FormInterface
    {
        $data = $this->getFormData();

        $formBuilder = $this->formFactory->createBuilder(FormType::class, $data);

        $formBuilder->add(self::SETTING_MAIN_ACCOUNT, EntityType::class, [
                'label' => $this->translator->trans(self::SETTING_MAIN_ACCOUNT, [], 'messages+intl-icu'),
                'required' => true,
                'class' => SubAccount::class,
                'placeholder' => 'Please select an account',
                'choice_label' => function (SubAccount $subAccount) {
                    return $subAccount->getAccount()->getName().' - '.$subAccount->getAccountNumber();
                },
                'empty_data' => [],
                'attr' => ['class' => 'select2'],
        ]);

        foreach (self::CATEGORY_SETTINGS as $categorySetting) {
            $formBuilder->add($categorySetting, EntityType::class, [
                'label' => $this->translator->trans($categorySetting, [], 'messages+intl-icu'),
                'required' => true,
                'class' => Category::class,
                'choice_label' => function (Category $category) {
                    return $category->getName();
                },
                'multiple' => true,
                'group_by' => 'categoryGroup',
                'attr' => ['class' => 'select2'],
            ]);
        }

        return $formBuilder->getForm();
    }

    /**
     * @param array $data
     */
    public function saveFormData(array $data)
    {
        $settingsData = $data[self::SETTING_MAIN_ACCOUNT] ?? null;
        if (!is_null($settingsData) && is_a($settingsData, SubAccount::class)) {
            $settingsRecord = $this->settingsRepository->findOneBy(['name' => self::SETTING_MAIN_ACCOUNT]);
            if (!$settingsRecord) {
                $settingsRecord = new Settings();
                $settingsRecord->setName(self::SETTING_MAIN_ACCOUNT);
            }

            $settingsRecord->setValue((string) $settingsData->getId());
            $this->em->persist($settingsRecord);
            $this->em->flush();
        }


        foreach (self::CATEGORY_SETTINGS as $categorySetting) {
            $settingsData = $data[$categorySetting] ?? null;

            if (!is_null($settingsData)) {
                $settingsRecord = $this->settingsRepository->findOneBy(['name' => $categorySetting]);
                if (!$settingsRecord) {
                    $settingsRecord = new Settings();
                    $settingsRecord->setName($categorySetting);
                }

                $value = [];
                foreach ($settingsData->toArray() as $category) {
                    $value[] = $category->getId();
                }
                $settingsRecord->setValue(implode(',', $value));
                $this->em->persist($settingsRecord);
                $this->em->flush();
            }
        }
    }

    /**
     * @return int
     *
     * @throws \Exception
     */
    public function getMainAccount(): int
    {
        $settingsRecord = $this->settingsRepository->findOneBy(['name' => self::SETTING_MAIN_ACCOUNT]);

        if (is_null($settingsRecord)) {
            throw new \Exception('No main account configured!');
        }

        return (int) $settingsRecord->getValue();
    }

    /**
     * @return array
     */
    private function getFormData(): array
    {
        $data = [];

        //pre-assign all settings with null
        array_map(function ($setting) use ($data) {
            $data[$setting] = null;
        }, self::AVAILABLE_SETTINGS);

        $settingsRecord = $this->settingsRepository->findOneBy(['name' => self::SETTING_MAIN_ACCOUNT]);
        if ($settingsRecord) {
            $data[self::SETTING_MAIN_ACCOUNT] = $this->subAccountRepository->findOneBy(['id' => $settingsRecord->getValue()]);
        }

        foreach (self::CATEGORY_SETTINGS as $categorySetting) {
            $settingsRecord = $this->settingsRepository->findOneBy(['name' => $categorySetting]);
            if ($settingsRecord) {
                $data[$categorySetting] = new ArrayCollection($this->categoryRepository->getWhereIn(explode(',', $settingsRecord->getValue())));
            }
        }

        return $data;
    }
}
