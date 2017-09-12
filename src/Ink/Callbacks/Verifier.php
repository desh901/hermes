<?php

namespace Hermes\Ink\Callbacks;

use Hermes\Ink\Contracts\Context;
use Symfony\Component\HttpFoundation\Request;
use Hermes\Core\Exceptions\CallbackVerificationFailed;
use Hermes\Ink\Contracts\Callbacks\Verifier as VerifierContract;

abstract class Verifier implements VerifierContract
{

    /**
     * Instance of the SDK context
     *
     * @var Context
     */
    protected $context;

    /**
     * Verifier constructor.
     *
     * @param Context $context
     */
    public function __construct(Context $context)
    {

        $this->context = $context;

    }

    /**
     * Check if the received request does not pass the verification process
     *
     * @param Request $request
     * @return bool
     */
    public function fails(Request $request)
    {
        return !$this->passes($request);
    }

    /**
     * Verify the callback, if the verification fails throw an exception
     *
     * @param $request
     * @return bool
     * @throws CallbackVerificationFailed
     */
    public function verify(Request $request)
    {

        if($this->context->verifyCallbacks() && $this->fails($request)) {

            throw new CallbackVerificationFailed(
                'Callback verification failed for callback: ' . $request->getContent()
                , $request);

        }

        return true;
    }

}