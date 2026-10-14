<?php

namespace Slub\Dfgviewer\Log\Writer;

use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogRecord;
use TYPO3\CMS\Core\Log\Writer\FileWriter;
use TYPO3\CMS\Core\Log\Writer\WriterInterface;

/**
 * A log writer that writes log records to a file only
 * if the log level matches the configured level.
 *
 * Example usage:
 * $GLOBALS['TYPO3_CONF_VARS']['LOG']['writerConfiguration'] = [
 *     LogLevel::INFO => [
 *         ExactLevelFileWriter::class => [
 *             'logFileInfix' => 'infos',
 *             'level' => LogLevel::INFO,
 *         ],
 *     ]
 * ];
 */
final class ExactLevelFileWriter extends FileWriter
{

    /**
     * The log level for this writer.
     *
     * @var string
     */
    private string $level = '';

    /**
     * Sets the log level for this writer.
     *
     * @access public
     *
     * @param string $level The log level to set (e.g., 'info', 'error').
     */
    public function setLevel(string $level): void
    {
        $this->level = LogLevel::getInternalName(LogLevel::normalizeLevel($level));
    }

    /**
     * Writes a log record to the file if the log level matches the configured level.
     *
     * @access public
     *
     * @param LogRecord $record The log record to write.
     *
     * @return WriterInterface|ExactLevelFileWriter The writer instance.
     */
    public function writeLog(LogRecord $record): WriterInterface|ExactLevelFileWriter
    {
        if ($record->getLevel() !== $this->level) {
            return $this;
        }

        return parent::writeLog($record);
    }
}
