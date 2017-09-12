<?php

namespace Hermes\Ink\Contracts\Callbacks;


use Hermes\Core\Exceptions\CallbackVerificationFailed;
use Symfony\Component\HttpFoundation\Request;

interface Verifier
{

    /**
     * Check if the received request passes the verification process
     *
     * @param Request $request
     * @return bool
     */
    public function passes(Request $request);

    /**
     * Check if the received request does not pass the verification process
     *
     * @param Request $request
     * @return bool
     */
    public function fails(Request $request);

    /**
     * Verify the callback, if the verification fails throw an exception
     *
     * @param $request
     * @return bool
     * @throws CallbackVerificationFailed
     */
    public function verify(Request $request);

}