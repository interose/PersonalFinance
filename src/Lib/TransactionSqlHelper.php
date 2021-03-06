<?php

namespace App\Lib;

/**
 * Class TransactionSqlHelper.
 */
class TransactionSqlHelper
{
    /**
     * @return string
     */
    public function getSqlForYearChartByCategoryGroup(): string
    {
        $condition = 'c.category_group_id = :category_group_id';

        return $this->getSqlForYearChart($condition);
    }

    /**
     * @return string
     */
    public function getSqlForYearChartByCategory(): string
    {
        $condition = 't.category_id = :category_id';

        return $this->getSqlForYearChart($condition);
    }

    /**
     * @return string
     */
    public function getSqlForMonthlyRemainingDashboard(): string
    {
        return <<<SQL
SELECT SUM(src.amount) AS amount, src.month_number, src.month_name
FROM (
    SELECT
           t.id,
           CASE WHEN t.credit_debit = 'credit' THEN t.amount WHEN t.credit_debit = 'debit' THEN t.amount * -1 ELSE t.amount END AS amount,
           DATE_FORMAT(t.valuta_date, '%Y-%m') AS month_number,
           DATE_FORMAT(t.valuta_date, '%b') AS month_name,
           DATE_FORMAT(t.valuta_date, '%Y') AS year_number
    FROM transaction t 
    LEFT JOIN split_transaction st on t.id = st.transaction_id
    LEFT JOIN category c ON t.category_id = c.id
    WHERE 
          st.transaction_id IS NULL AND 
          t.sub_account_id = :subaccountid AND
          (c.dashboard_ignore IS NULL OR c.dashboard_ignore = 0 ) AND
          t.valuta_date >= :start AND
          t.valuta_date <= :stop

    UNION 
    
    SELECT 
           st.id,
           CASE WHEN t.credit_debit = 'credit' THEN st.amount WHEN t.credit_debit = 'debit' THEN st.amount * -1 ELSE st.amount END AS amount, 
           DATE_FORMAT(st.valuta_date, '%Y-%m') AS month_number, 
           DATE_FORMAT(st.valuta_date, '%b') AS month_name, 
           DATE_FORMAT(st.valuta_date, '%Y') AS year_number  
    FROM split_transaction st
    LEFT JOIN transaction t ON st.transaction_id = t.id 
    LEFT JOIN category c ON st.category_id = c.id 
    WHERE
          t.sub_account_id = :subaccountid AND
          (c.dashboard_ignore IS NULL OR c.dashboard_ignore = 0 ) AND
          st.valuta_date >= :start AND
          st.valuta_date <= :stop          
) AS src
GROUP BY src.month_number, src.month_name
ORDER BY src.month_number ASC
SQL;
    }

    /**
     * @return string
     */
    public function getSqlForForTreeView(): string
    {
        return <<<SQL
SELECT ROUND(SUM(src.amount) / 100, 2) AS amount, src.month_number, src.month_name, src.category, src.category_group
FROM (
    SELECT
           t.id,
           CASE WHEN t.credit_debit = 'credit' THEN t.amount WHEN t.credit_debit = 'debit' THEN t.amount * -1 ELSE t.amount END AS amount,
           DATE_FORMAT(t.valuta_date, '%Y-%m') AS month_number,
           DATE_FORMAT(t.valuta_date, '%b') AS month_name,
           DATE_FORMAT(t.valuta_date, '%Y') AS year_number,
           c.name AS category, 
           cg.name AS category_group
    FROM transaction t 
    LEFT JOIN split_transaction st on t.id = st.transaction_id
    LEFT JOIN category c ON t.category_id = c.id
    LEFT JOIN category_group cg ON c.category_group_id = cg.id
    WHERE 
          st.transaction_id IS NULL AND 
          t.sub_account_id = :subaccountid AND
          (c.tree_ignore IS NULL OR c.tree_ignore = 0 ) AND
          t.category_id IS NOT NULL AND
          t.valuta_date >= :start AND
          t.valuta_date <= :stop

    UNION 
    
    SELECT 
           st.id,
           CASE WHEN t.credit_debit = 'credit' THEN st.amount WHEN t.credit_debit = 'debit' THEN st.amount * -1 ELSE st.amount END AS amount, 
           DATE_FORMAT(st.valuta_date, '%Y-%m') AS month_number, 
           DATE_FORMAT(st.valuta_date, '%b') AS month_name, 
           DATE_FORMAT(st.valuta_date, '%Y') AS year_number,
           c.name AS category, 
           cg.name AS category_group
    FROM split_transaction st
    LEFT JOIN transaction t ON st.transaction_id = t.id 
    LEFT JOIN category c ON st.category_id = c.id 
    LEFT JOIN category_group cg ON c.category_group_id = cg.id
    WHERE
          t.sub_account_id = :subaccountid AND
          (c.tree_ignore IS NULL OR c.tree_ignore = 0 ) AND
          st.category_id IS NOT NULL AND
          st.valuta_date >= :start AND
          st.valuta_date <= :stop          
) AS src
GROUP BY src.month_number, src.month_name, src.category, src.category_group
ORDER BY src.month_number ASC, src.category_group ASC, src.category ASC
SQL;
    }

    /**
     * @return string
     */
    public function getSqlForForDashboardMonthOverview(): string
    {
        return <<<SQL
SELECT ROUND(SUM(src.amount) / 100, 2) AS amount, src.category, src.category_group, src.category_color
FROM (
    SELECT
           t.id,
           CASE WHEN t.credit_debit = 'credit' THEN t.amount WHEN t.credit_debit = 'debit' THEN t.amount * -1 ELSE t.amount END AS amount,
           c.name AS category, 
           cg.name AS category_group,
           cg.color AS category_color
    FROM transaction t 
    LEFT JOIN split_transaction st on t.id = st.transaction_id
    LEFT JOIN category c ON t.category_id = c.id
    LEFT JOIN category_group cg ON c.category_group_id = cg.id
    WHERE 
          st.transaction_id IS NULL AND 
          t.sub_account_id = :subaccountid AND
          (c.tree_ignore IS NULL OR c.tree_ignore = 0 ) AND
          t.category_id IS NOT NULL AND
          t.valuta_date >= :start AND
          t.valuta_date <= :stop

    UNION 
    
    SELECT 
           st.id,
           CASE WHEN t.credit_debit = 'credit' THEN st.amount WHEN t.credit_debit = 'debit' THEN st.amount * -1 ELSE st.amount END AS amount, 
           c.name AS category, 
           cg.name AS category_group,
           cg.color AS category_color
    FROM split_transaction st
    LEFT JOIN transaction t ON st.transaction_id = t.id 
    LEFT JOIN category c ON st.category_id = c.id 
    LEFT JOIN category_group cg ON c.category_group_id = cg.id
    WHERE
          t.sub_account_id = :subaccountid AND
          (c.tree_ignore IS NULL OR c.tree_ignore = 0 ) AND
          st.category_id IS NOT NULL AND
          st.valuta_date >= :start AND
          st.valuta_date <= :stop          
) AS src
GROUP BY src.category, src.category_group, src.category_color
ORDER BY src.category_group ASC, src.category ASC
SQL;
    }

    /**
     * @param string $condition
     *
     * @return string
     */
    private function getSqlForYearChart(string $condition): string
    {
        return <<<SQL
SELECT SUM(src.amount) AS amount, src.year_number
FROM (
    SELECT
           t.id,
           CASE WHEN t.credit_debit = 'credit' THEN t.amount WHEN t.credit_debit = 'debit' THEN t.amount * -1 ELSE t.amount END AS amount,
           DATE_FORMAT(t.valuta_date, '%Y-%m') AS month_number,
           DATE_FORMAT(t.valuta_date, '%b') AS month_name,
           DATE_FORMAT(t.valuta_date, '%Y') AS year_number
    FROM transaction t 
    LEFT JOIN split_transaction st on t.id = st.transaction_id 
    LEFT JOIN category c ON t.category_id = c.id
    WHERE 
          st.transaction_id IS NULL AND 
          t.sub_account_id = :subaccountid AND
          $condition

    UNION 

    SELECT 
           st.id,
           CASE WHEN t.credit_debit = 'credit' THEN st.amount WHEN t.credit_debit = 'debit' THEN st.amount * -1 ELSE st.amount END AS amount, 
           DATE_FORMAT(st.valuta_date, '%Y-%m') AS month_number, 
           DATE_FORMAT(st.valuta_date, '%b') AS month_name, 
           DATE_FORMAT(st.valuta_date, '%Y') AS year_number  
    FROM split_transaction st
    LEFT JOIN transaction t ON st.transaction_id = t.id 
    LEFT JOIN category c ON t.category_id = c.id
    WHERE
          t.sub_account_id = :subaccountid AND 
          $condition
) AS src
GROUP BY src.year_number
ORDER BY src.year_number ASC
SQL;
    }
}
