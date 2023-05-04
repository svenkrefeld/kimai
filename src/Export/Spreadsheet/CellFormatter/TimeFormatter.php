<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Export\Spreadsheet\CellFormatter;

use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

final class TimeFormatter implements CellFormatterInterface
{
    public const TIME_FORMAT = 'hh:mm';

    public function setFormattedValue(Worksheet $sheet, int $column, int $row, $value): void
    {
        if (null === $value) {
            $sheet->setCellValue(CellAddress::fromColumnAndRow($column, $row), '');

            return;
        }

        if (!$value instanceof \DateTime) {
            throw new \InvalidArgumentException('Unsupported value given, only DateTime is supported');
        }

        $sheet->setCellValue(CellAddress::fromColumnAndRow($column, $row), Date::PHPToExcel($value));
        $sheet->getStyle(CellAddress::fromColumnAndRow($column, $row))->getNumberFormat()->setFormatCode(self::TIME_FORMAT);
    }
}
