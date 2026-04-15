<?php

declare(strict_types=1);

$router->get('/', [AuthController::class, 'showLogin']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/panel/profile', [PanelController::class, 'profile']);
$router->post('/panel/profile', [PanelController::class, 'updateProfile']);
$router->post('/panel/profile/password', [PanelController::class, 'updatePassword']);
$router->post('/panel/profile/avatar', [PanelController::class, 'updateAvatar']);
$router->get('/panel/settings', [PanelController::class, 'settings']);
$router->get('/panel/theme', [PanelController::class, 'theme']);
$router->post('/panel/theme', [PanelController::class, 'updateTheme']);
$router->get('/media/avatar', [MediaController::class, 'avatar']);

$router->get('/tickets', [TicketController::class, 'index']);
$router->get('/tickets/create', [TicketController::class, 'create']);
$router->post('/tickets/store', [TicketController::class, 'store']);
$router->get('/tickets/show', [TicketController::class, 'show']);
$router->post('/tickets/comment', [TicketController::class, 'comment']);
$router->post('/tickets/status', [TicketController::class, 'status']);
