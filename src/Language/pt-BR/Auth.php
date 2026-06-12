<?php

return [
    'modelNotFound' => 'Auth model não encontrado. Verifique o arquivo AuthConfig.',
    'findUserMethodNotFound' => 'Auth model não possui o método findUser. Verifique o arquivo AuthConfig.',
    'passwordFieldNotFound' => 'O campo {0} não existe no objeto User. Verifique o arquivo AuthConfig.',
    'passwordFieldNotSet' => '$passwordField não está definido. Verifique o arquivo AuthConfig.',
    'sessionAuthCheckKeyInvalid' => '$sessionAuthCheckKey não está definido ou não existe em $sessionData. Verifique o arquivo AuthConfig.',
    'roleKeyInvalid' => '$roleKey não está definido ou não existe em $sessionData. Verifique o arquivo AuthConfig.',
    'logoutFlashMessage' => 'Logout efetuado com sucesso',
    'notAuthenticatedFlashMessage' => 'Você deve estar autenticado para acessar essa página',
    'notAuthorizedFlashMessage' => 'Você não tem permissão para acessar essa página',
];
