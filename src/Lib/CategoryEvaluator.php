<?php

namespace App\Lib;

use App\Entity\CategoryAssignmentRule;
use App\Repository\CategoryAssignmentRuleRepository;
use Fhp\Model\StatementOfAccount\Transaction;
use Psr\Log\LoggerInterface;

class CategoryEvaluator
{
    private array $rules;
    private LoggerInterface $logger;

    /**
     * CategoryEvaluator constructor.
     *
     * @param CategoryAssignmentRuleRepository $repository
     * @param LoggerInterface                  $logger
     */
    public function __construct(CategoryAssignmentRuleRepository $repository, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->loadRules($repository);
    }

    /**
     * @param Transaction $transaction
     *
     * @return int|null
     */
    public function evaluate(Transaction $transaction): ?int
    {
        foreach ($this->rules as $rule) {
            if ($rule['field'] === CategoryAssignmentRule::TRANSACTION_FIELD_NAME) {
                $value = $transaction->getName();
            } elseif ($rule['field'] === CategoryAssignmentRule::TRANSACTION_FIELD_DESCRIPTION) {
                $value = $transaction->getDescription1();
            } else {
                $this->logger->error(sprintf('CategoryEvaluator: unknown transaction field: %d', $rule['field']));
                continue;
            }

            $comparative = $rule['comparative'] ?? '';
            if (strlen($comparative) === 0) {
                $this->logger->error('CategoryEvaluator: empty comparison value!');
                continue;
            }

            $category = intval($rule['category'] ?? 0);
            if (0 === $category) {
                $this->logger->error('CategoryEvaluator: missing category id!');
                continue;
            }

            if ($rule['type'] === CategoryAssignmentRule::TYPE_SIMPLE) {
                if ($comparative === $value) {
                    return $category;
                }
            } elseif ($rule['type'] === CategoryAssignmentRule::TYPE_REGEX) {
                if (1 === preg_match($comparative, $value)) {
                    return $category;
                }
            } else {
                $this->logger->error(sprintf('CategoryEvaluator: unknown comparison type: %d', $rule['type']));
            }
        }

        return null;
    }

    /**
     * @param CategoryAssignmentRuleRepository $repository
     */
    private function loadRules(CategoryAssignmentRuleRepository $repository)
    {
        $this->rules = array_map(function ($item) {
            return [
                'type' => $item->getType(),
                'comparative' => $item->getRule(),
                'field' => $item->getTransactionField(),
                'category' => $item->getCategory()->getId(),
            ];
        }, $repository->findAll());
    }
}
