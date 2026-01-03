<?php

declare(strict_types=1);

namespace App\Application\Constants;

class Messages
{
    // User Validation
    public const NAME_REQUIRED = 'El Nombre es requerido';
    public const NAME_MIN_LENGTH = 'El Nombre debe tener al menos 2 caracteres';
    public const NAME_MAX_LENGTH = 'El Nombre no puede tener más de 50 caracteres';
    
    public const LASTNAME_REQUIRED = 'El Apellido es requerido';
    public const LASTNAME_MIN_LENGTH = 'El Apellido debe tener al menos 2 caracteres';
    public const LASTNAME_MAX_LENGTH = 'El Apellido no puede tener más de 50 caracteres';
    
    public const EMAIL_REQUIRED = 'El Correo electrónico es requerido';
    public const EMAIL_INVALID = 'El Correo electrónico no es válido';
    public const GENERIC_ERROR = 'Se ha presentado un error al procesar su solicitud.';
    
    public const PASSWORD_REQUIRED = 'La Contraseña es requerida';
    public const PASSWORD_MIN_LENGTH = 'La Contraseña debe tener al menos 6 caracteres';
    
    // Fallbacks
    public const FIELD_REQUIRED = 'es requerido';
    public const FIELD_NOT_EMPTY = 'no puede estar vacío';

    // Actions
    public const USER_DELETED = 'Usuario eliminado exitosamente.';
    public const USER_NOT_FOUND = 'El usuario solicitado no existe.';
    public const USER_UPDATED = 'Usuario actualizado exitosamente.';
}
