<?php

namespace OANNA\Onfido\Enums;

enum Status: string
{
    case VERIFIED = 'verified';
    case NOT_VERIFIED = 'not_verified';
    case STARTED = 'started';
    case WAITING = 'waiting';
    case UNDEFINED = 'undefined';
}
