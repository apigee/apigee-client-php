<?php

namespace Apigee\Edge\Exception;

use Http\Message\Formatter;
use Http\Message\Formatter\FullHttpMessageFormatter;
use Psr\Http\Message\RequestInterface;

/**
 * Class ApiException.
 *
 * General exception class for API communication errors.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class ApiException extends \RuntimeException
{
    /** @var \Psr\Http\Message\RequestInterface */
    protected $request;

    /** @var \Http\Message\Formatter */
    protected $formatter;
    /**
     * @var null|\Throwable
     */
    private $previous;

    /**
     * ApiException constructor.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     * @param \Http\Message\Formatter|null $formatter
     */
    public function __construct(
        RequestInterface $request,
        string $message = '',
        int $code = 0,
        \Throwable $previous = null,
        Formatter $formatter = null
    ) {
        $this->request = $request;
        $this->previous = $previous;
        $this->formatter = $formatter ?: new FullHttpMessageFormatter();
        parent::__construct($message, $code, $previous);
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        $output = sprintf(
            "Request:\n%s\n",
            $this->formatter->formatRequest($this->request)
        );

        if ($this->previous) {
            $output .= sprintf("%s\n", $this->previous->getMessage());
        }

        return $output;
    }

    /**
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @return \Http\Message\Formatter
     */
    public function getFormatter(): Formatter
    {
        return $this->formatter;
    }
}
