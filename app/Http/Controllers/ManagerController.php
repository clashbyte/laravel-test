<?php


namespace App\Http\Controllers;


use App\Ticket;
use App\User;
use Auth;
use Illuminate\Http\Request;

class ManagerController extends Controller
{

    /**
     * Форма ответа на тикет
     * @param Request $request Данные запроса
     * @param int $ticketID ID тикета
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function replyForm(Request $request, int $ticketID) {
        $ticket = Ticket::findForReply(Auth::user(), $ticketID);
        return view('tickets.reply', [
            'ticket' => $ticket,
            'user' => User::findOrFail($ticket->user, ['id', 'name', 'email'])
        ]);
    }

    /**
     * Сохранение ответа на тикет
     * @param Request $request Данные запроса
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveReply(Request $request) {
        if (!$request->filled(['ticket', 'reply'])) {
            \Session::flash('status', 'Форма недозаполнена');
            return redirect('home');
        }

        $user = Auth::user();
        $ticket = Ticket::findForReply($user, $request->get('ticket'));
        $ticket->addReply($user, $request->get('reply'));

        \Session::flash('status', 'Ответ сохранён');
        return redirect('home');
    }


}
