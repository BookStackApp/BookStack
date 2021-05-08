<?php namespace BookStack\Exceptions;

use Exception;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;

class PrettyException extends Exception implements Responsable
{
    /**
     * @var ?string
     */
    protected $subtitle = null;

    /**
     * @var ?string
     */
    protected $details = null;

    /**
     * Render a response for when this exception occurs.
     * @param Request $request
     */
    public function toResponse($request)
    {
        $code = ($this->getCode() === 0) ? 500 : $this->getCode();
        return response()->view('errors.' . $code, [
            'message' => $this->getMessage(),
            'subtitle' => $this->subtitle,
            'details' => $this->details,
        ], $code);
    }

    public function setSubtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;
        return $this;
    }

    public function setDetails(string $details): self
    {
        $this->details = $details;
        return $this;
    }
}
