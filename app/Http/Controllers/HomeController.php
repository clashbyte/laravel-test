<?php

namespace App\Http\Controllers;

use App\Ticket;
use Auth;
use Storage;

class HomeController extends Controller
{

    /**
     * Основной список обращений
     */
    public function index() {
        $currentUser = Auth::user();
        $tickets = Ticket::forUser($currentUser);
        $users = Ticket::listedUsers($tickets, ['id', 'email', 'name']);

        return view('tickets.list', [
            'tickets' => $tickets,
            'users' => $users,
            'is_manager' => $currentUser->isManager(),
        ]);
    }

    /**
     * Получение файла-вложения для тикета
     * @param int $ticketID ID тикета
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|void
     */
    public function getFile(int $ticketID) {
        $path = Ticket::getAttachmentPath($ticketID);
        if ($path != null) {
            return Storage::download($path);
        }
        return abort(404);
    }

}
