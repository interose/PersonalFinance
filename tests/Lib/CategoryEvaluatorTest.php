<?php

namespace App\Tests\Lib;

use App\Entity\Category;
use App\Entity\CategoryAssignmentRule;
use App\Lib\CategoryEvaluator;
use App\Repository\CategoryAssignmentRuleRepository;
use Fhp\Model\StatementOfAccount\Transaction;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CategoryEvaluatorTest extends TestCase
{
    private Category $category;
    private CategoryAssignmentRule $rule;

    protected function setUp(): void
    {
        $this->category = new Category();
        $this->category->setName('Testkategorie 1');

        $this->rule = new CategoryAssignmentRule();
        $this->rule->setCategory($this->category);
        $this->rule->setRule('/SomeSimpleBusiness/');
        $this->rule->setTransactionField(CategoryAssignmentRule::TRANSACTION_FIELD_NAME);
        $this->rule->setType(CategoryAssignmentRule::TYPE_REGEX);
    }

    public function testRegExFieldName()
    {
        // This is a demo transaction
        $transaction = new Transaction();
        $transaction->setName('PPPP . SomeSimpleBusiness, Your purchase by SomeSimpleBusinessAB');

        // Now, mock the repository so it returns the mock of the employee
        $ruleRepository = $this->createMock(CategoryAssignmentRuleRepository::class);
        $ruleRepository->expects($this->any())
            ->method('findAll')
            ->willReturn([$this->rule]);

        // Mock the logger interface
        $logger = $this->createMock(LoggerInterface::class);

        $evaluator = new CategoryEvaluator($ruleRepository, $logger);
        $evaluatedCategory = $evaluator->evaluate($transaction);

        $this->assertEquals($this->category, $evaluatedCategory);
    }

    public function testRegExFieldDescription()
    {
        // This is a demo transaction
        $transaction = new Transaction();
        $transaction->setName('PPPP . ');
        $transaction->setDescription1('PPPP . SomeSimpleBusiness, Your purchase by SomeSimpleBusinessAB');

        $this->rule->setTransactionField(CategoryAssignmentRule::TRANSACTION_FIELD_DESCRIPTION);

        // Now, mock the repository so it returns the mock of the employee
        $ruleRepository = $this->createMock(CategoryAssignmentRuleRepository::class);
        $ruleRepository->expects($this->any())
            ->method('findAll')
            ->willReturn([$this->rule]);

        // Mock the logger interface
        $logger = $this->createMock(LoggerInterface::class);

        $evaluator = new CategoryEvaluator($ruleRepository, $logger);
        $evaluatedCategory = $evaluator->evaluate($transaction);

        $this->assertEquals($this->category, $evaluatedCategory);
    }

    public function testSimpleFieldName()
    {
        // This is a demo transaction
        $transaction = new Transaction();
        $transaction->setName('Central Perk');

        $this->rule->setRule('Central Perk');
        $this->rule->setType(CategoryAssignmentRule::TYPE_SIMPLE);

        // Now, mock the repository so it returns the mock of the employee
        $ruleRepository = $this->createMock(CategoryAssignmentRuleRepository::class);
        $ruleRepository->expects($this->any())
            ->method('findAll')
            ->willReturn([$this->rule]);

        // Mock the logger interface
        $logger = $this->createMock(LoggerInterface::class);

        $evaluator = new CategoryEvaluator($ruleRepository, $logger);
        $evaluatedCategory = $evaluator->evaluate($transaction);

        $this->assertEquals($this->category, $evaluatedCategory);
    }

    public function testNotFound()
    {
        // This is a demo transaction
        $transaction = new Transaction();
        $transaction->setName('Central Perk');

        // Now, mock the repository so it returns the mock of the employee
        $ruleRepository = $this->createMock(CategoryAssignmentRuleRepository::class);
        $ruleRepository->expects($this->any())
            ->method('findAll')
            ->willReturn([$this->rule]);

        // Mock the logger interface
        $logger = $this->createMock(LoggerInterface::class);

        $evaluator = new CategoryEvaluator($ruleRepository, $logger);
        $evaluatedCategory = $evaluator->evaluate($transaction);

        $this->assertEquals(null, $evaluatedCategory);
    }

    public function testLoggerUnknownTransactionField()
    {
        // This is a demo transaction
        $transaction = new Transaction();
        $transaction->setName('Central Perk');

        // Modify the CategoryAssignmentRule so that an error is logged
        $this->rule->setTransactionField(9);

        // Now, mock the repository so it returns the mock of the employee
        $ruleRepository = $this->createMock(CategoryAssignmentRuleRepository::class);
        $ruleRepository->expects($this->any())
            ->method('findAll')
            ->willReturn([$this->rule]);

        // Mock the logger interface
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error')
            ->with($this->stringStartsWith('CategoryEvaluator: unknown transaction field:'));

        $evaluator = new CategoryEvaluator($ruleRepository, $logger);
        $evaluator->evaluate($transaction);
    }

    public function testLoggerEmptyComparison()
    {
        // This is a demo transaction
        $transaction = new Transaction();
        $transaction->setName('Central Perk');

        // Modify the CategoryAssignmentRule so that an error is logged
        $this->rule->setRule('');

        // Now, mock the repository so it returns the mock of the employee
        $ruleRepository = $this->createMock(CategoryAssignmentRuleRepository::class);
        $ruleRepository->expects($this->any())
            ->method('findAll')
            ->willReturn([$this->rule]);

        // Mock the logger interface
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error')
            ->with($this->equalTo('CategoryEvaluator: empty comparison value!'));

        $evaluator = new CategoryEvaluator($ruleRepository, $logger);
        $evaluator->evaluate($transaction);
    }

    public function testLoggerMissingCategory()
    {
        // This is a demo transaction
        $transaction = new Transaction();
        $transaction->setName('Central Perk');

        // Modify the CategoryAssignmentRule so that an error is logged
        $this->rule->setCategory(null);

        // Now, mock the repository so it returns the mock of the employee
        $ruleRepository = $this->createMock(CategoryAssignmentRuleRepository::class);
        $ruleRepository->expects($this->any())
            ->method('findAll')
            ->willReturn([$this->rule]);

        // Mock the logger interface
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error')
            ->with($this->equalTo('CategoryEvaluator: missing category!'));

        $evaluator = new CategoryEvaluator($ruleRepository, $logger);
        $evaluator->evaluate($transaction);
    }

    public function testLoggerUnknownComparisonType()
    {
        // This is a demo transaction
        $transaction = new Transaction();
        $transaction->setName('Central Perk');

        // Modify the CategoryAssignmentRule so that an error is logged
        $this->rule->setType(9);

        // Now, mock the repository so it returns the mock of the employee
        $ruleRepository = $this->createMock(CategoryAssignmentRuleRepository::class);
        $ruleRepository->expects($this->any())
            ->method('findAll')
            ->willReturn([$this->rule]);

        // Mock the logger interface
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error')
            ->with($this->stringStartsWith('CategoryEvaluator: unknown comparison type:'));

        $evaluator = new CategoryEvaluator($ruleRepository, $logger);
        $evaluator->evaluate($transaction);
    }
}