<?php

return [
    'modelNotFound' => 'Auth model not found. Please check AuthConfig file.',
    'findUserMethodNotFound' => 'Auth model does not have the findUser method. Please check AuthConfig file.',
    'passwordFieldNotFound' => 'The {0} field does not exist in the User object. Please check AuthConfig file.',
    'passwordFieldNotSet' => '$passwordField is not set. Please check AuthConfig file.',
    'sessionAuthCheckKeyInvalid' => '$sessionAuthCheckKey is not set or does not exist in $sessionData. Please check AuthConfig file.',
    'roleKeyInvalid' => '$roleKey is not set or does not exist in $sessionData. Please check AuthConfig file.',
    'logoutFlashMessage' => 'Logout has been successfully',
    'notAuthenticatedFlashMessage' => 'You must be logged in to access this page',
    'notAuthorizedFlashMessage' => 'You do not have permission to access this page',
];
