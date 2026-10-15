<?php

namespace App\Exceptions;

use DomainException;

/**
 * Yon règ biznis pa respekte (stock vid, move estati, dire envalid...).
 * Mesaj la fèt pou montre itilizatè a tèl quel.
 */
class FleetException extends DomainException
{
}
