<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RedirectController extends AbstractController
{
    #[Route('/', name: 'redirect_to_api_doc')]
    public function redirectToApiDoc(): RedirectResponse
    {
        return $this->redirect('/api/doc');  // Redirect to /api/doc
    }
}
