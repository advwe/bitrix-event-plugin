<?php

namespace Adv\BitrixEventsPlugin;

use Bitrix\Main\Application;
use Bitrix\Main\SystemException;
use Composer\IO\IOInterface;
use RuntimeException;
use Throwable;

/**
 * Class BitrixCoreFinder
 *
 * @package Adv\BitrixEventsPlugin
 */
final class BitrixCoreFinder
{
    /**
     * @var Application
     */
    private $application;
    /**
     * @var IOInterface $io
     */
    private $io;
    private $prologPath = '/bitrix/modules/main/include/prolog_before.php';
    private $defaults = [
        '.',
        '../..',
        'web',
        'common',
    ];

    /**
     * @return Application
     *
     * @throws BitrixException
     */
    public function getApplication(): Application
    {
        if (!$this->application) {
            $this->includeBitrix();
        }

        return $this->application;
    }

    /**
     * @throws BitrixException
     */
    public function setApplication()
    {
        if (!$this->application) {
            $this->includeBitrix();
        }
    }

    /**
     * @throws BitrixException
     */
    private function includeBitrix()
    {
        try {
            $this->application = Application::getInstance();
        } catch (Throwable $e) {
            try {
                $this->includeBitrixFromDocumentRoot($this->findBitrixCorePath());
            } catch (Throwable $e) {
                throw new BitrixException('Wrong document root or bitrix is not found.');
            }
        }
    }

    /**
     * @param string $documentRoot
     *
     * @throws SystemException
     */
    private function includeBitrixFromDocumentRoot(string $documentRoot)
    {
        \define('NO_KEEP_STATISTIC', 'Y');
        \define('NOT_CHECK_PERMISSIONS', true);
        \define('PUBLIC_AJAX_MODE', true);
        \define('CHK_EVENT', false);
        \define('BX_WITH_ON_AFTER_EPILOG', false);
        \define('BX_NO_ACCELERATOR_RESET', true);

        $_SERVER['DOCUMENT_ROOT'] = $GLOBALS['DOCUMENT_ROOT'] = \sprintf(
            '%s/%s/',
            \getcwd(),
            \preg_replace('~(bitrix|local)\?~', '', $documentRoot)
        );

        /** @noinspection PhpIncludeInspection */
        require_once \sprintf('%s%s', $documentRoot, $this->prologPath);

        $this->application = Application::getInstance();
    }

    /**
     * @return string
     *
     * @throws RuntimeException
     * @throws BitrixException
     */
    private function findBitrixCorePath(): string
    {
        foreach ($this->defaults as $path) {
            if ($this->tryPath($this->normalizePath($path))) {
                return $path;
            }
        }

        while (true) {
            $path =
                \sprintf(
                    '/%s/%s',
                    \trim(
                        $this->io->ask("We cant find bitrix in your project. Write you`r absolute document root path or press Enter to skip.\n"),
                        " \t\n\r\0\x0B/"
                    ),
                    $this->prologPath
                );

            if (!$path) {
                break;
            }

            if ($this->tryPath($this->normalizePath($path))) {
                return $path;
            }
        }

        throw new BitrixException('Wrong document root or bitrix is not found.');
    }

    /**
     * @param $path
     *
     * @return bool
     */
    private function tryPath(string $path): bool
    {
        return \file_exists($path);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function normalizePath(string $path): string
    {
        return \realpath(\sprintf('%s%s%s%s%s', \getcwd(), DIRECTORY_SEPARATOR, $path, DIRECTORY_SEPARATOR, $this->prologPath)) ?: '';
    }

    /**
     * Add a custom path to default pathes
     *
     * @param string $path
     */
    public function unshiftDefaultPath(string $path)
    {
        \array_unshift($this->defaults, \rtrim($path));
    }

    /**
     * @param IOInterface $io
     */
    public function setIo(IOInterface $io)
    {
        $this->io = $io;
    }
}
