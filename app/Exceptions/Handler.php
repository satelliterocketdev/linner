<?php

namespace App\Exceptions;

use App\Log\LinnerWriter;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * カスタムレポート用の、出力対象外クラス一覧。
     *
     * @var array
     */
    private $dontCustomReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
        try {
            $this->customReport($exception);
        } catch (Exception $e) {
            Log::info("customReport Error");
        }
    }

    /**
     * カスタムレポート。
     * ログ以外出力したい処理を実装。
     *
     * @param \Exception $exception
     * @return void
     * @throws Exception
     */
    public function customReport(Exception $exception)
    {
        $shouldntCustomReport = !is_null(collect($this->dontCustomReport)->first(function ($type) use ($exception) {
            return $exception instanceof $type;
        }));
        if ($shouldntCustomReport) {
            // customReport出力対象外の場合は処理を抜ける。
            return;
        }

        try {
            /** @var LinnerWriter $logger */
            $logger = $this->container->make(LoggerInterface::class);
        } catch (Exception $ex) {
            throw $exception; // throw the original exception
        }

        $logger->sendLogMessage($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof LineApiException) {
            return response()->json(['code' => $exception->getStatusCode(), 'message' => $exception->getLineMessage()])->setStatusCode($exception->getCode());
        }

        if (get_class($exception) == 'Illuminate\Auth\AuthenticationException') {
            return parent::render($request, $exception);
        }

        if ($this->isHttpException($exception)) {
            if ($exception->getStatusCode() == 403) {
                return response()->view('errors.403')->setStatusCode(400);
            }

            if ($exception->getStatusCode() == 404) {
                return response()->view('errors.404')->setStatusCode(404);
            }

        }

        // 500
        return response()->view('errors.500')->setStatusCode(500);
//        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
}
