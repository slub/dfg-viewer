<?php

namespace Slub\Dfgviewer\Validation\Common;

use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Error\Notice;
use TYPO3\CMS\Extbase\Error\Result;
use TYPO3\CMS\Extbase\Error\Warning;

trait SeverityTrait {

    private SeverityLevel $severityLevel = SeverityLevel::ERROR;

    public function addSeverityMessage($message, $code) {
        switch ($this->severityLevel) {
            case SeverityLevel::WARNING:
                $this->getResult()->addWarning(new Warning($message, $code));
                break;
            case SeverityLevel::NOTICE:
                $this->getResult()->addNotice(new Notice($message, $code));
                break;
            default:
                $this->getResult()->addError(new Error($message, $code));
        }
    }

    public function severityError(): DomNodeValidator
    {
        $this->severityLevel = SeverityLevel::ERROR;
        return $this;
    }

    public function severityWarning(): DomNodeValidator
    {
        $this->severityLevel = SeverityLevel::WARNING;
        return $this;
    }

    public function severityNotice(): DomNodeValidator
    {
        $this->severityLevel = SeverityLevel::NOTICE;
        return $this;
    }

    abstract protected function getResult(): Result;


}
