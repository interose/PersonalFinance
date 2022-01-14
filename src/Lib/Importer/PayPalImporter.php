<?php

namespace App\Lib\Importer;

use App\Entity\PayPalTransaction;
use App\Repository\PayPalTransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\Translation\TranslatorInterface;

class PayPalImporter
{
    private PayPalTransactionRepository $repository;
    private EntityManagerInterface $em;
    private TranslatorInterface $translator;
    private array $columnConfig;

    public function __construct(PayPalTransactionRepository $repository, EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $this->repository = $repository;
        $this->em = $em;
        $this->translator = $translator;

        $this->columnConfig = PayPalCsvColumnConfig::$columnConfig;
    }

    /**
     * @param UploadedFile $uploadedFile
     *
     * @throws \Exception
     */
    public function import(UploadedFile $uploadedFile): string
    {
        $mime = $uploadedFile->getMimeType();
        if ($mime !== 'application/csv' && $mime !== 'text/csv') {
            throw new \Exception($this->translator->trans('Only csv files are supported!'));
        }

        $handle = fopen($uploadedFile->getRealPath(), 'r');

        //Skip BOM if present
        if (fgets($handle, 4) !== "\xef\xbb\xbf") {
            rewind($handle);
        }

        $firstLine = fgetcsv($handle);
        $this->getColumnIndices($firstLine);
        $this->checkIfMandatoryColumnsAreAvailable();

        try {
            $iSkipped = $iImported = 0;

            while (($line = fgetcsv($handle)) !== false) {
                if ($this->transactionAlreadyImported($line)) {
                    $iSkipped++;
                    continue;
                }

                $iImported++;
                $this->importTransaction($line);
            }
        } catch (\Exception $e) {
            // close handle and bubble exception one layer above
            fclose($handle);

            throw new \Exception($e);
        }

        fclose($handle);

        return $this->translator->trans('paypal_import_success', [
            'iSkipped' => $iSkipped,
            'iImported' => $iImported,
            'iOverall' => $iSkipped + $iImported
        ]);
    }

    /**
     * Compares the column names with the configuration from PayPalCsvColumnConfig.php and stores the column index.
     * @see PayPalCsvColumnConfig
     *
     * @param array $line
     */
    private function getColumnIndices(array $line)
    {
        // normalize column names
        array_walk($line, function(&$element) {
            $element = trim(strtolower(str_replace('"', '', $element)));
        });

        foreach ($this->columnConfig as &$config) {
            $config['columnIndex'] = array_search($config['paypalExportColumn'], $line);
        }
    }

    /**
     * Checks if all mandatory columns are available.
     *
     * @throws \Exception if a column is marked as mandatory but could be found within the csv.
     */
    private function checkIfMandatoryColumnsAreAvailable()
    {
        foreach ($this->columnConfig as $config) {
            if ($config['mandatory'] === true && $config['columnIndex'] === false) {
                throw new \Exception($this->translator->trans('paypal_import_error_missing_column', ['columnName' => $config['paypalExportColumn']]));
            }
        }
    }

    /**
     * @param array $record
     *
     * @return bool
     */
    private function transactionAlreadyImported(array $record): bool
    {
        $key = array_search(PayPalCsvColumnConfig::CSV_DATABASE_COMPARE_KEY, array_column($this->columnConfig, 'paypalExportColumn'));
        $config = $this->columnConfig[$key];

        $transaction = $this->repository->findOneBy([$config['associatedEntityColumn'] => $record[$config['columnIndex']]]);

        return !is_null($transaction);
    }

    /**
     * Imports a single csv entry. Calls a corresponding function for formatting the value if it is configured within
     * the config and finally calls the configured setter.
     *
     * @param array $record
     *
     * @throws \Exception
     */
    private function importTransaction(array $record)
    {
        $transaction = new PayPalTransaction();

        foreach ($this->columnConfig as $config) {
            if (false === $config['columnIndex']) {
                continue;
            }

            $value = $record[$config['columnIndex']];
            if (strlen($config['formatCallback']) !== 0) {
                if (method_exists($this, $config['formatCallback'])) {
                    $value = call_user_func([$this, $config['formatCallback']], $record[$config['columnIndex']]);
                }
            }

            $setter = sprintf('set%s', ucfirst($config['associatedEntityColumn']));
            if (method_exists($transaction, $setter)) {
                call_user_func([$transaction, $setter], $value);
            } else {
                throw new \Exception($this->translator->trans('paypal_import_error_unavailable_db_column', ['column' => $config['associatedEntityColumn']]));
            }
        }

        $this->em->persist($transaction);
        $this->em->flush();

        unset($transaction);
    }

    /**
     * @param string $value
     *
     * @return \DateTimeInterface
     *
     * @throws \Exception
     */
    private function parseDate(string $value): \DateTimeInterface
    {
        $parsedValue = \DateTime::createFromFormat('d.m.Y', $value);

        if (false === $parsedValue) {
            throw new \Exception($this->translator->trans('paypal_import_error_wrong_date_format', ['given_date' => $value]));
        }

        return $parsedValue;
    }

    /**
     * @param string $value
     *
     * @return \DateTimeInterface
     *
     * @throws \Exception
     */
    private function parseTime(string $value): \DateTimeInterface
    {
        $parsedValue = \DateTime::createFromFormat('H:i:s', $value);

        if (false === $parsedValue) {
            throw new \Exception($this->translator->trans('paypal_import_error_wrong_time_format', ['given_time' => $value]));
        }

        return $parsedValue;
    }

    /**
     * @param string $value
     *
     * @return int
     */
    private function parseAmount(string $value): int
    {
        $parsedValue = str_replace('.', '', $value);
        $parsedValue = str_replace(',', '.', $parsedValue);

        return intval(floatval($parsedValue) * 100);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function parseGenericString(string $value): string
    {
        if (strlen($value) > PayPalCsvColumnConfig::DB_MAX_STRING_LENGTH) {
            return substr($value, 0, PayPalCsvColumnConfig::DB_MAX_STRING_LENGTH);
        } else {
            return $value;
        }
    }
}