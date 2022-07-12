<?php

namespace Nubank\Authorizer\Application;


abstract class Violations
{
    const AccountAlreadyInitialized = 'account-already-initialized';
    const AccountNotInitialized = 'account-not-initialized';
    const CardNotActive = 'card-not-active';
    const InsufficientLimit = 'insufficient-limit';
    const HighFrequencySmallInterval = 'high-frequency-small-interval';
    const DoubledTransaction = 'doubled-transaction';
}
