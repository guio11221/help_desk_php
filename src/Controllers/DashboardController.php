<?php

declare(strict_types=1);

final class DashboardController extends BaseController
{
    public function index(): void
    {
        require_auth();

        $repo = new TicketRepository($this->db);
        $user = current_user();

        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'extraCss' => ['assets/css/dashboard.css'],
            'stats' => $repo->statsForUser($user),
            'recent' => array_slice($repo->listForUser($user), 0, 8),
        ]);
    }
}
