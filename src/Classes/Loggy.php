<?php

namespace Src\Classes;

use DateTime;

use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;
use \Monolog\Handler\FirePHPHandler;
use Monolog\Formatter\JsonFormatter;
use Src\TableGateways\UserGateway;
use Src\TableGateways\UserLoginGateway;
use stdClass;

class Loggy
{
    #region Private Props
    private const LoggyName = 'Loggy';
    private Logger $logger;

    #endregion

    #region Construct
    function __construct()
    {
        $this->logger = new \Monolog\Logger($this::LoggyName);
        $this->logger->pushProcessor(new \Monolog\Processor\UidProcessor());
        $formatter = new JsonFormatter();
        $stream = new StreamHandler($_SERVER['DOCUMENT_ROOT'] . '/logs/app.log', \Monolog\Level::Debug);
        $stream->setFormatter($formatter);
        $this->logger->pushHandler($stream);
        // $this->logger->pushHandler(new StreamHandler(
        //     'php://stdout',
        //     $level = \Monolog\Level::Debug,
        //     $bubble = true
        // ));
        $this->logger->pushHandler(new FirePHPHandler());
    }
    #endregion

    #region Public Props
    #endregion

    #region Private func
    #endregion

    #region Public func
    private function initValues(string $stacktrace, $exception)
    {
        global $dbConnection;
        $userLogin = $GLOBALS["userGateway"]??new UserGateway($dbConnection);
        $userLogin->checkLogin();
        $user = $userLogin->GetUser();
        //var_dump($userLogin);
        $u = array(
            'id'        =>  $user->id,
            'Name'      =>  $user->full_name,
            'sessionId' =>  session_id()
        );
        $retval  = array(
            'Date'          => (new DateTime())->format("Y-m-d"),
            'Time'          => (new DateTime())->format("H:i:s"),
            'User'          =>  $u,
            'Stack Trace'   =>  $stacktrace,
            'exception'     =>  $exception,
            'Server'        =>  $_SERVER,
            'Session'       =>  $_SESSION

        );
        return ($retval);
    }
    // public function Debug(string $msg, string $stacktrace = "")
    // {
    //     $this->log(\Monolog\Level::Debug, $msg, $stacktrace);
    // }
    public function Info(string $msg, string $stacktrace = "")
    {
        $this->log(\Monolog\Level::Info, $msg, $stacktrace);
    }
    // public function Notice(string $msg, string $stacktrace = "")
    // {
    //     $this->log(\Monolog\Level::Notice, $msg, $stacktrace);
    // }
    // public function Warning(string $msg, string $stacktrace = "")
    // {
    //     $this->log(\Monolog\Level::Warning, $msg, $stacktrace);
    // }
    // public function Error(string $msg, string $stacktrace = "", $exception = null)
    // {
    //     $this->log(\Monolog\Level::Error, $msg, $stacktrace, $exception);
    // }
    // public function Critical(string $msg, string $stacktrace = "")
    // {
    //     $this->log(\Monolog\Level::Critical, $msg, $stacktrace);
    // }
    // public function Alert(string $msg, string $stacktrace)
    // {
    //     $this->log(\Monolog\Level::Alert, $msg, $stacktrace);
    // }
    // public function Emergency(string $msg, string $stacktrace)
    // {
    //     $this->log(\Monolog\Level::Emergency, $msg, $stacktrace);
    // }
    
    public function logy(string $msg, string $stacktrace, $e)
    {
        if (gettype($e) == "array") {
            $exception = $e;
            if (isset($exception) && isset($exception['level'])) {
                $type = isset($exception['type']) ? $exception['type'] : $exception['level'];
                switch ($type) {
                    case E_ERROR: // 1 //
                        return $this->log(\Monolog\Level::Error, $msg, $stacktrace, $exception);
                    case E_WARNING: // 2 //
                        return $this->log(\Monolog\Level::Warning, $msg, $stacktrace, $exception);
                    case E_PARSE: // 4 //
                        return $this->log(\Monolog\Level::Info, $msg, $stacktrace, $exception);
                    case E_NOTICE: // 8 //
                        return $this->log(\Monolog\Level::Notice, $msg, $stacktrace, $exception);
                    case E_CORE_ERROR: // 16 //
                        return $this->log(\Monolog\Level::Error, $msg, $stacktrace, $exception);
                    case E_CORE_WARNING: // 32 //
                        return $this->log(\Monolog\Level::Warning, $msg, $stacktrace, $exception);
                    case E_COMPILE_ERROR: // 64 //
                        return $this->log(\Monolog\Level::Error, $msg, $stacktrace, $exception);
                    case E_COMPILE_WARNING: // 128 //
                        return $this->log(\Monolog\Level::Warning, $msg, $stacktrace, $exception);
                    case E_USER_ERROR: // 256 //
                        return $this->log(\Monolog\Level::Error, $msg, $stacktrace, $exception);
                    case E_USER_WARNING: // 512 //
                        return $this->log(\Monolog\Level::Warning, $msg, $stacktrace, $exception);
                    case E_USER_NOTICE: // 1024 //
                        return $this->log(\Monolog\Level::Notice, $msg, $stacktrace, $exception);
                    case E_STRICT: // 2048 //
                        return $this->log(\Monolog\Level::Error, $msg, $stacktrace, $exception);
                    case E_RECOVERABLE_ERROR: // 4096 //
                        return $this->log(\Monolog\Level::Error, $msg, $stacktrace, $exception);
                    case E_DEPRECATED: // 8192 //
                        return $this->log(\Monolog\Level::Notice, $msg, $stacktrace, $exception);
                    case E_USER_DEPRECATED: // 16384 //
                        return $this->log(\Monolog\Level::Notice, $msg, $stacktrace, $exception);
                }
            }
        } else if (is_a($e, "Exception")) {
            $this->log(\Monolog\Level::Error, $msg, $stacktrace, $e);
        }
        else
        {
            $this->log(\Monolog\Level::Error, $msg, $stacktrace, $e);
        }
    }
    private function log(\Monolog\Level $level, string $msg, string $stacktrace, $exception = null)
    {
        $var =  $this->initValues($stacktrace, $exception);
        switch ($level) {
            case \Monolog\Level::Debug:
                $this->logger->debug("$msg", $var);
                break;
            case \Monolog\Level::Info:
                $this->logger->info("$msg", $var);
                break;
            case \Monolog\Level::Notice:
                $this->logger->notice("$msg", $var);
                break;
            case \Monolog\Level::Warning:
                $this->logger->warning("$msg", $var);
                break;
            case \Monolog\Level::Error:
                $this->logger->error("$msg", $var);
                break;
            case \Monolog\Level::Critical:
                $this->logger->critical("$msg", $var);
                break;
            case \Monolog\Level::Alert:
                $this->logger->alert("$msg", $var);
                break;
            case \Monolog\Level::Emergency:
                $this->logger->emergency("$msg", $var);
                break;
        }
    }
    #endregion

}
